    public function create(Request $request)
    {
        $project = null;

        if ($request->has('project_id')) {
            $project = $this->loadFullProject($request->project_id);
        }

        $activeStep = $this->getCurrentStep($project);
        $surveyInvoice = Invoice::where('project_id', $project?->id)
            ->where('invoice_type', 'survey')
            ->latest()
            ->first();

        $surveyApproved = $surveyInvoice && $surveyInvoice->status === 'approved';
        $isFreeSurvey = !$surveyInvoice && $project?->levels
            ->firstWhere('level_order', 3)?->is_started;

        $surveyWaiting  = $surveyInvoice && $surveyInvoice->status === 'waiting_approval';
        $surveyRejected = $surveyInvoice && $surveyInvoice->status === 'rejected';

        $invoiceDp = Invoice::where('project_id', $project?->id)
            ->where('invoice_type', Invoice::TYPE_DP)
            ->first();
        $invoiceRab = Invoice::where('project_id', $project?->id)
            ->where('invoice_type', Invoice::TYPE_RAB)
            ->first();
        $invoiceBuild = InvoiceBuild::where('project_id', $project?->id)
            ->where('invoice_type', InvoiceBuild::TYPE_BUILD)
            ->first();

        if (
            $project &&
            $project->planning &&
            ($isFreeSurvey || $surveyApproved) &&
            $activeStep == 3
        ) {
            $activeStep = 4;
        }

        $map = $this->stepKeyMap();

        $timelineSteps = $project
            ? $project->levels
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
                ->values()
            : collect([]);

        $canEdit = auth()->user()->can('lihat daftar proyek'); 
        $weeks = $project->rab->job_duration ?? 0;
        $usedDates = BuildDailyReport::where('project_id', $project?->id)
            ->pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        $nextDate = Carbon::parse($project?->start_date);

        while (
            in_array($nextDate->format('Y-m-d'), $usedDates)
            && $nextDate->lte($project?->end_date)
        ) {
            $nextDate->addDay();
        }
        $reports = BuildDailyReport::where('project_id', $project?->id)
            ->orderBy('tanggal')
            ->get()
            ->groupBy('minggu');

        $buildItems = BuildProcessItem::query()
            ->where('project_id', $project?->id)
            ->with([
                'weeklyProgresses:id,build_process_item_id,week_no,volume,just_kurang,just_tambah,just_baru',
                'tambahan.weeklyProgresses:id,build_process_item_id,week_no,volume,just_kurang,just_tambah,just_baru',
            ])
            ->get();
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
                     'category_id' => $items->first()->category_order,
                    'category_name' => $items->first()->category_name,

                    'uraians' => $items
                        ->groupBy('uraian_order')
                        ->map(function ($rows) {

                            return [
                                'uraian_name' => $rows->first()->uraian_name,

                                'items' => $rows
                                    ->sortBy('item_order')
                                    ->values()
                            ];
                        })
                ];
            });

        $buildPlans = BuildPlans::query()
            ->where('project_id', $project?->id)
            ->with([
                'weeks:id,build_plan_id,week_no,plan_percent'
            ])
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

                    'category_name' =>
                        $items->first()->category_name,

                    'uraians' => $items
                        ->groupBy('uraian_order')
                        ->map(function ($rows) {

                            return [

                                'uraian_name' =>
                                    $rows->first()->uraian_name,

                                'items' => $rows
                                    ->sortBy('item_order')
                                    ->values()

                            ];

                        })

                ];

            });
        return view('projects.create', array_merge(
            $this->formData($project),
            compact('project', 'timelineSteps', 'activeStep', 'surveyInvoice', 'nextDate', 'groupedPlans', 'buildPlans',
        'surveyApproved', 'usedDates', 'reports', 'groupedItems', 'buildItems',
        'isFreeSurvey', 'surveyWaiting', 'surveyRejected', 'invoiceDp', 'invoiceRab', 'invoiceBuild', 'canEdit', 'weeks')
        ));
    }

        private function loadFullProject($projectId)
    {
        return Project::with([
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
        ])->findOrFail($projectId);
    }

        private function formData($project = null, $merge = [])
    {
        return array_merge([
            'employees'     => \App\Models\Employee::with('user:id,fullname')->get(['id','user_id']),
            'customers'     => \App\Models\Customer::with('user:id,fullname')->get(['id','user_id']),
            'affiliators'   => \App\Models\Affiliator::with('user:id,fullname')->get(['id','user_id']),
            'workers'   => \App\Models\Worker::with('user:id,fullname')->get(['id','user_id']),
            'provinces'     =>  Province::all(),
            'designPackages' => \App\Models\DesignPackage::orderBy('name')->orderBy('price_meter')->get(),
            'rabPackages' => \App\Models\RabPackage::orderBy('name')->orderBy('price_meter')->get(),
            'jobCategories' => JobCategory::orderBy('kode_urut')->orderBy('nama_pekerjaan')->get(),
            'rabProcesses' => RabProcess::whereHas('project', function ($q) use ($project) {
                    $q->where('customer_id', $project?->customer_id);
                })->get(),
            'rabs' => RabProcessItem::whereHas('rab.project', function ($q) use ($project) {
                $q->where('customer_id', $project?->customer_id);
            })
            ->orderBy('job_name')
            ->get(),
            'projectStatus' => [
                1 => 'Proses',
                2 => 'Revisi',
                3 => 'Butuh Persetujuan',
                4 => 'Selesai'
            ]
        ], $merge);
    }



    <?php

