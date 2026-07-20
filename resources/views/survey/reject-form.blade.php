<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tolak Rencana Survei</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-body p-5">

                    <h3 class="fw-bold mb-4 text-danger">
                        Tolak Rencana Survei
                    </h3>

                    <p class="mb-4">
                        Silakan berikan alasan atau catatan penolakan agar tim kami dapat melakukan revisi.
                    </p>

                    <form method="POST"
                          action="{{ route('survey.invoice.reject', [$invoice->id, $invoice->approval_token]) }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Catatan Penolakan <span class="text-danger">*</span>
                            </label>
                            <textarea name="reject_note"
                                      class="form-control"
                                      rows="5"
                                      required
                                      placeholder="Contoh: Jadwal survei belum sesuai, mohon dijadwalkan ulang."></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="javascript:history.back()" class="btn btn-secondary">
                                Kembali
                            </a>

                            <button type="submit" class="btn btn-danger">
                                Kirim Penolakan
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
