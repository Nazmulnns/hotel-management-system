<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::unprepared('DROP FUNCTION IF EXISTS booking_nights');

        DB::unprepared(<<<'SQL'
            CREATE FUNCTION booking_nights(check_in DATE, check_out DATE)
            RETURNS INT
            DETERMINISTIC
            NO SQL
            RETURN DATEDIFF(check_out, check_in)
            SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::unprepared('DROP FUNCTION IF EXISTS booking_nights');
    }
};
