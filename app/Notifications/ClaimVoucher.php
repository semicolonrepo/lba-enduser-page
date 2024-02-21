<?php

namespace App\Notifications;

use App\Channels\QontakGatewayChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ClaimVoucher extends Notification
{
    use Queueable;

    private $voucher;

    /**
     * Create a new notification instance.
     */
    public function __construct($voucher)
    {
        $this->voucher = $voucher;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Directly fetch mailer configuration from the database
        $mailerConfig = DB::table('campaigns')
            ->where('id', $this->voucher->campaign_id)
            ->select('mail_username', 'mail_password')
            ->first();

        config([
            'mail.mailers.smtp.username' => $mailerConfig->mail_username ?? env('MAIL_USERNAME'),
            'mail.mailers.smtp.password' =>  $mailerConfig->mail_password ?? env('MAIL_PASSWORD'),
        ]);

        return ['mail', QontakGatewayChannel::class];
    }

    /**
     * Get the whatsapp representation of the notification.
     */
    public function toWhatsapp(object $notifiable): object
    {
        return $this->voucher;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('Claim Voucher Letsbuyasia!')
        ->view('emails.claim_voucher', [
            'voucher' => $this->voucher,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
