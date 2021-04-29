<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengembalianUloMail extends Notification
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
            'no_permohonan' => $this->model->no_permohonan,
            'no_sk' => $this->model->no_sk,
            'catatan' => $this->model->catatan,
            'url_mekanisme_ulo' => $this->model->url_mekanisme_ulo,
            'jenis_izin' => $this->model->jenis_izin
        ];
        return (new MailMessage)
            ->subject('Pemberitahuan Status Permohonan')
            ->markdown('mail.pengembalian_ulo_mail', $data);
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
