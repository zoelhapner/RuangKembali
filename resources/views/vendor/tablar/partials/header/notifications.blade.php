@auth
    <div class="nav-item dropdown d-md-flex me-3">
        <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
           aria-label="Show notifications">

            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                 stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path
                    d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
            </svg>
            @if(auth()->user()->unreadNotifications->count())
                <span id="notification-count" class="badge bg-red text-white">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </a>
        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Last updates</h3>
                </div>
                <div id="notif-header" class="d-flex justify-content-between align-items-center px-3 py-2">
                    <strong>Notifikasi</strong>
                    @if(auth()->user()->unreadNotifications->count())
                        <button id="mark-all-btn" onclick="markAllAsRead()" class="btn btn-sm btn-outline-dark">
                            Tandai semua dibaca
                        </button>
                    @endif
                </div>
                <div id="notification-list" class="list-group list-group-flush list-group-hoverable">

                    @forelse(auth()->user()->unreadNotifications as $notif)
                        <a href="{{ $notif->data['url'] }}"
                        class="list-group-item list-group-item-action notification-item"
                        onclick="markAsRead('{{ $notif->id }}', this)">

                            <div class="row align-items-start">
                                <div class="col-auto pt-1">
                                    <span class="status-dot status-dot-animated bg-red d-block"></span>
                                </div>

                                <div class="col">
                                    <div class="text-body fw-semibold">
                                        {{ $notif->data['message'] }}
                                    </div>
                                    <div class="text-muted small">
                                        <span>{{ $notif->data['project_name'] }}</span>
                                        <span>{{ $notif->created_at->diffForHumans(['short' => true]) }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="list-group-item text-center text-muted">
                            Tidak ada notifikasi baru
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
@endauth

@push('js')
<script>
function markAsRead(notificationId, el) {
    fetch('/notifications/' + notificationId + '/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(() => {

        const item = el.closest('.notification-item');
        if (item) item.remove();

        const badge = document.querySelector('#notification-count');

        let count = 0;
        if (badge) {
            count = parseInt(badge.textContent || 0);
            count = Math.max(count - 1, 0);

            if (count === 0) {
                badge.remove();
            } else {
                badge.textContent = count;
            }
        }

        // ✅ kalau sudah habis → hapus tombol mark all
        if (count === 0) {
            const btn = document.querySelector('#mark-all-btn');
            if (btn) btn.remove();
        }

        const container = document.querySelector('#notification-list');
        if (container && container.children.length === 0) {
            container.innerHTML = `
                <div class="list-group-item text-center text-muted">
                    Tidak ada notifikasi baru
                </div>
            `;
        }
    });
}
</script>

<script>
function markAllAsRead() {
    fetch(`/notifications/read-all`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(res => {

        if (res.status === 'ok') {

            // 1️⃣ Hapus badge notif
            document.querySelectorAll('#notification-count')
                .forEach(el => el.remove());

            // 2️⃣ Hapus semua notif item
            document.querySelectorAll('.notification-item')
                .forEach(el => el.remove());

            // 3️⃣ Hapus header notif + tombol
            const header = document.getElementById('notif-header');
            if (header) header.remove();

            // 4️⃣ Tampilkan kosong
            const container = document.getElementById('notification-list');
            if (container) {
                container.innerHTML = `
                    <div class="list-group-item text-center text-muted">
                        Tidak ada notifikasi baru
                    </div>
                `;
            }

        }
    });
}
</script>

@endpush