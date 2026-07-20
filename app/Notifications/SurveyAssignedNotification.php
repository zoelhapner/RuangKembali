<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SurveyAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public $survey,
        public string $type
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $project = $this->survey->project;

        return [
            'type' => $this->type,
            'survey_id' => $this->survey->id,
            'project_id'      => $project->id,
            'project_name'    => $project->project_name,
            'message'         => match ($this->type) {
                'created_self'      => 'Selamat, Anda telah berhasil menyimpan form survei',
                'assigned_employee' => 'Anda ditugaskan untuk melakukan kegiatan survei proyek',
                'customer'          => 'Form survei Anda berhasil disimpan dan sekarang masuk ke tahap penawaran jasa desain',
                default             => 'Update proyek',
            },
            'url' => route('projects.create', ['project_id' => $project->id]),
        ];
    }
}

