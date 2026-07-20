<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Persetujuan Rencana Survei</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-body text-center p-5">

                    @if($status === 'approved')
                        <div class="mb-4">
                            <i class="ti ti-check text-success" style="font-size:48px"></i>
                        </div>
                        <h3 class="fw-bold text-success">Rencana Survei Disetujui</h3>
                        <p class="mt-3">
                            Terima kasih. Rencana survei telah <strong>disetujui</strong> dan
                            tim kami akan segera menindaklanjuti.
                        </p>

                    @elseif($status === 'rejected')
                        <div class="mb-4">
                            <i class="ti ti-x text-danger" style="font-size:48px"></i>
                        </div>
                        <h3 class="fw-bold text-danger">Rencana Survei Ditolak</h3>
                        <p class="mt-3">
                            Rencana survei telah <strong>ditolak</strong>.
                            Tim kami akan melakukan penyesuaian sesuai catatan Anda.
                        </p>

                        @if(!empty($note))
                            <div class="alert alert-warning mt-4 text-start">
                                <strong>Catatan Penolakan:</strong><br>
                                {{ $note }}
                            </div>
                        @endif

                    @else
                        <div class="mb-4">
                            <i class="ti ti-alert-triangle text-warning" style="font-size:48px"></i>
                        </div>
                        <h3 class="fw-bold text-warning">Akses Tidak Valid</h3>
                        <p class="mt-3">
                            Tautan ini tidak valid atau sudah tidak berlaku.
                        </p>
                    @endif

                    <hr class="my-4">

                    <p class="text-muted small mb-0">
                        Antosa Architect © {{ date('Y') }}
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
