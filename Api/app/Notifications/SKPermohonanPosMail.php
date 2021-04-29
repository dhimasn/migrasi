<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SKPermohonanPosMail extends Notification
{
    use Queueable;
    private $model;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $data = [
            'nama_pt' => $this->model->nama_pt,
            'no_penyelenggaraan' => $this->model->no_penyelenggaraan,
            'no_sk' => $this->model->no_sk,
            'jenis_izin' => $this->model->jenis_izin,
            'url_upload_spm' => $this->model->url_upload_spm,
            'expired_date' => $this->model->expired_date
        ];

        $a = new MailMessage;
        $a->subject('Pemberitahuan Status Permohonan');
        $a->markdown('mail.sk_pos_mail', $data);
        // $a->attach($this->model->list_path);
        foreach ($this->model->list_path as $lp) {
            $a->attach($lp);
        }
        return $a;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
