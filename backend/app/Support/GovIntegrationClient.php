<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

/**
 * Government ICT integration wrapper.
 *
 * Enforces:
 * - outbound host allowlist
 * - standard headers (incl. X-Request-Id)
 * - default timeout
 *
 * Use this instead of calling Http:: directly from controllers.
 */
final class GovIntegrationClient
{
    public static function forBaseUrl(string $baseUrl): PendingRequest
    {
        $baseUrl = trim($baseUrl);
        if ($baseUrl === '') {
            throw new InvalidArgumentException('Integration baseUrl is empty.');
        }

        $host = (string) (parse_url($baseUrl, PHP_URL_HOST) ?? '');
        if ($host === '') {
            throw new InvalidArgumentException('Integration baseUrl host is invalid.');
        }

        $allowed = config('integrations.allowlist_hosts', []);
        $hostLower = strtolower($host);
        if (! in_array($hostLower, $allowed, true)) {
            throw new InvalidArgumentException("Outbound host not allowlisted: {$hostLower}");
        }

        $timeout = (int) config('integrations.http.timeout_seconds', 10);
        $requestId = (string) request()?->headers->get('X-Request-Id', '');

        return Http::baseUrl($baseUrl)
            ->timeout(max(1, $timeout))
            ->acceptJson()
            ->withHeaders(array_filter([
                'X-Request-Id' => $requestId !== '' ? $requestId : null,
            ]));
    }
}