/**
 * ============================================================
 * REFACTORED ProjectController — method create()
 * ============================================================
 *
 * Perubahan utama:
 * 1. Eager loading conditional berdasarkan activeStep & projectType
 * 2. formData() hanya load dropdown yang relevan
 * 3. Hapus query duplikat — pakai data dari eager load
 * 4. Pisah logic ke helper methods agar mudah dibaca
 *
 * CARA PAKAI:
 * - Replace method create(), loadFullProject(), dan formData()
 *   di ProjectController kamu dengan kode di bawah ini.
 * - Tambahkan helper methods baru yang disediakan.
 * - Pastikan semua use/import sudah sesuai.
 * ============================================================
 */

// ─────────────────────────────────────────────────────────────
// MAIN METHOD
// ─────────────────────────────────────────────────────────────

public function create(Request $request)
{
    $project = null;

    if ($request->has('project_id')) {
        // ━━━ Phase 1: Load project MINIMAL untuk hitung step ━━━
        $project = $this->loadBaseProject($request->project_id);
    }

    $activeStep  = $this->getCurrentStep($project);
    $projectType = $project?->project_type;

    // ━━━ Phase 2: Lazy-load relasi tambahan sesuai kebutuhan ━━━
    if ($project) {
        $extra = $this->resolveExtraRelations($activeStep, $projectType);
        if (!empty($extra)) {
            $project->load($extra);
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
        'project', 'activeStep', 'canEdit'
    ));

    // ── Survey data (step >= 3) ──
    if ($activeStep >= 3 && $project) {
        $surveyData = $this->resolveSurveyData($project, $activeStep);
        $viewData   = array_merge($viewData, $surveyData);

        // Mungkin activeStep berubah jadi 4
        $activeStep = $viewData['activeStep'];
    }

    // ── Timeline (butuh activeStep final) ──
    $viewData['timelineSteps'] = $this->buildTimelineSteps($project, $activeStep);

    // ── Invoice data (step >= 6) ──
    if ($activeStep >= 6 && $project) {
        $viewData = array_merge($viewData, $this->resolveInvoiceData($project));
    }

    // ── Build data (type 3, step >= 8) ──
    if ($projectType == 3 && $activeStep >= 8 && $project) {
        $viewData = array_merge($viewData, $this->resolveBuildData($project));
    }

    // ── Build plans (type 3, step >= 8) ──
    if ($projectType == 3 && $activeStep >= 8 && $project) {
        $viewData = array_merge($viewData, $this->resolveBuildPlanData($project));
    }

    return view('projects.create', array_merge(
        $this->formData($project, $activeStep, $projectType),
        $viewData
    ));
}

// ─────────────────────────────────────────────────────────────
// PHASE 1: LOAD PROJECT MINIMAL
// ─────────────────────────────────────────────────────────────

/**
 * Load project dengan relasi MINIMAL yang dibutuhkan untuk:
 * - Menentukan activeStep (levels)
 * - Menampilkan header/info dasar (customer, employee)
 * - Cek keberadaan planning & rab duration
 */
private function loadBaseProject($projectId)
{
    return Project::with([
        'customer.user',
        'employee',
        'levels.employees',
        'planning',
        'invoices',                           // Untuk surveyInvoice & invoiceFinal check di Blade
        'rab:id,project_id,job_duration',     // Hanya ambil field yang perlu
    ])->findOrFail($projectId);
}

