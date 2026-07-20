<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConsultationAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public $consultation,
        public string $type
    ) {}

    public function via(object $notifiable): array
    {
        return ['database']; // email matikan dulu biar gak ribet
    }

    public function toDatabase($notifiable)
    {
        $project = $this->consultation->project;

        return [
            'type' => $this->type,
            'consultation_id' => $this->consultation->id,
            'project_id'      => $project->id,
            'project_name'    => $project->project_name,
            'message'         => match ($this->type) {
                'created_self'      => 'Selamat, Anda telah melakukan tahap konsultasi proyek',
                'assigned_employee' => 'Anda telah melakukan tahap konsultasi proyek bersama customer',
                'customer'          => 'Form konsultasi Anda berhasil disimpan dan sekarang masuk ke tahap rencana survei',
                default             => 'Update proyek',
            },
            'url' => route('projects.create', ['project_id' => $project->id]),
        ];
    }
}

