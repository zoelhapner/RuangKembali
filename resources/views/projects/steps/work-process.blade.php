@php
$tasks = \App\Models\ProjectTask::where('project_id', $project->id)
    ->orderByRaw('COALESCE(parent_task_id, id)')
    ->orderBy('revision_number')
    ->get();
$colors = [
    'tunda'       => 'secondary',
    'proses'      => 'warning',
    'konfirmasi'  => 'info',
    'revisi'      => 'danger',
    'selesai'     => 'success',
];

    $isReadOnly = !$canEdit;
@endphp

    @foreach($tasks->groupBy('category') as $category => $tasks)
    @php
        $categoryKey = \Illuminate\Support\Str::slug($category);
    @endphp

    <h3 class="fw-bold mt-4">{{ $category }}</h3>
    @if(!$isReadOnly)
    <div class="mb-3">
        <form method="POST" action="{{ route('projects.tasks.sync', $project->id) }}">
            @csrf
            <button type="submit" class="btn btn-outline-warning">
                <i class="ti ti-refresh"></i>
                Sync Task dari Offer
            </button>
        </form>
    </div>
    @endif

        <table class="table table-bordered align-middle table-fixed">
<colgroup>
    <col style="width: 28%">
    <col style="width: 20%">
    <col style="width: 20%">
    <col style="width: 22%">
    <col style="width: 10%">
