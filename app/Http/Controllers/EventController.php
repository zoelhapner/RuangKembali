<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
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

        ->addColumn('province_name', fn($row) => $row->province->name ?? '-')
        ->addColumn('city_name', fn($row) => $row->city->name ?? '-')
        ->addColumn('district_name', fn($row) => $row->district->name ?? '-')
        ->addColumn('sub_district_name', fn($row) => $row->subDistrict->name ?? '-')
        ->addColumn('postal_code', fn($row) => $row->postalCode->postal_code ?? '-')
        ->addColumn('customer', fn($row) => $row->customer?->user?->fullname ?? '-')
        ->addColumn('employee', fn($row) => $row->employee?->user?->fullname ?? '-')
        ->addColumn('affiliator', fn($row) => $row->affiliator?->user?->fullname ?? '-')
        ->addColumn('event_type', fn($row) => $this->readableEventType($row->event_type))
        ->addColumn('start_date', fn($row) => $row->start_date ? Carbon::parse($row->start_date)->format('d/m/Y') : '-')

        ->addColumn('event_status', function ($row) use ($statusLabel) {

            $label = $statusLabel[$row->event_status] ?? 'Tidak Diketahui';

            $color = match ($row->event_status) {
                1 => 'info',
                2 => 'danger',
                3 => 'warning',
                4 => 'success',
                default => 'secondary'
            };

            return '<span class="badge bg-' . $color . '">' . $label . '</span>';
        })

        ->addColumn('current_level', function ($row) {
            $current = $row->levels
                ->where('is_completed', false)
                ->sortBy('level_order')
                ->first();

            // Jika semua selesai
            if (!$current) {
                return '<span class="badge bg-success">Selesai</span>';
            }

            $url = route('events.continue', $row->id);

            return '<a href="'.$url.'" class="badge bg-primary" style="cursor:pointer;">
                        '.$current->level_name.'
                    </a>';
        })
        ->editColumn('event_name', function ($row) {
                    $url = route('events.continue', $row->id);
                    $name = Str::title($row->event_name ?? '-');
                    return '<a href="'.$url.'">'.e($name).'</a>';
                })

        // Tombol Aksi
        ->addColumn('action', function ($event) {
            $buttons = '';
            if (auth()->user()->can('hapus data proyek')) {
                $buttons .= '<button data-id="' . $event->id . '" 
                            class="btn btn-icon btn-sm btn-dark delete-events">
                            <i class="ti ti-trash"></i></button>';
            }
            return $buttons;
        })

        ->rawColumns(['current_level', 'action', 'event_status', 'event_name'])
        ->make(true);
    }

    return view('events.index');
}

    private function readableEventType($value)
    {
        return match ((int) $value) {
            1 => 'Desain',
            2 => 'RAB',
            3 => 'Build',
            default => '-',
        };
    }

