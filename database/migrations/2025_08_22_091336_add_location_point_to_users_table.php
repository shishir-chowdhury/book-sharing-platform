<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE users ADD COLUMN location POINT NOT NULL DEFAULT (ST_GeomFromText('POINT(0 0)')) AFTER longitude");

        DB::statement('ALTER TABLE users ADD SPATIAL INDEX location_index (location)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP INDEX location_index');
        DB::statement('ALTER TABLE users DROP COLUMN location');
    }
};
