<?php

namespace App\Console\Commands;

use App\Enums\MembershipStanding;
use App\Models\User;
use App\Services\WingMembershipService;
use Illuminate\Console\Command;

class BackfillWingMemberships extends Command
{
    protected $signature = 'membership:backfill-wings {--chunk=200 : Users per chunk}';

    protected $description = 'Ensure full members have main (and legacy wing) rows in memberships';

    public function handle(WingMembershipService $wings): int
    {
        $chunk = max(1, (int) $this->option('chunk'));
        $count = 0;

        User::query()
            ->where('membership_standing', MembershipStanding::Member->value)
            ->orderBy('id')
            ->chunkById($chunk, function ($users) use ($wings, &$count) {
                foreach ($users as $user) {
                    $wings->ensureForFullMember($user);
                    $count++;
                }
            });

        $this->info("Processed {$count} full member(s).");

        return self::SUCCESS;
    }
}
