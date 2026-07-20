<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\GeneralHelper;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectLevel;
use App\Models\ProjectTask;
use App\Services\ProjectNotifier;
use App\Services\InvoiceNumberGenerator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function invoiceDp(Project $project)
    {
        abort_if(!$project->offer, 404);

        Carbon::setLocale('id');

        $invoice = DB::transaction(function () use ($project) {

            $offer = $project->offer;
            $newAmount = $offer->grand_total * 0.7;

            $invoice = Invoice::where('project_id', $project->id)
                ->where('invoice_type', Invoice::TYPE_DP)
                ->lockForUpdate()
                ->latest()
                ->first();

            if (!$invoice) {

                $invoice = Invoice::create([
                    'project_id'     => $project->id,
                    'invoice_type'   => Invoice::TYPE_DP,
                    'invoice_number' => InvoiceNumberGenerator::generate(Invoice::TYPE_DP),
                    'invoice_date'   => now(),
                    'amount'         => $newAmount,
                    'status'         => Invoice::STATUS_WAITING,
                ]);

            } else {

                // update amount jika total offer berubah
                if ($invoice->amount != $newAmount) {
                    $invoice->update([
                        'amount' => $newAmount,
                    ]);
                }
            }

            if (!$invoice->invoice_dp_downloaded_at) {
                $invoice->update([
                    'invoice_dp_downloaded_at' => now(),
                ]);
            }

            return $invoice->fresh();
        });

        $offer = $project->offer;

        return Pdf::loadView('invoice.dp', [
            'invoice' => $invoice,
            'project' => $project,
            'offer'   => $offer,
        ])->stream('Invoice-DP-' . $project->project_name . '.pdf');
    }

    public function invoiceFinal(Project $project)
    {
        abort_if(!$project->offer, 404);

        Carbon::setLocale('id');

        $invoice = DB::transaction(function () use ($project) {

            $offer = $project->offer;
            $newAmount = $offer->grand_total * 0.3;

            $invoice = Invoice::where('project_id', $project->id)
                ->where('invoice_type', Invoice::TYPE_FINAL)
                ->lockForUpdate()
                ->first();

            if (!$invoice) {

                $invoice = Invoice::create([
                    'project_id'     => $project->id,
                    'invoice_type'   => Invoice::TYPE_FINAL,
                    'invoice_number' => InvoiceNumberGenerator::generate(Invoice::TYPE_FINAL),
                    'invoice_date'   => now(),
                    'amount'         => $newAmount,
                    'status'         => Invoice::STATUS_WAITING,
                ]);

            } else {

                // sync ulang jika total offer berubah
                if ($invoice->amount != $newAmount) {
                    $invoice->update([
                        'amount' => $newAmount,
                    ]);
                }
            }

            if (!$invoice->downloaded_at) {
                $invoice->update([
                    'downloaded_at' => now(),
                ]);
            }

            return $invoice->fresh();
        });

        $offer = $project->offer;

        return Pdf::loadView('invoice.final', compact('invoice', 'project', 'offer'))
            ->stream('Invoice-Pelunasan-' . $project->project_name . '.pdf');
    }

    public function invoiceSurvey(Project $project)
    {
        abort_if(!$project->planning, 404);
        Carbon::setLocale('id');

        $invoice = DB::transaction(function () use ($project) {

            $invoice = Invoice::where('project_id', $project->id)
                ->where('invoice_type', Invoice::TYPE_SURVEY)
                ->where('status', '!=', 'obsolete') // 🔥 filter invoice lama
                ->lockForUpdate()
                ->latest() // 🔥 ambil terbaru
                ->first();

            if (!$invoice) {
                $invoice = Invoice::create([
                    'project_id'     => $project->id,
                    'invoice_type'   => Invoice::TYPE_SURVEY,
                    'invoice_number' => InvoiceNumberGenerator::generate(Invoice::TYPE_SURVEY),
                    'invoice_date'   => now(),
                    'amount' => $project->latestSurveyInvoice()?->amount ?? 0,
                    'status'         => Invoice::STATUS_WAITING,
                ]);
            }

            return $invoice;
        });

        return Pdf::loadView('invoice.survey', [
            'invoice' => $invoice,
            'project' => $project,
            'planning'=> $project->planning,
        ])->stream('Invoice-Survey-' . $project->project_name . '.pdf');
    }

    public function invoiceRab(Project $project)
    {
        abort_if(!$project->offer, 404);
        abort_if($project->project_type != 2, 403); // 🔒 khusus RAB

        Carbon::setLocale('id');

        $invoice = DB::transaction(function () use ($project) {

            $invoice = Invoice::where('project_id', $project->id)
                ->where('invoice_type', Invoice::TYPE_RAB)
                ->lockForUpdate()
                ->first();

            if (!$invoice) {
                $invoice = Invoice::create([
                    'project_id'     => $project->id,
                    'invoice_type'   => Invoice::TYPE_RAB,
                    'invoice_number' => InvoiceNumberGenerator::generate(Invoice::TYPE_RAB),
                    'invoice_date'   => now(),
                    'amount'         => $project->offer->grand_total,
                    'status'         => Invoice::STATUS_WAITING,
                ]);
            }

            if (!$invoice->downloaded_at) {
                $invoice->update([
                    'downloaded_at' => now(),
                ]);
            }

            return $invoice;
        });

        $offer = $project->offer;

        $data = [
            'invoice_number' => $invoice->invoice_number,
            'invoice_date'   => $invoice->invoice_date->translatedFormat('d F Y'),
            'client_name'    => $offer->contact_name,
            'client_address' => optional($project->customer->user)->address,
            'client_phone'   => optional($project->customer->user)->phone,
            'project_name'   => $project->project_name,
            'total_amount'   => $offer->grand_total,
        ];

        return Pdf::loadView('invoice.rab', $data)
            ->setPaper('A4', 'portrait')
            ->stream('Invoice-RAB-' . $project->project_name . '.pdf');
    }

    public function approve(Project $project)
    {
        abort_if(
            $project->customer->user_id !== auth()->id()
            && auth()->user()->cannot('lihat daftar proyek'),
            403
        );

        DB::transaction(function () use ($project) {

            $invoice = Invoice::where('project_id', $project->id)
                ->where('invoice_type', Invoice::TYPE_DP)
                ->firstOrFail();

            abort_if(!$invoice, 404, 'Invoice DP belum dibuat.');

            $invoice->update([
                'invoice_dp_approved_at' => now(),
                'status' => 'dp',
                'approved_at' => now(),
            ]);

            $offer = $project->offer;
            abort_if(!$offer, 404, 'Offer belum tersedia.');

            // ===============================
            // SYNC PROJECT TASK DARI OFFER
            // ===============================

            $existingTasks = ProjectTask::where('project_id', $project->id)
                ->pluck('task_name')
                ->toArray();

            foreach ($offer->items as $item) {

                if (!in_array($item->item_name, $existingTasks)) {

                    ProjectTask::create([
                        'project_id' => $project->id,
                        'offer_id'   => $offer->id,
                        'offer_item_id' => $item->id,
                        'category'   => $item->category,
                        'task_name'  => $item->item_name,
                    ]);

                }
            }

            // ===============================
            // UPDATE LEVEL
            // ===============================

            ProjectLevel::where([
                'project_id'  => $project->id,
                'level_order' => 6,
            ])->update(['is_completed' => true]);

            ProjectLevel::where([
                'project_id'  => $project->id,
                'level_order' => 7,
            ])->update(['is_started' => true]);

        });

        $event = 'dp_paid';
        $cfg   = config("project_events.dp_paid");

        if (!$cfg) {
            throw new \Exception("Config project_events.$event not found");
        }

        ProjectNotifier::notifyUsers(
            [$project->createdBy ?? auth()->user()],
            ProjectNotifier::makePayload($project, [
                'type'    => $event,
                'role'    => 'Super-Admin',
                'title'   => $cfg['title'],
                'message' => $cfg['message']['Super-Admin'],
                'url'     => route('projects.create', ['project_id' => $project->id]),
            ])
        );

        if ($project->customer?->user) {
            ProjectNotifier::notifyUsers(
                [$project->customer->user],
                ProjectNotifier::makePayload($project, [
                    'type'    => $event,
                    'role'    => 'customer',
                    'title'   => $cfg['title'],
                    'message' => $cfg['message']['customer'],
                    'url'     => route('projects.create', ['project_id' => $project->id]),
                ])
            );
        }

        return redirect()
            ->route('projects.create', ['project_id' => $project->id])
            ->with('success', 'Invoice DP selesai, lanjut ke tahap pengerjaan.');
    }

    public function approveFinal(Project $project)
    {
        abort_if(
            $project->customer->user_id !== auth()->id()
            && auth()->user()->cannot('lihat daftar proyek'),
            403
        );

        DB::transaction(function () use ($project) {

            $invoice = Invoice::where('project_id', $project->id)
                ->where('invoice_type', Invoice::TYPE_FINAL)
                ->firstOrFail();

            $invoice->update([
                'status'      => Invoice::STATUS_APPROVED,
                'approved_at' => now(),
            ]);

            // Level pengerjaan selesai
            ProjectLevel::where([
                'project_id'  => $project->id,
                'level_order' => 8,
            ])->update(['is_completed' => true]);

            // Level selesai proyek
            ProjectLevel::where([
                'project_id'  => $project->id,
                'level_order' => 9,
            ])->update(['is_started' => true]);
        });

        $event = 'final_paid';
        $cfg   = config("project_events.final_paid");

        if (!$cfg) {
            throw new \Exception("Config project_events.$event not found");
        }

        ProjectNotifier::notifyUsers(
            [$project->createdBy ?? auth()->user()],
            ProjectNotifier::makePayload($project, [
                'type'    => $event,
                'role'    => 'Super-Admin',
                'title'   => $cfg['title'],
                'message' => $cfg['message']['Super-Admin'],
                'url'     => route('projects.create', ['project_id' => $project->id]),
            ])
        );

        if ($project->customer?->user) {
            ProjectNotifier::notifyUsers(
                [$project->customer->user],
                ProjectNotifier::makePayload($project, [
                    'type'    => $event,
                    'role'    => 'customer',
                    'title'   => $cfg['title'],
                    'message' => $cfg['message']['customer'],
                    'url'     => route('projects.create', ['project_id' => $project->id]),
                ])
            );
        }

        return redirect()
            ->route('projects.create', ['project_id' => $project->id])
            ->with('success', 'Pelunasan disetujui. Proyek selesai.');
    }

    public function approveRab(Project $project)
    {
        abort_if(
            $project->customer->user_id !== auth()->id()
            && auth()->user()->cannot('lihat daftar proyek'),
            403
        );

        DB::transaction(function () use ($project) {

            $invoice = Invoice::where('project_id', $project->id)
                ->where('invoice_type', Invoice::TYPE_RAB)
                ->firstOrFail();

            $invoice->update([
                'status'      => Invoice::STATUS_PAID,
                'approved_at' => now(),
            ]);

            // Level pengerjaan selesai
            ProjectLevel::where([
                'project_id'  => $project->id,
                'level_order' => 5,
            ])->update(['is_completed' => true]);

            // Level selesai proyek
            ProjectLevel::where([
                'project_id'  => $project->id,
                'level_order' => 6,
            ])->update(['is_started' => true]);
        });

        $event = 'rab_paid';
        $cfg   = config("project_events.rab_paid");

        if (!$cfg) {
            throw new \Exception("Config project_events.$event not found");
        }

        ProjectNotifier::notifyUsers(
            [$project->createdBy ?? auth()->user()],
            ProjectNotifier::makePayload($project, [
                'type'    => $event,
                'role'    => 'Super-Admin',
                'title'   => $cfg['title'],
                'message' => $cfg['message']['Super-Admin'],
                'url'     => route('projects.create', ['project_id' => $project->id]),
            ])
        );

        if ($project->customer?->user) {
            ProjectNotifier::notifyUsers(
                [$project->customer->user],
                ProjectNotifier::makePayload($project, [
                    'type'    => $event,
                    'role'    => 'customer',
                    'title'   => $cfg['title'],
                    'message' => $cfg['message']['customer'],
                    'url'     => route('projects.create', ['project_id' => $project->id]),
                ])
            );
        }

        return redirect()
            ->route('projects.create', ['project_id' => $project->id])
            ->with('success', 'RAB disetujui. Lanjut ke tahap pengerjaan.');
    }

    public function surveyPlanningPdf(Project $project)
    {
        $invoice = Invoice::with([
            'project.customer.user',
            'project.levels.employees',
            'project.planning.province',
            'project.planning.city',
            'project.planning.district',
            'project.planning.subDistrict',
            'project.planning.postalCode',
        ])
        ->where('project_id', $project->id)
        ->where('invoice_type', 'survey')
        ->where('amount', '>', 0)
        ->latest()
        ->firstOrFail();

        $planningEmployees = $project->levels()
        ->where('level_order', 2)
        ->with('employees')
        ->get()
        ->flatMap->employees;

        return Pdf::loadView('pdf.planning-survey', [
            'invoice'  => $invoice,
            'offer_number' => $this->generateOfferNumber(),
            'project'  => $project,
            'planning' => $project->planning,
            'planningEmployees'  => $planningEmployees,
        ])->stream('rencana-survei.pdf');
    }

    public function approveSurvey(Invoice $invoice, $token)
{
    $project = $invoice->project;

    abort_if(
        !$project || !$project->customer || (
            $project->customer->user_id !== auth()->id()
            && auth()->user()->cannot('lihat daftar proyek')
        ),
        403
    );

    abort_if(
        $invoice->approval_token !== $token ||
        $invoice->invoice_type !== 'survey',
        403
    );

    if ($invoice->status === 'approved') {
        return view('survey.approval-result', [
            'status' => 'approved',
            'message' => 'Rencana survei sudah disetujui sebelumnya.'
        ]);
    }

    DB::transaction(function () use ($invoice, $project) {

        $invoice->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approve_by_name' => $project->customer->user->fullname ?? 'Customer',
            'approved_ip' => request()->ip(),
        ]);

        $planningLevel = $project->levels()->where('level_name', 'Rencana Survei')->first();
        if ($planningLevel && !$planningLevel->is_completed) {
            $planningLevel->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }

        $surveyLevel = $project->levels()->where('level_name', 'Survei')->first();
        if ($surveyLevel && !$surveyLevel->is_started) {
            $surveyLevel->update([
                'is_started' => true,
                'started_at' => now(),
            ]);
        }
    });

    $event = 'planning_paid';
    $cfg   = config("project_events.planning_paid");

    if (!$cfg) {
        throw new \Exception("Config project_events.$event not found");
    }

    ProjectNotifier::notifyUsers(
        [$project->createdBy ?? auth()->user()],
        ProjectNotifier::makePayload($project, [
            'type'    => $event,
            'role'    => 'Super-Admin',
            'title'   => $cfg['title'],
            'message' => $cfg['message']['Super-Admin'],
            'url'     => route('projects.create', ['project_id' => $project->id]),
        ])
    );

    $surveyLevel = $project->levels()->where('level_name', 'Survei')->first();
    if ($surveyLevel) {
        $users = $surveyLevel->employees()->with('user')->get()->pluck('user');

        ProjectNotifier::notifyUsers(
            $users,
            ProjectNotifier::makePayload($project, [
                'type'    => $event,
                'role'    => 'assigned',
                'title'   => $cfg['title'],
                'message' => $cfg['message']['assigned'],
                'url'     => route('projects.create', ['project_id' => $project->id]),
            ])
        );
    }

    if ($project->customer?->user) {
        ProjectNotifier::notifyUsers(
            [$project->customer->user],
            ProjectNotifier::makePayload($project, [
                'type'    => $event,
                'role'    => 'customer',
                'title'   => $cfg['title'],
                'message' => $cfg['message']['customer'],
                'url'     => route('projects.create', ['project_id' => $project->id]),
            ])
        );
    }

    return view('survey.approval-result', [
        'status' => 'approved',
        'message' => 'Rencana survei berhasil disetujui. Tim survei dapat mulai bekerja.'
    ]);
}

    public function rejectSurveyForm(Invoice $invoice, $token)
    {
        abort_if(
            $invoice->approval_token !== $token ||
            $invoice->invoice_type !== 'survey',
            403
        );

        return view('survey.reject-form', compact('invoice', 'token'));
    }

    public function rejectSurvey(Request $request, Invoice $invoice, $token)
    {
        $project = $invoice->project;

        abort_if(
            $invoice->approval_token !== $token ||
            $invoice->invoice_type !== 'survey',
            403
        );

        $request->validate([
            'reject_note' => 'required|min:5'
        ]);

        $invoice->update([
            'status'       => 'rejected',
            'reject_note'  => $request->reject_note,
            'approved_at'  => null,
            'rejected_at'  => now(),
        ]);
        $event = 'planning_rejected';
        $cfg   = config("project_events.planning_rejected");

        ProjectNotifier::notifyUsers(
            [$project->createdBy ?? auth()->user()],
            ProjectNotifier::makePayload($project, [
                'type'    => $event,
                'role'    => 'Super-Admin',
                'title'   => $cfg['title'],
                'message' => $cfg['message']['Super-Admin'],
                'url'     => route('projects.create', ['project_id' => $project->id]),
            ])
        );

        $surveyLevel = $project->levels()->where('level_name', 'Rencana Survei')->first();
        if ($surveyLevel) {
            $users = $surveyLevel->employees()->with('user')->get()->pluck('user');

            ProjectNotifier::notifyUsers(
                $users,
                ProjectNotifier::makePayload($project, [
                    'type'    => $event,
                    'role'    => 'assigned',
                    'title'   => $cfg['title'],
                    'message' => $cfg['message']['assigned'],
                    'url'     => route('projects.create', ['project_id' => $project->id]),
                ])
            );
        }

        if ($project) {

            $surveyLevel = $project->levels()
                ->where('level_name', 'Survei')
                ->first();

            if ($surveyLevel) {
                $surveyLevel->update([
                    'is_started'   => false,
                    'is_completed' => false,
                    'started_at'   => null,
                    'completed_at' => null,
                ]);
            }

            $planningLevel = $project->levels()
                ->where('level_name', 'Rencana Survei')
                ->first();

            if ($planningLevel) {
                $planningLevel->update([
                    'is_started'   => true,
                    'is_completed' => false,
                ]);
            }
        }

        return redirect()
            ->route('projects.create', ['project_id' => $project->id])
            ->with('error', 'Rencana survei ditolak oleh customer. Silakan perbaiki data.');
    }

    private function generateOfferNumber()
    {
        $tahunFull = date('Y');        // 2026
        $tahun = date('y');            // 26
        $bulan = date('n');            // 1-12
        $romawiBulan = \App\Helpers\GeneralHelper::bulanRomawi($bulan);

        // Ambil nomor terakhir di tahun ini saja
        $lastOffer = \App\Models\Offer::whereYear('offer_date', $tahunFull)
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastOffer) {
            // PH/DSN/26/I/001 → ambil 001
            $explode = explode('/', $lastOffer->offer_number);
            $lastNumber = intval(end($explode)) + 1;
        } else {
            // Kalau belum ada di tahun ini → mulai dari 1
            $lastNumber = 1;
        }

        // Format ke 3 digit: 1 → 001
        $nomorUrut = str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

        return "PH/SRV/$tahun/$romawiBulan/$nomorUrut";
    }
}