<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

        DB::unprepared('DROP PROCEDURE IF EXISTS room_occupancy');

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE room_occupancy(IN from_date DATE, IN to_date DATE)
            BEGIN
                SELECT
                    room_id,
                    room_label,
                    booked_nights,
                    DATEDIFF(to_date, from_date) AS total_nights,
                    ROUND(booked_nights / DATEDIFF(to_date, from_date) * 100, 2) AS occupancy_percent
                FROM (
                    SELECT
                        r.id AS room_id,
                        CONCAT(r.floor, r.room_number) AS room_label,
                        COALESCE(SUM(DATEDIFF(LEAST(b.check_out, to_date), GREATEST(b.check_in, from_date))), 0) AS booked_nights
                    FROM rooms r
                    LEFT JOIN booking_rooms br ON br.room_id = r.id
                    LEFT JOIN bookings b ON b.id = br.booking_id
                        AND b.status IN ('confirmed', 'checked_in', 'checked_out')
                        AND b.check_in < to_date
                        AND b.check_out > from_date
                    WHERE r.deleted_at IS NULL
                    GROUP BY r.id, r.floor, r.room_number
                ) AS room_nights
                ORDER BY occupancy_percent DESC, room_id;
            END
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

        DB::unprepared('DROP PROCEDURE IF EXISTS room_occupancy');
    }
};
