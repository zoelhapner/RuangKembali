<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Services\AttendanceService;
use App\Services\AttendanceSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::where('employee_id', auth()->user()->id)
            ->latest('attendance_date')
            ->paginate(20);

        return view('attendances.index', compact('attendances'));
    }

    public function datatable(Request $request, AttendanceSummaryService $summaryService) 
{
    $month = $request->month ?? now()->month;
    $year  = $request->year ?? now()->year;

    $summaries = $summaryService->summaries($month, $year);

    $employees = Employee::with('user');

    return DataTables::eloquent($employees)

        ->addIndexColumn()
        ->addColumn('fullname', function ($employee) {

            return $employee->user?->fullname;

        })
        ->addColumn('h', fn($e) => $summaries[$e->id]['H'] ?? 0)
        ->addColumn('tla', fn($e) => $summaries[$e->id]['TL A'] ?? 0)
        ->addColumn('tlb', fn($e) => $summaries[$e->id]['TL B'] ?? 0)
        ->addColumn('tlc', fn($e) => $summaries[$e->id]['TL C'] ?? 0)
        ->addColumn('dl', fn($e) => $summaries[$e->id]['DL'] ?? 0)
        ->addColumn('izin', fn($e) => $summaries[$e->id]['I'] ?? 0)
        ->addColumn('sakit', fn($e) => $summaries[$e->id]['S'] ?? 0)
        ->addColumn('cuti', fn($e) => $summaries[$e->id]['C'] ?? 0)
        ->addColumn('alpha', fn($e) => $summaries[$e->id]['A'] ?? 0)
        ->addColumn('total_hari_kerja', fn($e) => $summaries[$e->id]['total_hari_kerja'] ?? 0)
        ->addColumn('total_hari_kehadiran', fn($e) => $summaries[$e->id]['total_hari_kehadiran'] ?? 0)
        ->addColumn('kehadiran', fn($e) => ($summaries[$e->id]['kehadiran'] ?? 0).' %')
        ->addColumn('ketepatan_waktu', fn($e) => ($summaries[$e->id]['ketepatan_waktu'] ?? 0).' %')
        ->addColumn('lembur', fn($e) => round(($summaries[$e->id]['total_jam_lembur'] ?? 0)/60,2))
        ->addColumn('keterangan', fn($e) => $summaries[$e->id]['keterangan'] ?? '')
        ->addColumn('roles', function ($row) {
            return $row->user?->roles?->pluck('name')->implode(', ') ?: '-';
        })
        ->toJson();
}
    public function checkIn(Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
            'check_in_lat' => 'required|numeric',
            'check_in_lng' => 'required|numeric',
        ]);
        $employee = auth()->user()->employee;

        if (!$employee) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($attendance) {
            return back()->with('warning', 'Anda sudah melakukan absensi hari ini.');
        }

        $photoPath = null;

        if ($request->filled('photo')) {

            $image = $request->photo;

            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);

            $filename = 'attendance/checkin/' . Str::uuid() . '.jpg';

            Storage::disk('public')->put(
                $filename,
                base64_decode($image)
            );

            $photoPath = $filename;
        }

        Attendance::create([
            'employee_id'      => $employee->id,
            'attendance_date'  => $today,
            'check_in'         => now(),
            'status'           => 'present',
            'check_in_photo'   => $photoPath,
            'check_in_lat'     => $request->check_in_lat,
            'check_in_lng'     => $request->check_in_lng,
        ]);

        return back()->with('success', 'Berhasil melakukan absensi masuk.');
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
            'check_out_lat' => 'required|numeric',
            'check_out_lng' => 'required|numeric',
        ]);

        $employee = auth()->user()->employee;

        if (!$employee) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', today())
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan absensi masuk.');
        }

        if ($attendance->check_out) {
            return back()->with('warning', 'Anda sudah melakukan absensi pulang.');
        }

        // Simpan foto
        $photoPath = null;

        if ($request->filled('photo')) {

            $image = $request->photo;

            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);

            $filename = 'attendance/checkout/' . Str::uuid() . '.jpg';

            Storage::disk('public')->put(
                $filename,
                base64_decode($image)
            );

            $photoPath = $filename;
        }

        $attendance->update([
            'status'           => 'go_home',
            'check_out'        => now(),
            'check_out_photo'  => $photoPath,
            'check_out_lat'    => $request->check_out_lat,
            'check_out_lng'    => $request->check_out_lng,
        ]);

        app(AttendanceService::class)->calculate($attendance);
        return back()->with('success', 'Berhasil melakukan absensi pulang.');
    }
}