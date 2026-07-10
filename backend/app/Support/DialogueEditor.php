<?php

namespace App\Support;

use App\Models\User;

class DialogueEditor
{
    public static function userId(): ?int
    {
        static $cached = false;
        static $id = null;

        if ($cached === false) {
            $id = User::query()->where('name', 'System')->value('id');
            $cached = true;
        }

        return $id ? (int) $id : null;
    }

    public static function isEditorUser(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->name === 'System') {
            return true;
        }

        $editorId = self::userId();

        return $editorId !== null && (int) $user->id === $editorId;
    }
}
