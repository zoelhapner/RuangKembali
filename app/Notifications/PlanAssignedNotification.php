<?php

namespace App\Notifications;

use App\Models\Planning;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Planning $planning,
        public string $type
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $project = $this->planning->project;

        return [
            'type' => $this->type,
            'planning_id' => $this->planning->id,
            'project_id'  => $project->id,
            'project_name'=> $project->project_name,
            'message'     => match ($this->type) {
                'created_self'      => 'Selamat, Anda telah berhasil menyimpan form rencana survei',
                'assigned_employee' => 'Anda direncanakan untuk melakukan survei proyek',
                'customer'          => 'Form rencana survei Anda berhasil disimpan dan sekarang masuk ke tahap survei',
                default             => 'Update proyek',
            },
            'url' => route('projects.continue', $project->id),
        ];
    }
}

