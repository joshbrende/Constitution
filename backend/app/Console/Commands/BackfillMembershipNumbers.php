<?php

namespace App\Console\Commands;

use App\Enums\MembershipStanding;
use App\Models\User;
use App\Services\MembershipNumberService;
use Illuminate\Console\Command;

class BackfillMembershipNumbers extends Command
{
    protected $signature = 'membership:backfill-numbers {--chunk=200 : Users per chunk}';

    protected $description = 'Assign opaque membership numbers to full members who do not have one yet';

    public function handle(MembershipNumberService $numbers): int
    {
        $chunk = max(1, (int) $this->option('chunk'));
        $assigned = 0;

        User::query()
            ->where('membership_standing', MembershipStanding::Member->value)
            ->whereNull('membership_number')
            ->orderBy('id')
            ->chunkById($chunk, function ($users) use ($numbers, &$assigned) {
                foreach ($users as $user) {
                    $numbers->ensureForFullMember($user);
                    if (filled($user->fresh()->membership_number)) {
                        $assigned++;
                    }
                }
            });

        $this->info("Assigned {$assigned} membership number(s).");

        return self::SUCCESS;
    }
}
