<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('party_profiles', function (Blueprint $table) {
            $table->id();
            $table->longText('history')->nullable();
            $table->longText('veterans_league_body')->nullable();
            $table->string('veterans_league_leader_name')->nullable();
            $table->string('veterans_league_leader_title')->nullable();
            $table->longText('womens_league_body')->nullable();
            $table->string('womens_league_leader_name')->nullable();
            $table->string('womens_league_leader_title')->nullable();
            $table->longText('youth_league_body')->nullable();
            $table->string('youth_league_leader_name')->nullable();
            $table->string('youth_league_leader_title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('party_profiles');
    }
};

