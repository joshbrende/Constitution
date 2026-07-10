<?php

namespace App\Support;

use App\Models\DialogueMessage;
use App\Models\DialogueMessageAttachment;
use Illuminate\Support\Facades\Storage;

class DialogueMessagePresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(DialogueMessage $message): array
    {
        $message->loadMissing('user', 'attachments');
        $isEditor = DialogueEditor::isEditorUser($message->user);

        if ($message->is_deleted) {
            return [
                'id' => $message->id,
                'body' => null,
                'is_deleted' => true,
                'is_pinned' => (bool) $message->is_pinned,
                'is_official' => $isEditor,
                'user' => $message->user?->only(['id', 'name', 'surname']),
                'created_at' => $message->created_at?->toIso8601String(),
                'updated_at' => $message->updated_at?->toIso8601String(),
                'attachments' => [],
            ];
        }

        return [
            'id' => $message->id,
            'body' => $message->body,
            'is_deleted' => false,
            'is_pinned' => (bool) $message->is_pinned,
            'is_official' => $isEditor,
            'user' => $message->user?->only(['id', 'name', 'surname']),
            'created_at' => $message->created_at?->toIso8601String(),
            'updated_at' => $message->updated_at?->toIso8601String(),
            'attachments' => $message->attachments->map(function (DialogueMessageAttachment $a) {
                $disk = $a->disk ?: 'public';
                $url = $disk === 'public'
                    ? Storage::disk('public')->url($a->path)
                    : null;

                return [
                    'id' => $a->id,
                    'type' => $a->type,
                    'url' => $url,
                    'name' => $a->original_name,
                    'mime' => $a->mime,
                    'size_bytes' => (int) $a->size_bytes,
                ];
            })->values()->all(),
        ];
    }
}
