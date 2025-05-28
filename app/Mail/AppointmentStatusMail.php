<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointmentId;
    public $status;

    /**
     * Create a new message instance.
     *
     * @param  int    $appointmentId
     * @param  string $status
     * @return void
     */
    public function __construct(int $appointmentId, string $status)
    {
        $this->appointmentId = $appointmentId;
        $this->status        = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Randevu Durum Güncellendi')
            ->view('emails.appointmentStatus', [
                'appointmentId' => $this->appointmentId,
                'status'        => $this->status,
            ]);
    }
}
