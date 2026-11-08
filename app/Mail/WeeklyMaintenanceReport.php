<?php

namespace App\Mail;

use App\Models\RoomMaintenance;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyMaintenanceReport extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  Collection<int, RoomMaintenance>  $pending
     * @param  Collection<int, RoomMaintenance>  $inProgress
     * @param  Collection<int, RoomMaintenance>  $resolved
     */
    public function __construct(
        public Collection $pending,
        public Collection $inProgress,
        public Collection $resolved,
        public CarbonInterface $periodStart,
        public CarbonInterface $periodEnd,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Weekly maintenance report ({$this->periodStart->toDateString()} to {$this->periodEnd->toDateString()})",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.maintenances.weekly-report',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => Pdf::loadView('pdf.maintenance-weekly-report', [
                    'pending' => $this->pending,
                    'inProgress' => $this->inProgress,
                    'resolved' => $this->resolved,
                    'periodStart' => $this->periodStart,
                    'periodEnd' => $this->periodEnd,
                ])->output(),
                "maintenance-report-{$this->periodEnd->toDateString()}.pdf",
            )->withMime('application/pdf'),
        ];
    }
}