<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('wing', 32);
            $table->string('status', 16)->default('active')->index();
            $table->timestamp('joined_at');
            $table->timestamp('ended_at')->nullable();
            $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'wing']);
            $table->index(['wing', 'status']);
        });

        $now = now();
        $upsert = function (int $userId, string $wing) use ($now): void {
            $exists = DB::table('memberships')
                ->where('user_id', $userId)
                ->where('wing', $wing)
                ->exists();

            if ($exists) {
                DB::table('memberships')
                    ->where('user_id', $userId)
                    ->where('wing', $wing)
                    ->update([
                        'status' => 'active',
                        'ended_at' => null,
                        'updated_at' => $now,
                    ]);

                return;
            }

            DB::table('memberships')->insert([
                'user_id' => $userId,
                'wing' => $wing,
                'status' => 'active',
                'joined_at' => $now,
                'ended_at' => null,
                'assigned_by_user_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        };

        DB::table('users')->orderBy('id')->chunkById(200, function ($users) use ($upsert) {
            foreach ($users as $user) {
                $wing = strtolower(trim((string) ($user->wing ?? '')));
                $isFullMember = ($user->membership_standing ?? '') === 'member';

                if (! $isFullMember) {
                    continue;
                }

                $upsert((int) $user->id, 'main');

                if ($wing !== '' && in_array($wing, ['main', 'youth', 'women', 'veterans'], true)) {
                    $upsert((int) $user->id, $wing);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
