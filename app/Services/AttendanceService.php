<?php

namespace App\Services;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceService
{
    public function calculate(Attendance $attendance): void
    {
        $workMinutes = $this->calculateWorkMinutes($attendance);

        $attendanceCode = $this->calculateAttendanceCode(
            $attendance,
            $workMinutes
        );

        $attendance->update([
            'work_minutes' => $workMinutes,
            'attendance_code' => $attendanceCode,
            'is_full_work' => $workMinutes >= 480,
        ]);
    }

    private function calculateWorkMinutes(Attendance $attendance): int
    {
        return Carbon::parse($attendance->check_in)
            ->diffInMinutes(
                Carbon::parse($attendance->check_out)
            );
    }

    private function calculateAttendanceCode(
        Attendance $attendance,
        int $workMinutes
    ): ?string {

        if ($workMinutes < 480) {
            return null;
        }

        $time = Carbon::parse($attendance->check_in)->format('H:i:s');

        return match (true) {
            $time < '08:00:00'  => 'H',
            $time <= '08:10:00' => 'TL A',
            $time <= '08:20:00' => 'TL B',
            default             => 'TL C',
        };
    }
}