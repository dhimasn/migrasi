<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PerbaikanMail extends Notification
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
            'nama' => $this->model->nama,
            'jabatan' => $this->model->jabatan,
            'nama_pt' => $this->model->nama_pt,
            'no_permohonan' => $this->model->no_permohonan,
            'tanggal_input' => $this->model->tanggal_input,
            'jenis_permohonan' => $this->model->jenis_permohonan,
        ];
        return (new MailMessage)
            ->subject('Pemberitahuan Status Permohonan')
            ->markdown('mail.perbaikan_mail', $data);
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