public function create(Request $request)
{
    $event = null;
    if ($request->has('event_id')) {
        $event = $this->loadBaseEvent($request->event_id);
    }
    $activeStep  = $this->getCurrentStep($event);
    $eventType = $event?->event_type;
    if ($event) {
        $extra = $this->resolveExtraRelations($activeStep, $eventType);
        if (!empty($extra)) {
            $event->load($extra);
        }
    }
    // ━━━ Phase 3: Siapkan data view — conditional per section ━━━
    $canEdit = auth()->user()->can('lihat daftar proyek');
    // Default values untuk variabel yang dipakai di Blade
    // Ini mencegah "undefined variable" di view
    $defaults = [
        'surveyInvoice'  => null,
        'surveyApproved' => false,
        'surveyWaiting'  => false,
        'surveyRejected' => false,
        'isFreeSurvey'   => false,
        'invoiceDp'      => null,
        'invoiceRab'     => null,
        'invoiceBuild'   => null,
        'weeks'          => 0,
        'usedDates'      => [],
        'nextDate'       => now(),
        'reports'        => collect(),
        'buildItems'     => collect(),
        'groupedItems'   => collect(),
        'buildPlans'     => collect(),
        'groupedPlans'   => collect(),
    ];
    $viewData = array_merge($defaults, compact(
        'event', 'activeStep', 'canEdit'
    ));
    // ── Survey data (step >= 3) ──
    if ($activeStep >= 3 && $event) {
        $surveyData = $this->resolveSurveyData($event, $activeStep);
        $viewData   = array_merge($viewData, $surveyData);
        // Mungkin activeStep berubah jadi 4
        $activeStep = $viewData['activeStep'];
    }
    // ── Timeline (butuh activeStep final) ──
    $viewData['timelineSteps'] = $this->buildTimelineSteps($event, $activeStep);
    // ── Invoice data (step >= 6) ──
    if ($activeStep >= 6 && $event) {
        $viewData = array_merge($viewData, $this->resolveInvoiceData($event));
    }

    if ($eventType == 3 && $activeStep >= 8 && $event) {
        $viewData = array_merge($viewData, $this->resolveBuildData($event));
    }
    // ── Build plans (type 3, step >= 8) ──
    if ($eventType == 3 && $activeStep >= 8 && $event) {
        $viewData = array_merge($viewData, $this->resolveBuildPlanData($event));
    }
    return view('events.create', array_merge(
        $this->formData($event, $activeStep, $eventType),
        $viewData
    ));
}
private function loadBaseEvent($eventId)
{
    return Event::with([
        'customer.user',
        'employee',
        'levels.employees',
        'planning',
        'invoices',                           
        'rab:id,event_id,job_duration',     
    ])->findOrFail($eventId);
}
private function resolveExtraRelations(int $activeStep, ?int $eventType): array
{
    $relations = [];

    if ($activeStep >= 2) {
        $relations[] = 'consultation.items';
    }

    if ($activeStep >= 4) {
        $relations[] = 'survey.items';
    }

    if ($activeStep >= 5) {
        $relations[] = 'offer.items';

        if ($eventType == 2) {
            $relations[] = 'offer.rab.items.category';
        }
    }

    if ($eventType == 2 && $activeStep >= 7) {
        $relations[] = 'rab.categories.uraians.items.category';
    }

    if ($eventType == 3 && $activeStep >= 8) {
        $relations = array_merge($relations, [
            'buildItems.jobCategory',
            'buildItems.weeklyProgresses',
            'buildItems.tambahan.weeklyProgresses',
            'dailyReports.works.rabProcessItem',
            'dailyReports.workers.worker.user',
            'dailyReports.materials',
        ]);
    }

    return $relations;
}
private function resolveSurveyData($event, int $activeStep): array
{
    $surveyInvoice = $event->invoices
        ->where('invoice_type', 'survey')
        ->sortByDesc('created_at')
        ->first();

    $surveyApproved = $surveyInvoice?->status === 'approved';
    $surveyWaiting  = $surveyInvoice?->status === 'waiting_approval';
    $surveyRejected = $surveyInvoice?->status === 'rejected';
    $isFreeSurvey   = !$surveyInvoice && $event->levels->firstWhere('level_order', 3)?->is_started;

    if (
        $event->planning
        && ($isFreeSurvey || $surveyApproved)
        && $activeStep == 3
    ) {
        $activeStep = 4;
    }

    return compact(
        'surveyInvoice', 'surveyApproved', 'surveyWaiting',
        'surveyRejected', 'isFreeSurvey', 'activeStep'
    );
}
private function resolveInvoiceData($event): array
{
    $invoiceDp = $event->invoices
        ->where('invoice_type', Invoice::TYPE_DP)
        ->first();

    $invoiceRab = $event->invoices
        ->where('invoice_type', Invoice::TYPE_RAB)
        ->first();

    $invoiceBuild = InvoiceBuild::where('event_id', $event->id)
        ->where('invoice_type', InvoiceBuild::TYPE_BUILD)
        ->first();

    return compact('invoiceDp', 'invoiceRab', 'invoiceBuild');
}
private function resolveBuildData($event): array
{
    $weeks = $event->rab?->job_duration ?? 0;

    // ✅ Pakai relasi yang sudah di-eager-load (bukan query baru)
    $usedDates = $event->dailyReports
        ->pluck('tanggal')
        ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
        ->toArray();

    // Hitung next date
    $nextDate = Carbon::parse($event->start_date);
    while (
        in_array($nextDate->format('Y-m-d'), $usedDates)
        && $nextDate->lte($event->end_date)
    ) {
        $nextDate->addDay();
    }

    // ✅ Pakai relasi yang sudah di-eager-load
    $reports = $event->dailyReports
        ->sortBy('tanggal')
        ->groupBy('minggu');

    // ✅ Pakai relasi yang sudah di-eager-load (bukan BuildProcessItem::query())
    $buildItems = $event->buildItems;
    $buildItems->each(function ($item) {
        $item->progress_map = $item->weeklyProgresses->keyBy('week_no');
        $item->tambahan->each(function ($sub) {
            $sub->progress_map = $sub->weeklyProgresses->keyBy('week_no');
        });
    });

    $groupedItems = $buildItems
        ->whereNull('parent_id')
        ->sortBy([
            ['category_order', 'asc'],
            ['uraian_order', 'asc'],
            ['item_order', 'asc'],
        ])
        ->groupBy('category_order')
        ->map(function ($items) {
            return [
                'category_id'   => $items->first()->category_order,
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
    $weeklyReports = WeeklyReport::where('event_id', $event->id)
        ->get()
        ->keyBy('minggu');
    return compact('weeks', 'usedDates', 'nextDate', 'reports', 'buildItems', 'groupedItems', 'weeklyReports');
}

    public function store(EventRequest $request)
{
    abort_if(auth()->user()->cannot('lihat daftar proyek'), 403);

    $event = DB::transaction(function () use ($request) {

        $event = Event::create($request->validated());

        $event->generateLevels();

        return $event;
    });

    $event->load(['employee.user', 'customer.user']);

    $event = 'event_created';
    $cfg   = config("event_events.event_created");

    if (!$cfg) {
        throw new \Exception("Config event_events.$event not found");
    }

    EventNotifier::notifyUsers(
        [auth()->user()],
        EventNotifier::makePayload($event, [
            'type'    => $event,
            'role'    => 'created_self',
            'title'   => $cfg['title'],
            'message' => $cfg['message']['created_self'],
            'url'     => route('events.create', ['event_id' => $event->id]),
        ])
    );

    if ($event->employee?->user && $event->employee->user->id !== auth()->id()) {
        EventNotifier::notifyUsers(
            [$event->employee->user],
            EventNotifier::makePayload($event, [
                'type'    => $event,
                'role'    => 'assigned',
                'title'   => $cfg['title'],
                'message' => $cfg['message']['assigned'],
                'url'     => route('events.create', ['event_id' => $event->id]),
            ])
        );
    }

    $directors = User::role('Direktur')->get();

    EventNotifier::notifyUsers(
        $directors,
        EventNotifier::makePayload($event, [
            'type'    => $event,
            'role'    => 'director',
            'title'   => $cfg['title'],
            'message' => $cfg['message']['director'],
            'url'     => route('events.create', ['event_id' => $event->id]),
        ]),
        exceptUserId: auth()->id()
    );

    if ($event->customer?->user) {
        EventNotifier::notifyUsers(
            [$event->customer->user],
            EventNotifier::makePayload($event, [
                'type'    => $event,
                'role'    => 'customer',
                'title'   => $cfg['title'],
                'message' => $cfg['message']['customer'],
                'url'     => route('events.create', ['event_id' => $event->id]),
            ])
        );
    }

        return redirect()
            ->route('events.create', ['event_id' => $event->id])
            ->with('success', 'Event berhasil dibuat.');
}

    private function getCurrentStep($event)
    {
        if (!$event) return 1;

        $current = $event->levels
            ->where('is_completed', false)
            ->sortBy('level_order')
            ->first();

        return $current ? $current->level_order + 1 : 9;
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