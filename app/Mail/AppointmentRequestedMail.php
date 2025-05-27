<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentRequestedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointmentId;

    public function __construct(int $appointmentId)
    {
        $this->appointmentId = $appointmentId;
    }

    public function build()
    {
        return $this
            ->subject('Randevu Talebiniz Alındı')
            ->view('emails.appointment-requested')
            ->with(['appointmentId' => $this->appointmentId]);
    }
}
