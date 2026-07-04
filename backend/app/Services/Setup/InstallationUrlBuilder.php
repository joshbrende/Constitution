<?php

namespace App\Services\Setup;

use Illuminate\Http\Request;

class InstallationUrlBuilder
{
    /**
     * @return array{protocol: string, domain: string, directory: string}
     */
    public static function parse(?string $url, ?string $fallbackHost = null, bool $fallbackSecure = false): array
    {
        $fallbackHost ??= 'localhost';
        $defaults = [
            'protocol' => $fallbackSecure ? 'https' : 'http',
            'domain' => $fallbackHost,
            'directory' => '',
        ];

        if (! is_string($url) || trim($url) === '') {
            return $defaults;
        }

        $parts = parse_url(trim($url));
        if ($parts === false || empty($parts['host'])) {
            return $defaults;
        }

        $path = trim((string) ($parts['path'] ?? ''), '/');
        if ($path === '' || $path === 'setup') {
            $directory = '';
        } else {
            $directory = $path;
        }

        return [
            'protocol' => ($parts['scheme'] ?? $defaults['protocol']) === 'http' ? 'http' : 'https',
            'domain' => (string) $parts['host'],
            'directory' => $directory,
        ];
    }

    public static function build(string $protocol, string $domain, ?string $directory): string
    {
        $protocol = $protocol === 'http' ? 'http' : 'https';
        $domain = strtolower(trim($domain));
        $domain = rtrim($domain, '/');

        if ($domain === '') {
            throw new \InvalidArgumentException('Domain is required.');
        }

        $directory = trim((string) $directory, '/');
        if ($directory !== '' && (str_contains($directory, '..') || str_contains($directory, '://'))) {
            throw new \InvalidArgumentException('Invalid installation directory.');
        }

        $url = $protocol.'://'.$domain;
        if ($directory !== '') {
            $url .= '/'.$directory;
        }

        return $url;
    }

    /**
     * @return list<string>
     */
    public static function domainOptions(?string $savedUrl, ?Request $request = null): array
    {
        $hosts = [];

        if ($request) {
            $hosts[] = $request->getHost();
        }

        $appUrl = (string) config('app.url', '');
        if ($appUrl !== '') {
            $parsed = parse_url($appUrl);
            if (! empty($parsed['host'])) {
                $hosts[] = (string) $parsed['host'];
            }
        }

        if (is_string($savedUrl) && $savedUrl !== '') {
            $parsed = parse_url($savedUrl);
            if (! empty($parsed['host'])) {
                $hosts[] = (string) $parsed['host'];
            }
        }

        $hosts = array_values(array_unique(array_filter($hosts)));

        return $hosts !== [] ? $hosts : ['localhost'];
    }
}
