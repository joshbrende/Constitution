<?php

namespace App\Services;

class PushNotificationUrlResolver
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function resolve(array $data): string
    {
        $base = rtrim((string) config('app.url'), '/').'/app';

        if (! empty($data['application_id'])) {
            return "{$base}/home/receipt/{$data['application_id']}";
        }

        if (($data['cta_tab'] ?? null) === 'ChatTab') {
            return "{$base}/chat";
        }

        if (($data['cta_screen'] ?? null) === 'ChatThread' && ! empty($data['cta_params']['threadId'])) {
            return "{$base}/chat/threads/{$data['cta_params']['threadId']}";
        }

        if (($data['cta_screen'] ?? null) === 'CourseDetail' && ! empty($data['cta_params']['courseId'])) {
            return "{$base}/home/academy/courses/{$data['cta_params']['courseId']}";
        }

        if (in_array($data['cta_screen'] ?? null, ['AcademyStatus', 'Certificates'], true)) {
            return "{$base}/home/academy-status";
        }

        if (($data['cta_screen'] ?? null) === 'AcademyHome') {
            return "{$base}/home/academy";
        }

        return "{$base}/home/notifications";
    }
}
