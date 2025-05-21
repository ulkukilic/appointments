<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /** 
     * @var \stdClass|Model  Gönderilecek kullanıcı verisi (full_name vs.)
     */
    public $user;

    /**
     * @var string  Şifre sıfırlama bağlantısı (URL)
     */
    public $resetUrl;

    /**
     * Constructor: Mail nesnesi oluşturulurken
     * gerekli parametreler buraya iletilir.
     *
     * @param  mixed   $user     Kullanıcı nesnesi veya stdClass
     * @param  string  $resetUrl Şifre sıfırlama linki
     */
    public function __construct($user, $resetUrl)
    {
        // Gelen kullanıcı bilgisini sınıf özelliğine ata
        $this->user = $user;

        // Gelen reset URL’sini sınıf özelliğine ata
        $this->resetUrl = $resetUrl;
    }

    /**
     * Mail içeriğini, başlığını ve view’i tanımlar.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            // E-posta başlığını ayarla
            ->subject('Password Reset Request')
            // Hangi blade şablonunun kullanılacağını belirt
            ->view('emails.reset-password')
            // View’e gönderilecek verileri tanımla
            ->with([
                // Kullanıcı adını şablonda {{ $fullName }} olarak kullan
                'fullName' => $this->user->full_name,
                // Şifre sıfırlama linkini {{ $resetUrl }} olarak kullan
                'resetUrl'  => $this->resetUrl,
            ]);
    }
}