</colgroup>
            <thead>
                <tr>
                    <th>Uraian Pekerjaan</th>
                    <th>PIC</th>
                    <th>Dokumen</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody data-category="{{ $categoryKey }}">
                @foreach($tasks as $task)
                        <tr
                            data-task-row="{{ $task->id }}"
                            data-task-id="{{ $task->id }}"
                            data-parent-id="{{ $task->parent_task_id }}"
                            data-is-revision="{{ $task->parent_task_id ? 1 : 0 }}"
                        >
                        <td>
                            <div class="task-name">
                                {{ $task->task_name }}
                            </div>
                            @if($task->revision_number > 0)
                                <span class="badge bg-danger mt-1">
                                    Revisi {{ $task->revision_number }}
                                </span>
                            @endif
                            @if($task->reject_note)
                                <div class="text-muted small mt-1">
                                    <i class="ti ti-note"></i>
                                    {{ $task->reject_note }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if(!$isReadOnly)
                            <select class="form-select assign-employee"
                                    data-task="{{ $task->id }}">
                                <option value="">-- Pilih --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}"
                                        @selected($task->employee_id == $emp->id)>
                                        {{ $emp->user->fullname }}
                                    </option>
                                @endforeach
                            </select>
                            @else
                                <div class="form-control bg-light">
                                    {{ $task->employee?->user?->fullname ?? '-' }}
                                </div>
                            @endif
                        </td>
                        <td class="task-document" data-task="{{ $task->id }}">
                            @if($task->files->count())
                                @foreach($task->files as $file)
                                    <div class="doc-cell">
                                        <div class="doc-actions">
                                            <a href="{{ route('tasks.files.view', $file) }}"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-eye"></i> Lihat File
                                            </a>
                                            @if(!$isReadOnly)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger btn-delete-file"
                                                        data-file="{{ $file->id }}">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="doc-meta">
                                        <strong title="{{ $file->uploader_name }}">
                                            {{ $file->uploader_short_name }}
                                        </strong>
                                            <br>
                                            {{ $file->created_at->timezone('Asia/Jakarta') }}
                                        </div>

                                    </div>
                                @endforeach
                            @elseif(!$isReadOnly)
                                <button class="btn btn-sm btn-dark btn-upload"
                                        data-task="{{ $task->id }}">
                                    <i class="ti ti-upload"></i> Upload
                                </button>
                                <input type="file"
                                    class="d-none upload-input"
                                    data-task="{{ $task->id }}">
                            @endif
                        </td>
                        <td class="task-action" data-task="{{ $task->id }}">

                            {{-- ✅ TASK SELESAI --}}
                            @if($task->status === 'selesai')
                                <div class="action-cell">
                                    <span class="text-success">
                                        <i class="ti ti-check" style="font-size:18px"></i>
                                    </span>
                                    <div class="action-meta text-muted small">
                                        Disetujui oleh
                                        <strong>{{ optional($task->approvedBy)->short_name ?? 'Sistem' }}</strong><br>
                                        {{ optional($task->approved_at)?->timezone('Asia/Jakarta') }}
                                    </div>
                                </div>

                            {{-- ❌ TASK DITOLAK / REVISI --}}
                            @elseif($task->status === 'revisi')
                                <div class="action-cell">
                                    <span class="text-danger">
                                        <i class="ti ti-x" style="font-size:18px"></i>
                                    </span>
                                    <div class="action-meta text-muted small">
                                        Ditolak oleh
                                        <strong>{{ optional($task->rejectedBy)->short_name ?? 'Sistem' }}</strong><br>
                                        {{ optional($task->rejected_at)?->timezone('Asia/Jakarta') }}
                                    </div>
                                </div>

                            {{-- ⏳ TASK PROSES / KONFIRMASI --}}
                            @else
                                <div class="action-cell">
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-success btn-approve-task"
                                                data-task="{{ $task->id }}">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-open-reject"
                                                data-task="{{ $task->id }}">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif

                        </td>
                        <td class="task-status" data-task="{{ $task->id }}">
                            <span class="badge bg-{{ $colors[$task->status] }}">
                                {{ strtoupper($task->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="modal fade" id="globalRejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Hasil Pekerjaan</h5>
                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <textarea id="globalRejectNote"
                                class="form-control"
                                placeholder="Catatan revisi..."
                                rows="4"
                                required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="button"
                                class="btn btn-danger"
                                id="btnConfirmReject">
                            Tolak & Minta Revisi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@push('js')
<script>
    const isReadOnly = @json($isReadOnly);
</script>
<script>
document.querySelectorAll('.assign-employee').forEach(select => {
    select.addEventListener('change', function () {
        const taskId = this.dataset.task;
        const employeeId = this.value;

        fetch(`/tasks/${taskId}/assign`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ employee_id: employeeId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'ok') {
                this.closest('tr')
                    .querySelector('.task-status')
                    .innerHTML = badge('proses');
            }
        });
    });
});

function badge(status) {
    const map = {
        proses: 'warning',
        konfirmasi: 'info',
        selesai: 'success',
        revisi: 'danger'
    };
    return `<span class="badge bg-${map[status]}">${status.toUpperCase()}</span>`;
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-upload');
        if (!btn) return;

        e.preventDefault();

        const taskId = btn.dataset.task;
        const input = document.querySelector(
            `.upload-input[data-task="${taskId}"]`
        );

        if (input) input.click();
    });

    document.addEventListener('change', function (e) {

        const input = e.target.closest('.upload-input');
        if (!input) return;

        const taskId = input.dataset.task;
        const file   = input.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        fetch(`/tasks/${taskId}/upload`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error('Upload gagal');
            return res.json();
        })
        .then(data => {

            const docCell = document.querySelector(
                `.task-document[data-task="${taskId}"]`
            );

            if (docCell) {
                docCell.innerHTML = `
                    <div class="doc-cell">
                        <div class="doc-actions">
                            <a href="${data.file.url}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-eye"></i> Lihat File
                            </a>

                            <button type="button"
                                    class="btn btn-sm btn-outline-danger btn-delete-file"
                                    data-file="${data.file.id}">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>

                        <div class="doc-meta">
                            Di-upload oleh <strong>${data.file.uploaded_by ?? 'Sistem'}</strong><br>
                            ${data.file.uploaded_at ?? '-'}
                        </div>
                    </div>

                    <input type="file"
                           class="d-none upload-input"
                           data-task="${taskId}">
                `;
            }

            const statusCell = document.querySelector(
                `.task-status[data-task="${taskId}"] span`
            );

            if (statusCell) {
                statusCell.className = 'badge bg-info';
                statusCell.innerText = 'KONFIRMASI';
            }

            const actionCell = document.querySelector(
                `.task-action[data-task="${taskId}"]`
            );
            const row = document.querySelector(
                `tr[data-task-row="${taskId}"]`
            );

            if (row && row.dataset.isRevision === '1') return;

            if (actionCell) {
                actionCell.innerHTML = `
                    <div class="action-cell">
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-success btn-approve-task"
                                    data-task="${taskId}">
                                <i class="ti ti-check"></i>
                            </button>

                            <button class="btn btn-sm btn-danger btn-open-reject"
                                    data-task="${taskId}">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                `;
            }

        })
        .catch(err => alert(err.message));
    });
});
</script>


