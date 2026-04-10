<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    public function awardPoints(User $user, int $amount, ?string $reason = null): void
    {
        if ($amount <= 0) {
            return;
        }
        $user->increment('points', $amount);
        $this->checkBadges($user);
    }

    public function ensureBadge(User $user, string $slug): bool
    {
        $badge = Badge::where('slug', $slug)->first();
        if (!$badge) {
            return false;
        }
        $exists = DB::table('user_badges')->where('user_id', $user->id)->where('badge_id', $badge->id)->exists();
        if ($exists) {
            return false;
        }
        DB::table('user_badges')->insert([
            'user_id' => $user->id,
            'badge_id' => $badge->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return true;
    }

    private function checkBadges(User $user): void
    {
        $points = (int) $user->fresh()->points;
        foreach (Badge::where('points_required', '>', 0)->where('points_required', '<=', $points)->orderBy('points_required')->get() as $badge) {
            $this->ensureBadge($user, $badge->slug);
        }
    }
}
