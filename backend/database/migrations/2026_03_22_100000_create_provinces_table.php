<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Provinces per Constitution of Zimbabwe, Section 267.
     */
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $provinces = [
            ['name' => 'Bulawayo', 'code' => 'bulawayo', 'sort_order' => 1],
            ['name' => 'Harare', 'code' => 'harare', 'sort_order' => 2],
            ['name' => 'Manicaland', 'code' => 'manicaland', 'sort_order' => 3],
            ['name' => 'Mashonaland Central', 'code' => 'mashonaland_central', 'sort_order' => 4],
            ['name' => 'Mashonaland East', 'code' => 'mashonaland_east', 'sort_order' => 5],
            ['name' => 'Mashonaland West', 'code' => 'mashonaland_west', 'sort_order' => 6],
            ['name' => 'Masvingo', 'code' => 'masvingo', 'sort_order' => 7],
            ['name' => 'Matabeleland North', 'code' => 'matabeleland_north', 'sort_order' => 8],
            ['name' => 'Matabeleland South', 'code' => 'matabeleland_south', 'sort_order' => 9],
            ['name' => 'Midlands', 'code' => 'midlands', 'sort_order' => 10],
        ];

        foreach ($provinces as $p) {
            DB::table('provinces')->insert(array_merge($p, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Clear invalid province_ids before adding FK (existing users may have no valid reference)
        DB::table('users')->whereNotNull('province_id')->update(['province_id' => null]);

        // Add foreign key (users.province_id already exists from roles migration)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
        });
        Schema::dropIfExists('provinces');
    }
};
