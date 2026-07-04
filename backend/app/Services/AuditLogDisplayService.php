<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\CertificateApplication;
use App\Models\User;
use Illuminate\Support\Str;

class AuditLogDisplayService
{
    /**
     * @return array{
     *     category: string,
     *     category_label: string,
     *     action_label: string,
     *     severity: string,
     *     actor_label: string,
     *     actor_hint: ?string,
     *     target_label: ?string,
     *     target_url: ?string,
     *     summary: string,
     *     details: list<array{label: string, value: string}>,
     *     show_ip: bool
     * }
     */
    public function present(AuditLog $log): array
    {
        $meta = is_array($log->metadata) ? $log->metadata : [];
        $category = $this->categoryForAction($log->action);
        $categoryLabels = config('audit.category_labels', []);

        return [
            'category' => $category,
            'category_label' => $categoryLabels[$category] ?? ucfirst($category),
            'action_label' => $this->actionLabel($log->action),
            'severity' => $this->severityForAction($log->action),
            'actor_label' => $this->actorLabel($log, $meta),
            'actor_hint' => $this->actorHint($log, $meta),
            'target_label' => $this->targetLabel($log),
            'target_url' => $this->targetUrl($log),
            'summary' => $this->summary($log, $meta),
            'details' => $this->details($log, $meta),
            'show_ip' => $this->showIp($log->action),
        ];
    }

    public function categoryForAction(string $action): string
    {
        foreach (config('audit.categories', []) as $key => $prefix) {
            if (str_starts_with($action, (string) $prefix)) {
                return $key;
            }
        }

        return 'other';
    }

    public function actionLabel(string $action): string
    {
        $labels = config('audit.action_labels', []);

        if (isset($labels[$action])) {
            return $labels[$action];
        }

        return Str::headline(str_replace(['.', '_'], ' ', $action));
    }

