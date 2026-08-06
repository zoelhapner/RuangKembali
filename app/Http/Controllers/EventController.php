<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\EventCategory;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Affiliator;
use App\Models\Worker;
use App\Models\Invoice;
use App\Models\InvoiceBuild;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\PostalCode;
use App\Models\Event;
use App\Models\EventLevel;
use App\Models\EventTask;
use App\Models\JobCategory;
use App\Models\RabProcess;
use App\Models\RabProcessCategory;
use App\Models\BuildDailyReport;
use App\Models\BuildProcessItem;
use App\Models\BuildPlans;
use App\Models\WeeklyReport;
use App\Models\User;
use App\Services\EventNotifier;
use App\Services\BuildPlanSyncService;
use App\Services\BuildProcessSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use DB;

class EventController extends Controller
{
       public function index(Request $request)
{
    $auth = auth()->user();

    $query = Event::with([
        'customer.user:id,fullname',
        'employee.user:id,fullname',
        'affiliator.user:id,fullname',
        'province:id,name',
        'city:id,name',
        'district:id,name',
        'subDistrict:id,name',
        'postalCode:id,postal_code',
        'levels:id,event_id,level_order,level_name,is_completed'
    ]);

    // Jika ada hak akses untuk membatasi data
if (
    $auth->can('lihat data event') &&
    !$auth->can('lihat daftar event')
) {
    $query->where(function ($q) use ($auth) {
        $q->whereHas('customer', function ($qq) use ($auth) {
            $qq->where('user_id', $auth->id);
        })
        ->orWhereHas('employee', function ($qq) use ($auth) {
            $qq->where('user_id', $auth->id);
        });
    });
}

    if ($request->ajax()) {
        $events = $query->get();

        $statusLabel = [
            1 => 'Proses',
            2 => 'Revisi',
            3 => 'Butuh Persetujuan',
            4 => 'Selesai'
        ];

        return DataTables::of($query)
        ->addIndexColumn()
        ->editColumn('event_name', function ($row) {
            $url = route('events.show', $row->id);
            $name = Str::title($row->event_name ?? '-');
            return '<a href="'.$url.'">'.e($name).'</a>';
        })
        ->addColumn('event_category', function ($row) {
            return $row->category?->name ?? '-';
        })
        ->addColumn('schedule', function ($row) {
            return $row->start_at->format('d M Y H:i') .
                '<br><small>s/d ' .
                $row->end_at->format('d M Y H:i') .
                '</small>';
        })

        ->addColumn('registration', function ($row) {
            return optional($row->registration_open)->format('d M') .
                ' - ' .
                optional($row->registration_close)->format('d M Y');
        })

        ->editColumn('price', function ($row) {
            return $row->price == 0
                ? '<span class="badge bg-success">Gratis</span>'
                : 'Rp ' . number_format($row->price, 0, ',', '.');
        })

        ->editColumn('quota', function ($row) {
            return $row->remaining_quota . ' / ' . $row->quota;
        })

        ->editColumn('status', function ($row) {
            return match ($row->status_label) {
                'Coming Soon' => '<span class="badge bg-secondary">Coming Soon</span>',
                'Pendaftaran' => '<span class="badge bg-success">Pendaftaran</span>',
                'Sold Out' => '<span class="badge bg-danger">Sold Out</span>',
                'Sedang Berlangsung' => '<span class="badge bg-warning">Berlangsung</span>',
                'Selesai' => '<span class="badge bg-dark">Selesai</span>',
            };
        })
        ->addColumn('action', function ($event) {
            $buttons = '';
            if (auth()->user()->can('hapus data proyek')) {
                $buttons .= '<button data-id="' . $event->id . '" 
                            class="btn btn-icon btn-sm btn-dark delete-events">
                            <i class="ti ti-trash"></i></button>';
            }
            return $buttons;
        })

        ->rawColumns(['action', 'schedule', 'price', 'status', 'event_name'])
        ->make(true);
    }

    return view('events.index');
}

public function create()
{
    $categories = EventCategory::orderBy('name')->get();

    $eventTypes = [
        'free' => 'Gratis',
        'paid' => 'Berbayar',
    ];

    $audiences = [
        'public' => 'Umum',
        'gender' => 'Berdasarkan Gender',
        'age' => 'Berdasarkan Usia',
    ];
    $statuses = [
        'coming_soon' => 'Coming Soon',
        'registration_open' => 'Pendaftaran',
        'sold_out' => 'Sold Out',
        'ongoing' => 'Sedang Berlangsung',
        'finished' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    return view('events.create', compact(
        'categories',
        'eventTypes',
        'audiences',
        'statuses'
    ));
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'event_category_id' => 'required|exists:event_categories,id',
            'event_type' => 'required|in:free,paid',
            'audience_type' => 'required|in:public,gender,age',

            'registration_open' => 'nullable|date',
            'registration_close' => 'nullable|date|after_or_equal:registration_open',

            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',

            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'quota' => 'nullable|integer|min:1',

            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'description' => 'nullable|string',
            'is_published' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $poster = null;
            $thumbnail = null;

            if ($request->hasFile('poster')) {
                $poster = $request->file('poster')
                    ->store('events/posters', 'public');
            }

            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail')
                    ->store('events/thumbnails', 'public');
            }

            Event::create([
                'license_id' => auth()->user()->license_id ?? null,

                'event_code' => $this->generateEventCode(),

                'name' => $request->name,
                'event_category_id' => $request->event_category_id,
                'event_type' => $request->event_type,
                'audience_type' => $request->audience_type,

                'registration_open' => $request->registration_open,
                'registration_close' => $request->registration_close,

                'start_at' => $request->start_at,
                'end_at' => $request->end_at,

                'location' => $request->location,
                'price' => $request->price ?? 0,
                'quota' => $request->quota,

                'poster' => $poster,
                'thumbnail' => $thumbnail,

                'description' => $request->description,
                'is_published' => $request->boolean('is_published'),
            ]);

            DB::commit();

            return redirect()
                ->route('events.index')
                ->with('success', 'Event berhasil ditambahkan.');

        } catch (\Throwable $e) {

            DB::rollBack();

            if ($poster) {
                Storage::disk('public')->delete($poster);
            }

            if ($thumbnail) {
                Storage::disk('public')->delete($thumbnail);
            }

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function continue(Event $event, Request $request)
{
    $event->load([
        'customer.user',
        'employee',
        'levels.employees',
        'consultation.items',
        'planning',
        'survey.items',
        'offer.items',
        'offer.rab.items.category',
        'rab.categories.uraians.items.category',
        'buildItems.jobCategory',
        'buildItems.weeklyProgresses', 
        'buildItems.tambahan.weeklyProgresses',
        'dailyReports.works.rabProcessItem',
        'dailyReports.workers.worker.user',
        'dailyReports.materials'
    ]);

    $activeStep = $this->computeActiveStep($event);

    return redirect()->route('events.create', [
        'event_id' => $event->id,
        'step'       => $activeStep
    ]);
}

private function computeActiveStep($event, $request = null)
{
    if ($request && $request->filled('step')) {
        return (int) $request->step;
    }

    if (!$event) {
        return 1;
    }

    $current = $event->levels
        ->where('is_completed', false)
        ->sortBy('level_order')
        ->first();

    return $current ? $current->level_order + 1 : 9;
}

private function stepKeyMap()
{
    return [
        0 => 'event',
        1 => 'form-konsultasi',
        2 => 'detail-konsultasi',
        3 => 'planning',
        4 => 'survei',
        5 => 'offer',
        6 => 'kontrak',
        7 => 'invoice',
        8 => 'work',
        9 => 'invoice-final',
        10 => 'final',
    ];
}

public function update(Request $request, Event $event)
{
    abort_if(auth()->user()->cannot('lihat daftar proyek'), 403);
    
    $event->update($request->all());

    return back()->with('success', 'Data proyek berhasil diperbarui!');
}
public function show(Event $event)
{
    return redirect()->route('events.create', ['event_id' => $event->id]);
}
     public function destroy(Event $event) 
    {
        if ($event) {
            $event->delete();
            return response()->json(['status' => 'success', 'message' => 'Event deleted successfully']);
        }

        return response()->json(['status' => 'failed', 'message' => 'Unable to delete']);
    }
private function buildTimelineSteps($event, int $activeStep): \Illuminate\Support\Collection
{
    if (!$event) {
        return collect([]);
    }
    $map = $this->stepKeyMap();
    return $event->levels
        ->sortBy('level_order')
        ->map(function ($level) use ($activeStep, $map) {
            $order = $level->level_order + 1;
            return [
                'id'        => $map[$order] ?? 'step-' . $order,
                'label'     => $level->level_name,
                'completed' => $level->is_completed,
                'current'   => $activeStep === $order,
            ];
        })
        ->values();
}
private function formData($event = null, int $activeStep = 1, ?int $eventType = null, array $merge = []): array
{
    $data = [
        'eventStatus' => [
            1 => 'Proses',
            2 => 'Revisi',
            3 => 'Butuh Persetujuan',
            4 => 'Selesai',
        ],
    ];

    if ($activeStep >= 1) {
        $data['employees']   = Employee::with('user:id,fullname')->get(['id', 'user_id']);
        $data['customers']   = Customer::with('user:id,fullname')->get(['id', 'user_id']);
        $data['affiliators'] = Affiliator::with('user:id,fullname')->get(['id', 'user_id']);
        $data['provinces']   = Province::all();
    }

    if ($eventType == 3 && $activeStep >= 8) {
        $data['workers'] = Worker::with('user:id,fullname')->get(['id', 'user_id']);
    }
    // ── Design packages — hanya type 1, saat step penawaran ──
    if ($eventType == 1 && $activeStep >= 5) {
        $data['designPackages'] = \App\Models\DesignPackage::orderBy('name')
            ->orderBy('price_meter')->get();
    }
    // ── RAB packages — hanya type 2, saat step penawaran ──
    if ($eventType == 2 && $activeStep >= 5) {
        $data['rabPackages'] = \App\Models\RabPackage::orderBy('name')
            ->orderBy('price_meter')->get();
    }
    // ── Job categories — hanya type 3, saat step penawaran/build ──
    if (in_array($eventType, [2, 3]) && $activeStep >= 5) {
        $data['jobCategories'] = JobCategory::orderBy('kode_urut')
            ->orderBy('nama_pekerjaan')->get();
    }
    // ── RAB processes & items — hanya jika edit form offer aktif ──
    // Ini bisa di-lazy-load via AJAX di masa depan
    if ($activeStep >= 5 && $event?->customer_id) {

        $data['rabProcesses'] = RabProcess::whereHas('event', function ($q) use ($event) {
            $q->where('customer_id', $event->customer_id);
        })->get();

        $data['categories'] = RabProcessCategory::with([
            'uraians.items.rab'
        ])
        ->whereHas('rabProcess.event', function ($q) use ($event) {
            $q->where('customer_id', $event->customer_id);
        })
        ->orderBy('order_no')
        ->get();
    }
    return array_merge($data, $merge);
}
    public function invoicePanel(Event $event)
{
    $event->load('invoiceBuilds');

    return view('events.partials.invoice_panel',
    compact('event'));
}

public function loadTambahan(BuildProcessItem $item)
{
    $item->load([
        'tambahan.weeklyProgresses'
    ]);

    $jobCategories = JobCategory::select(
        'id',
        'nama_pekerjaan'
    )->get();

    return view(
        'events.partials.tambahan_rows',
        [
            'item' => $item,
            'jobCategories' => $jobCategories,
            'weekLabels' => $item->event->week_labels,
        ]
    )->render();
}
public function syncBuildPlan(Event $event)
{
    app(BuildPlanSyncService::class)->syncFull($event);
    app(BuildProcessSyncService::class)->syncFull($event);

    $event->update([
        'need_sync_build' => false
    ]);

    return back()->with(
        'success',
        'Build Plan dan Build Process berhasil disinkronkan.'
    );
}
public function syncBuildProcess(Event $event)
{
    app(BuildProcessSyncService::class)
        ->syncFull($event);

    $event->update([
        'need_sync_build' => false
    ]);
    return back()->with(
        'success',
        'Build process berhasil disinkronkan.'
    );
}
public function data(Event $event)
{
    $weeks = $event->week_labels;
    $query = BuildPlans::query()
        ->with('weeks')
        ->where('event_id',$event->id)
        ->ordered();
    $dataTable = DataTables::eloquent($query)
        ->addIndexColumn()
        ->addColumn('total_format',function($row){
            return 'Rp '.number_format(
                $row->total,
                0,
                ',',
                '.'
            );
        })
        ->addColumn('bobot_format',function($row){
            return number_format(
                $row->bobot_percent,
                3,
                '.',
                ''
            );
        })
        ->addColumn('week_values',function($row) use($weeks){
            $values=[];
            foreach($weeks as $week){
                $progress=$row->weeks
                    ->firstWhere(
                        'week_no',
                        $week['week_no']
                    );
                $values[$week['week_no']] = $progress?->plan_percent ?? 0;
            }
            return $values;
        });
    $response=$dataTable->make(true);

    $plans = BuildPlans::with('weeks')
        ->where('event_id',$event->id)
        ->get();

    $weekTotal=[];

    foreach($weeks as $week){
        $weekTotal[$week['week_no']] =
            $plans->sum(function($plan) use($week){
                $weekPlan = $plan->weeks
                    ->firstWhere('week_no', $week['week_no']);
                
                $planPersen = $weekPlan?->plan_percent ?? 0;
                $bobot = $plan->bobot_percent ?? 0;
                
                return ($planPersen / 100) * $bobot;
            });
    }

    $kumulatif=[];
    $running=0;
    foreach($weekTotal as $week=>$total){
        $running += $total;
        $kumulatif[$week]=$running;
    }

    return $dataTable
        ->with([
            'week_total'=>$weekTotal,
            'week_kumulatif'=>$kumulatif
        ])
        ->make(true);

}

private function resolveBuildPlanData($event): array
{
    $canEdit = auth()->user()->can('lihat daftar proyek');
    $buildPlans = BuildPlans::query()
        ->where('event_id', $event->id)
        ->with('weeks:id,build_plan_id,week_no,plan_percent')
        ->orderBy('category_order')
        ->orderBy('uraian_order')
        ->orderBy('item_order')
        ->get();

    $buildPlans->each(function ($item) {
        $item->progress_map = $item->weeks->keyBy('week_no');
    });

    $groupedPlans = $buildPlans
        ->sortBy([
            ['category_order', 'asc'],
            ['uraian_order', 'asc'],
            ['item_order', 'asc'],
        ])
        ->groupBy('category_order')
        ->map(function ($items) {
            return [
                'category_name' => $items->first()->category_name,
                'uraians'       => $items
                    ->groupBy('uraian_order')
                    ->map(function ($rows) {
                        return [
                            'uraian_name' => $rows->first()->uraian_name,
                            'items'       => $rows->sortBy('item_order')->values(),
                        ];
                    }),
            ];
        });

    return compact('buildPlans', 'groupedPlans', 'canEdit');
}
// Route: GET /events/{event}/build-process-data
public function buildProcessPartial(Event $event)
{
    $event->load([
        'city',
        'weeklyPlans',
        'buildItems.weeklyProgresses',
        'buildItems.tambahan.weeklyProgresses',
        'dailyReports.works.rabProcessItem',
        'dailyReports.workers.worker.user',
        'dailyReports.materials',
    ]);

    $buildData = $this->resolveBuildData($event);

    $formData = $this->formData(
        $event,
        8,
        3
    );

    $canEdit = auth()->user()->can('lihat daftar proyek');
    $isReadOnly = !$canEdit;

    return view(
        'events.steps.build-process',
        array_merge(
            $buildData,
            $formData,
            compact(
                'event',
                'canEdit',
                'isReadOnly'
            )
        )
    );
}
}