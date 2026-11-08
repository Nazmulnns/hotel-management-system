<?php

namespace App\Console\Commands;

use App\Enums\MaintenanceStatus;
use App\Mail\WeeklyMaintenanceReport;
use App\Models\RoomMaintenance;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('maintenances:send-weekly-report')]
#[Description('Email the manager a PDF of pending, in progress and this week\'s resolved maintenances')]
class SendWeeklyMaintenanceReport extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $managerEmail = config('mail.manager_address');

        if (blank($managerEmail)) {
            $this->error('Set MAIL_MANAGER_ADDRESS in .env first.');

            return self::FAILURE;
        }

        $periodEnd = now();
        $periodStart = now()->subWeek();

        $maintenances = RoomMaintenance::query()
            ->with(['room' => fn ($query) => $query->withTrashed()])
            ->whereIn('maintenance_status', [MaintenanceStatus::Pending, MaintenanceStatus::InProgress])
            ->orWhere(function ($query) use ($periodStart) {
                $query->where('maintenance_status', MaintenanceStatus::Resolved)
                    ->where('updated_at', '>=', $periodStart);
            })
            ->oldest()
            ->get();

        Mail::to($managerEmail)->send(new WeeklyMaintenanceReport(
            pending: $maintenances->where('maintenance_status', MaintenanceStatus::Pending)->values(),
            inProgress: $maintenances->where('maintenance_status', MaintenanceStatus::InProgress)->values(),
            resolved: $maintenances->where('maintenance_status', MaintenanceStatus::Resolved)->values(),
            periodStart: $periodStart,
            periodEnd: $periodEnd,
        ));

        $this->info("Report sent to {$managerEmail}.");

        return self::SUCCESS;
    }
}