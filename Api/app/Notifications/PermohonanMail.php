<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermohonanMail extends Notification
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
            'jenis_izin' => $this->model->jenis_izin,
            'no_permohonan' => $this->model->no_permohonan,
            'no_sk' => $this->model->no_sk,
            'url_komitmen' =>  $this->model->url_komitmen
        ];
        $a = new MailMessage;
        $a->subject('Pemberitahuan Status Permohonan');
        $a->markdown('mail.permohonan_mail', $data);
        foreach($this->model->list_path as $lp)
        {
            $a->attach($lp->file_pdf_path);
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