// ─────────────────────────────────────────────────────────────
// PHASE 2: RESOLVE EXTRA RELATIONS
// ─────────────────────────────────────────────────────────────

/**
 * Tentukan relasi tambahan yang perlu di-load
 * berdasarkan step dan project type saat ini.
 */
private function resolveExtraRelations(int $activeStep, ?int $projectType): array
{
    $relations = [];

    // Step >= 2: Konsultasi
    if ($activeStep >= 2) {
        $relations[] = 'consultation.items';
    }

    // Step >= 4: Survei
    if ($activeStep >= 4) {
        $relations[] = 'survey.items';
    }

    // Step >= 5: Offer
    if ($activeStep >= 5) {
        $relations[] = 'offer.items';

        // RAB offer detail — hanya type 2
        if ($projectType == 2) {
            $relations[] = 'offer.rab.items.category';
        }
    }

    // RAB detail — type 2, step >= 7
    if ($projectType == 2 && $activeStep >= 7) {
        $relations[] = 'rab.categories.uraians.items.category';
    }

    // Build items & daily reports — HANYA type 3, step >= 8
    if ($projectType == 3 && $activeStep >= 8) {
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

// ─────────────────────────────────────────────────────────────
// PHASE 3: RESOLVE VIEW DATA — HELPER METHODS
// ─────────────────────────────────────────────────────────────

/**
 * Hitung data terkait survei.
 * Mengembalikan array variabel untuk view.
 */
private function resolveSurveyData($project, int $activeStep): array
{
    // Pakai relasi invoices yang sudah di-eager-load
    $surveyInvoice = $project->invoices
        ->where('invoice_type', 'survey')
        ->sortByDesc('created_at')
        ->first();

    $surveyApproved = $surveyInvoice?->status === 'approved';
    $surveyWaiting  = $surveyInvoice?->status === 'waiting_approval';
    $surveyRejected = $surveyInvoice?->status === 'rejected';
    $isFreeSurvey   = !$surveyInvoice
        && $project->levels->firstWhere('level_order', 3)?->is_started;

    // Auto-advance ke step 4
    if (
        $project->planning
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

/**
 * Data invoice — hanya dipanggil saat step >= 6
 */
private function resolveInvoiceData($project): array
{
    // Pakai relasi invoices yang sudah di-eager-load
    $invoiceDp = $project->invoices
        ->where('invoice_type', Invoice::TYPE_DP)
        ->first();

    $invoiceRab = $project->invoices
        ->where('invoice_type', Invoice::TYPE_RAB)
        ->first();

    // InvoiceBuild mungkin tabel terpisah, perlu query
    $invoiceBuild = InvoiceBuild::where('project_id', $project->id)
        ->where('invoice_type', InvoiceBuild::TYPE_BUILD)
        ->first();

    return compact('invoiceDp', 'invoiceRab', 'invoiceBuild');
}

/**
 * Data build items & daily reports — hanya type 3, step >= 8.
 * PENTING: Pakai data dari eager-load, BUKAN query ulang.
 */
private function resolveBuildData($project): array
{
    $weeks = $project->rab?->job_duration ?? 0;

    // ✅ Pakai relasi yang sudah di-eager-load (bukan query baru)
    $usedDates = $project->dailyReports
        ->pluck('tanggal')
        ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
        ->toArray();

    // Hitung next date
    $nextDate = Carbon::parse($project->start_date);
    while (
        in_array($nextDate->format('Y-m-d'), $usedDates)
        && $nextDate->lte($project->end_date)
    ) {
        $nextDate->addDay();
    }

    // ✅ Pakai relasi yang sudah di-eager-load
    $reports = $project->dailyReports
        ->sortBy('tanggal')
        ->groupBy('minggu');

    // ✅ Pakai relasi yang sudah di-eager-load (bukan BuildProcessItem::query())
    $buildItems = $project->buildItems;
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

    return compact(
        'weeks', 'usedDates', 'nextDate', 'reports',
        'buildItems', 'groupedItems'
    );
}

/**
 * Data build plans — hanya type 3, step >= 8.
 * BuildPlans TIDAK di-eager-load di project (tabel terpisah),
 * jadi query terpisah dipertahankan tapi hanya jika dibutuhkan.
 */
private function resolveBuildPlanData($project): array
{
    $buildPlans = BuildPlans::query()
        ->where('project_id', $project->id)
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

    return compact('buildPlans', 'groupedPlans');
}

/**
 * Build timeline steps dari project levels.
 */
private function buildTimelineSteps($project, int $activeStep): \Illuminate\Support\Collection
{
    if (!$project) {
        return collect([]);
    }

    $map = $this->stepKeyMap();

    return $project->levels
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

// ─────────────────────────────────────────────────────────────
// FORM DATA — CONDITIONAL DROPDOWN LOADING
// ─────────────────────────────────────────────────────────────

/**
 * Load dropdown/reference data hanya yang dibutuhkan
 * berdasarkan step dan project type saat ini.
 */
private function formData($project = null, int $activeStep = 1, ?int $projectType = null, array $merge = []): array
{
    $data = [
        'projectStatus' => [
            1 => 'Proses',
            2 => 'Revisi',
            3 => 'Butuh Persetujuan',
            4 => 'Selesai',
        ],
    ];

    // ── Dropdown utama: employees, customers, affiliators, provinces ──
    // Hanya dibutuhkan saat create/edit proyek (step awal)
    // atau saat form konsultasi/planning
    if ($activeStep <= 4) {
        $data['employees']   = \App\Models\Employee::with('user:id,fullname')->get(['id', 'user_id']);
        $data['customers']   = \App\Models\Customer::with('user:id,fullname')->get(['id', 'user_id']);
        $data['affiliators'] = \App\Models\Affiliator::with('user:id,fullname')->get(['id', 'user_id']);
        $data['provinces']   = Province::all();
    }

    // ── Workers — hanya untuk build (type 3) saat tahap pengerjaan ──
    if ($projectType == 3 && $activeStep >= 8) {
        $data['workers'] = \App\Models\Worker::with('user:id,fullname')->get(['id', 'user_id']);
    }

    // ── Design packages — hanya type 1, saat step penawaran ──
    if ($projectType == 1 && $activeStep >= 5) {
        $data['designPackages'] = \App\Models\DesignPackage::orderBy('name')
            ->orderBy('price_meter')->get();
    }

    // ── RAB packages — hanya type 2, saat step penawaran ──
    if ($projectType == 2 && $activeStep >= 5) {
        $data['rabPackages'] = \App\Models\RabPackage::orderBy('name')
            ->orderBy('price_meter')->get();
    }

    // ── Job categories — hanya type 3, saat step penawaran/build ──
    if ($projectType == 3 && $activeStep >= 5) {
        $data['jobCategories'] = JobCategory::orderBy('kode_urut')
            ->orderBy('nama_pekerjaan')->get();
    }

    // ── RAB processes & items — hanya jika edit form offer aktif ──
    // Ini bisa di-lazy-load via AJAX di masa depan
    if ($activeStep >= 5 && $project?->customer_id) {
        $data['rabProcesses'] = RabProcess::whereHas('project', function ($q) use ($project) {
            $q->where('customer_id', $project->customer_id);
        })->get();

        $data['rabs'] = RabProcessItem::whereHas('rab.project', function ($q) use ($project) {
            $q->where('customer_id', $project->customer_id);
        })
        ->orderBy('job_name')
        ->get();
    }

    return array_merge($data, $merge);
}


// ─────────────────────────────────────────────────────────────
// CATATAN MIGRASI
// ─────────────────────────────────────────────────────────────

/**
 * Method lama yang DIHAPUS:
 * - loadFullProject()  → diganti loadBaseProject() + resolveExtraRelations()
 * - formData() lama    → diganti formData() baru dengan conditional loading
 *
 * Method BARU yang ditambahkan:
 * - loadBaseProject()
 * - resolveExtraRelations()
 * - resolveSurveyData()
 * - resolveInvoiceData()
 * - resolveBuildData()
 * - resolveBuildPlanData()
 * - buildTimelineSteps()
 *
 * Method yang TIDAK BERUBAH (tetap pakai yang existing):
 * - getCurrentStep()
 * - stepKeyMap()
 *
 * PENTING — Cek Blade Variables:
 * Pastikan semua variabel yang dipakai di blade sudah ada di $defaults.
 * Jika ada variabel lain yang dipakai di @include() sub-views,
 * tambahkan ke $defaults dengan nilai default yang aman.
 */