<script>
document.addEventListener('click', function (e) {

    const deleteBtn = e.target.closest('.btn-delete-file');
    if (!deleteBtn) return;

    e.preventDefault();

    const fileId = deleteBtn.dataset.file;
    const wrapper = deleteBtn.closest('.doc-cell');
    const taskDocument = deleteBtn.closest('.task-document');

    if (!fileId || !wrapper || !taskDocument) return;

    if (!confirm('Hapus file ini?')) return;

    fetch(`/tasks/files/${fileId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => {
        if (!res.ok) throw new Error();
        return res.json();
    })
    .then(() => {

        wrapper.remove();

        const taskId = taskDocument.dataset.task;
        const badge = document.querySelector(
            `.task-status[data-task="${taskId}"] span`
        );

        if (badge) {
            badge.className = 'badge bg-warning';
            badge.innerText = 'PROSES';
        }

        taskDocument.innerHTML = `
            <button class="btn btn-sm btn-dark btn-upload"
                    data-task="${taskId}">
                <i class="ti ti-upload"></i> Upload
            </button>

            <input type="file"
                   class="d-none upload-input"
                   data-task="${taskId}">
        `;
    })
    .catch(() => alert('Gagal menghapus file'));
});
</script>

<script>
document.addEventListener('click', function (e) {

    const btn = e.target.closest('.btn-approve-task');
    if (!btn) return;

    e.preventDefault();

    const taskId = btn.dataset.task;

    fetch(`/tasks/${taskId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => {
                throw new Error(err.message || 'Gagal menyetujui tugas');
            });
        }
        return res.json(); // ⬅️ DI SINI data didapat
    })
    .then(data => {

        // update status badge
        const badge = document.querySelector(
            `.task-status[data-task="${taskId}"] span`
        );
        if (badge) {
            badge.className = 'badge bg-success';
            badge.innerText = 'SELESAI';
        }

        // update kolom aksi + footnote
        btn.closest('.task-action').innerHTML = `
            <div class="action-cell">
                <span class="text-success">
                    <i class="ti ti-check" style="font-size:18px"></i>
                </span>

                <div class="action-meta text-muted small">
                    Disetujui oleh <strong>${data.approved_by ?? 'Sistem'}</strong><br>
                    ${data.approved_at ?? '-'}
                </div>
            </div>
        `;
    })
    .catch(err => alert(err.message));
});
</script>

<script>
let currentRejectTaskId = null;

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-open-reject');
    if (!btn) return;

    currentRejectTaskId = btn.dataset.task;

    const noteEl = document.getElementById('globalRejectNote');
    noteEl.value = '';
    noteEl.focus();

    new bootstrap.Modal(
        document.getElementById('globalRejectModal')
    ).show();
});