    private function severityForAction(string $action): string
    {
        if (str_contains($action, 'failed')
            || str_contains($action, 'revoked')
            || str_contains($action, 'suspended')
            || str_contains($action, 'rate_limited')
            || str_contains($action, 'pii_viewed')
        ) {
            return 'warning';
        }

        if (str_starts_with($action, 'auth.')) {
            return 'security';
        }

        return 'info';
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function actorLabel(AuditLog $log, array $meta): string
    {
        if ($log->actor) {
            $name = trim(($log->actor->name ?? '').' '.($log->actor->surname ?? ''));

            return $name !== '' ? $name : (string) ($log->actor->email ?? 'User #'.$log->actor_user_id);
        }

        if (! empty($meta['email']) && is_string($meta['email'])) {
            return $meta['email'];
        }

        if (str_contains($log->action, 'failed') || str_contains($log->action, 'rate_limited')) {
            return 'Unknown / unauthenticated';
        }

        return 'System';
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function actorHint(AuditLog $log, array $meta): ?string
    {
        if ($log->actor && ! empty($log->actor->email)) {
            return (string) $log->actor->email;
        }

        if (! empty($meta['email']) && is_string($meta['email']) && ! $log->actor) {
            return 'No authenticated session recorded';
        }

        if ($log->actor_user_id) {
            return 'User #'.$log->actor_user_id;
        }

        return null;
    }

    private function targetLabel(AuditLog $log): ?string
    {
        if (! $log->target_type) {
            return null;
        }

        $type = class_basename($log->target_type);

        if (! $log->target_id) {
            return match ($type) {
                'RefreshToken' => 'Refresh token',
                'AuditLog' => 'Audit log listing',
                default => null,
            };
        }

        return $this->targetTypeLabel($type).' #'.$log->target_id;
    }

    private function targetTypeLabel(string $type): string
    {
        return match ($type) {
            'User' => 'User account',
            'Certificate' => 'Certificate',
            'CertificateApplication' => 'Certificate application',
            'RefreshToken' => 'Refresh token',
            'AuditLog' => 'Audit log',
            'Course' => 'Course',
            default => Str::headline($type),
        };
    }

    private function targetUrl(AuditLog $log): ?string
    {
        if (! $log->target_id) {
            return null;
        }

        return match ($log->target_type) {
            User::class => route('admin.users.edit', ['user' => $log->target_id]),
            Certificate::class => route('admin.certificates.index'),
            CertificateApplication::class => route('admin.certificate-applications.show', ['application' => $log->target_id]),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function summary(AuditLog $log, array $meta): string
    {
        return match ($log->action) {
            'auth.api.registered', 'auth.web.registered' => 'New account registered'
                . (isset($meta['email']) ? ' · '.(string) $meta['email'] : ''),
            'auth.api.logged_in', 'auth.web.logged_in' => 'Successful sign-in'
                . (isset($meta['email']) ? ' · '.(string) $meta['email'] : ''),
            'auth.api.login_failed', 'auth.web.login_failed' => 'Failed sign-in attempt'
                . (isset($meta['email']) ? ' · '.(string) $meta['email'] : ''),
            'auth.api.refresh_failed' => 'Invalid or expired refresh token',
            'auth.api.refresh_succeeded' => 'Session renewed via refresh token',
            'audit_logs.viewed' => 'Administrator opened audit log index',
            'audit_logs.exported' => 'Administrator exported audit records'
                . (isset($meta['row_count']) ? ' · '.(int) $meta['row_count'].' rows' : ''),
            'admin.users.pii_viewed' => 'Administrator opened user edit screen (PII)',
            'certificate.revoked' => 'Certificate revoked'
                . (isset($meta['reason']) ? ' · '.(string) $meta['reason'] : ''),
            'academy.application.created' => 'Exam passed — application and receipt created',
            'academy.application.payment_confirmed' => 'Offline payment confirmed by admin',
            'academy.application.collected' => 'Certificate collected by member',
            'constitution.version_submitted_for_review' => 'Constitution version submitted for Presidium review',
            'constitution.version_approved' => 'Constitution version approved',
            'constitution.version_rejected_to_draft' => 'Constitution version returned to draft',
            default => $this->actionLabel($log->action),
        };
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return list<array{label: string, value: string}>
     */
    private function details(AuditLog $log, array $meta): array
    {
        $details = [];
        $labels = config('audit.metadata_labels', []);
        $skip = ['workflow_channel', 'presidium_review_bypassed'];

        foreach ($meta as $key => $value) {
            if (in_array($key, $skip, true) || $value === null || $value === '') {
                continue;
            }

            $details[] = [
                'label' => $labels[$key] ?? Str::headline(str_replace('_', ' ', (string) $key)),
                'value' => $this->formatMetadataValue($key, $value),
            ];
        }

        if (isset($meta['workflow_channel']) || array_key_exists('presidium_review_bypassed', $meta)) {
            $channel = $meta['workflow_channel'] ?? null;
            if ($channel) {
                $details[] = ['label' => 'Workflow channel', 'value' => (string) $channel];
            }
            if (array_key_exists('presidium_review_bypassed', $meta)) {
                $details[] = [
                    'label' => 'Presidium review',
                    'value' => $meta['presidium_review_bypassed'] ? 'Bypassed' : 'Required',
                ];
            }
        }

        if ($log->request_id) {
            $details[] = ['label' => 'Request ID', 'value' => (string) $log->request_id];
        }

        return $details;
    }

    private function formatMetadataValue(string $key, mixed $value): string
    {
        if ($key === 'filters' && is_array($value)) {
            $parts = [];
            foreach ($value as $filterKey => $filterValue) {
                if ($filterValue === null || $filterValue === '') {
                    continue;
                }
                $parts[] = Str::headline((string) $filterKey).': '.$filterValue;
            }

            return $parts !== [] ? implode(' · ', $parts) : 'None';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '—';
        }

        return (string) $value;
    }

    private function showIp(string $action): bool
    {
        return str_starts_with($action, 'auth.');
    }
}
