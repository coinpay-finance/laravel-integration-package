<?php

namespace Coinpay\Finance\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

/**
 * Verifies that an incoming CoinPay webhook request is authentic before it
 * reaches the FormRequest/controller.
 *
 * This runs as route middleware (not inside WebhookRequest::authorize())
 * because it needs the RAW, unparsed request body. Anything that decodes
 * and re-encodes JSON (Laravel's validation pipeline, FormRequest, etc.)
 * can change the bytes and break signature verification, so the check
 * has to happen before that machinery touches the request.
 *
 * Two schemes are supported:
 *
 *  - Current (HMAC-SHA256): `X-Coinpay-Signature: v1=<hex-hmac-sha256>`,
 *    `X-Coinpay-Timestamp: <unix-seconds>`, `X-Coinpay-Delivery: <uuid>`.
 *    The digest covers "{timestamp}.{deliveryId}.{rawBody}". Requests
 *    older/newer than the configured tolerance, or whose delivery id has
 *    already been seen within that window, are rejected.
 *
 *  - Legacy (deprecated): a static `SECRET` header compared with
 *    hash_equals() against the configured webhook secret. No payload
 *    binding, no replay protection. Kept only as a fallback for merchants
 *    who haven't set up signature verification yet.
 *
 * If HMAC headers are present, HMAC is the sole path evaluated (falling
 * back to legacy purely because HMAC headers were malformed would let an
 * attacker downgrade the scheme). Legacy is only attempted when none of
 * the HMAC headers are present at all.
 */
class VerifyCoinPayWebhookSignature
{
    protected const DEFAULT_TOLERANCE_SECONDS = 300;

    public function handle(Request $request, Closure $next)
    {
        if ($this->hasHmacHeaders($request)) {
            if (! $this->verifyHmacSignature($request)) {
                return $this->reject('Invalid webhook signature.');
            }

            return $next($request);
        }

        if (! $this->verifyLegacySecret($request)) {
            return $this->reject('Invalid webhook secret.');
        }

        return $next($request);
    }

    protected function hasHmacHeaders(Request $request): bool
    {
        return $request->hasHeader('X-Coinpay-Signature')
            && $request->hasHeader('X-Coinpay-Timestamp')
            && $request->hasHeader('X-Coinpay-Delivery');
    }

    protected function verifyHmacSignature(Request $request): bool
    {
        $secret = (string) config('coinpay.webhook_secret');

        if ($secret === '') {
            return false;
        }

        $signatureHeader = (string) $request->header('X-Coinpay-Signature');
        $timestamp = (string) $request->header('X-Coinpay-Timestamp');
        $deliveryId = (string) $request->header('X-Coinpay-Delivery');

        if ($signatureHeader === '' || $timestamp === '' || $deliveryId === '') {
            return false;
        }

        if (! ctype_digit($timestamp)) {
            return false;
        }

        $tolerance = (int) config('coinpay.webhook_signature_tolerance', self::DEFAULT_TOLERANCE_SECONDS);

        if (abs(time() - (int) $timestamp) > $tolerance) {
            return false;
        }

        if (! str_starts_with($signatureHeader, 'v1=')) {
            return false;
        }

        $providedDigest = substr($signatureHeader, strlen('v1='));

        // Raw body, read BEFORE any JSON decode/validation touches it.
        $rawBody = $request->getContent();

        $expectedDigest = hash_hmac('sha256', $timestamp . '.' . $deliveryId . '.' . $rawBody, $secret);

        if ($providedDigest === '' || ! hash_equals($expectedDigest, $providedDigest)) {
            return false;
        }

        return $this->rememberDelivery($deliveryId, $tolerance);
    }

    /**
     * Reject an exact-duplicate delivery id seen within the tolerance
     * window. Cache::add() is atomic, so this doubles as the replay guard.
     */
    protected function rememberDelivery(string $deliveryId, int $tolerance): bool
    {
        try {
            return Cache::add('coinpay:webhook:delivery:' . $deliveryId, true, $tolerance);
        } catch (Throwable $e) {
            // Don't let a broken/unconfigured cache store turn into a hard
            // rejection of an otherwise validly-signed webhook — HMAC +
            // timestamp are the primary guarantee, replay dedup is
            // defense-in-depth on top of that.
            return true;
        }
    }

    protected function verifyLegacySecret(Request $request): bool
    {
        $configured = (string) config('coinpay.webhook_secret');
        $provided = (string) $request->header('SECRET');

        if ($configured === '' || $provided === '') {
            return false;
        }

        return hash_equals($configured, $provided);
    }

    protected function reject(string $message)
    {
        return response()->json([
            'is_success' => false,
            'message' => $message,
        ], SymfonyResponse::HTTP_UNAUTHORIZED);
    }
}
