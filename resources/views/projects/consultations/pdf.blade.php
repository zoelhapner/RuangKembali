<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Konsultasi - {{ $consultation->project->project_code }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
        .logo { width:220px; }
        .table { width:100%; border-collapse: collapse; margin-top:10px; }
        .table th, .table td { border:1px solid #000; padding:6px; vertical-align:top; }
        .no-border { border:none; }
        .sign { height:80px; }
        .footer { margin-top:20px; display:flex; justify-content:space-between; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <img src="{{ public_path('images/logo.png') }}" alt="logo" style="height:60px;">
            <div>Antosa Architect</div>
        </div>
        <div style="text-align:right">
            <strong>FORM KONSULTASI</strong><br>
            Project: {{ $consultation->project->project_code }} - {{ $consultation->project->project_name }}
        </div>
    </div>

    <table class="table" style="border:0;">
        <tr>
            <td class="no-border" style="border:0; padding:0;">
                <table style="width:100%; border-collapse: collapse;">
                    <tr>
                        <td style="width:25%"><strong>Nama</strong></td>
                        <td style="width:75%">{{ $consultation->contact_name ?? $consultation->project->customer->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td>{{ $consultation->project->project_location }}</td>
                    </tr>
                    <tr>
                        <td><strong>No HP</strong></td>
                        <td>{{ $consultation->contact_phone ?? $consultation->project->customer->user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Ukuran Tanah/Bangunan</strong></td>
                        <td>{{ $consultation->site_area ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th width="6%">No</th>
                <th>Uraian</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consultation->items as $i => $item)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->remark }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="width:45%; text-align:center;">
            <div>Konsultan Desain</div>
            @if($consultation->consultant_signature)
                <img src="{{ storage_path('app/public/' . $consultation->consultant_signature) }}" class="sign" alt="ttd">
            @else
                <div style="height:80px"></div>
            @endif
            <div>_________________________</div>
        </div>

        <div style="width:45%; text-align:center;">
            <div>Client</div>
            @if($consultation->client_signature)
                <img src="{{ storage_path('app/public/' . $consultation->client_signature) }}" class="sign" alt="ttd">
            @else
                <div style="height:80px"></div>
            @endif
            <div>_________________________</div>
        </div>
    </div>
</body>
</html>