const btnConfirmReject = document.getElementById('btnConfirmReject');
if (btnConfirmReject) {
    btnConfirmReject.addEventListener('click', function () {

    if (!currentRejectTaskId) return;

    const noteEl = document.getElementById('globalRejectNote');
    const note   = noteEl.value.trim();

    if (!note) {
        alert('Catatan revisi wajib diisi');
        return;
    }

    fetch(`/tasks/${currentRejectTaskId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reject_note: note })
    })
    .then(res => res.json())
    .then(data => {

        /* === UPDATE BADGE STATUS TASK LAMA === */
        const oldBadge = document.querySelector(
            `.task-status[data-task="${currentRejectTaskId}"] span`
        );
        if (oldBadge) {
            oldBadge.className = 'badge bg-danger';
            oldBadge.innerText = 'REVISI';
        }

        /* === 🔥 UPDATE TASK NAME + CATATAN REVISI (TASK LAMA) === */
        const taskNameCell = document.querySelector(
            `tr[data-task-row="${currentRejectTaskId}"] td:first-child`
        );

        if (taskNameCell) {
            taskNameCell.innerHTML = renderTaskNameCell({
                name: taskNameCell.querySelector('.task-name')?.innerText,
                revision: null,
                reject_note: data.rejected.reject_note
            });
        }

        /* === UPDATE KOLOM AKSI TASK LAMA === */
        const actionCell = document.querySelector(
            `.task-action[data-task="${currentRejectTaskId}"]`
        );
        if (actionCell) {
            actionCell.innerHTML = `
                <div class="action-cell">
                    <span class="text-danger">
                        <i class="ti ti-x" style="font-size:18px"></i>
                    </span>
                    <div class="action-meta text-muted small">
                        Ditolak oleh <strong>${data.rejected.rejected_by}</strong><br>
                        ${data.rejected.rejected_at}
                    </div>
                </div>
            `;
        }

        const tbody = document.querySelector(
            `tbody[data-category="${data.revision.category_key}"]`
        );

        if (tbody) {
            // cari semua row yg parent-nya sama
            const relatedRows = tbody.querySelectorAll(
                `tr[data-task-id="${data.revision.parent_task_id}"],
                tr[data-parent-id="${data.revision.parent_task_id}"]`
            );

            if (relatedRows.length) {
                // sisipkan setelah baris terakhir (parent / revisi terakhir)
                relatedRows[relatedRows.length - 1]
                    .insertAdjacentHTML('afterend', renderRevisionRow(data.revision));
            } else {
                // fallback
                tbody.insertAdjacentHTML(
                    'beforeend',
                    renderRevisionRow(data.revision)
                );
            }
        }

        bootstrap.Modal.getInstance(
            document.getElementById('globalRejectModal')
        ).hide();

        currentRejectTaskId = null;
    })
    .catch(() => alert('Gagal meminta revisi'));
});
}
</script>

<script>
function renderRevisionRow(task) {
    return `
    <tr 
        data-task-id="${task.id}"
        data-parent-id="${task.parent_task_id}"
        data-revision="${task.revision}"
        data-is-revision="1"
        class="table-warning"
    >
        <td>
            ${renderTaskNameCell(task)}
        </td>

        <td>
            <span class="text-muted">
                ${task.employee ?? '-'}
            </span>
        </td>

        <td class="task-document" data-task="${task.id}">
            ${!isReadOnly ? `
                <button class="btn btn-sm btn-dark btn-upload"
                        data-task="${task.id}">
                    <i class="ti ti-upload"></i> Upload
                </button>
                <input type="file"
                       class="d-none upload-input"
                       data-task="${task.id}">
            ` : `
                <span class="text-muted">Menunggu...</span>
            `}
        </td>

        <td class="task-action" data-task="${task.id}">
            <div class="action-cell">
                <span class="text-danger">
                    <i class="ti ti-x" style="font-size:18px"></i>
                </span>
                <div class="action-meta text-muted small">
                    Ditolak oleh <strong>${task.rejected_by ?? 'Sistem'}</strong><br>
                    ${task.rejected_at ?? '-'}
                </div>
            </div>
        </td>

        <td class="task-status" data-task="${task.id}">
            <span class="badge bg-danger">REVISI</span>
        </td>
    </tr>`;
}
</script>
<script>
function renderTaskNameCell(task) {

    const revisionBadge = task.revision && task.revision > 0
        ? `<span class="badge bg-danger mt-1">
                Revisi ${task.revision}
           </span>`
        : '';

    const rejectNote = task.reject_note
        ? `<div class="text-muted small mt-1">
                <i class="ti ti-note"></i>
                ${task.reject_note}
           </div>`
        : '';

    return `
        <div class="task-name">
            ${task.name}
        </div>
        ${revisionBadge}
        ${rejectNote}
    `;
}
</script>

@endpush