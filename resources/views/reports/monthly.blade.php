<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan IMO</title>
    <style>
        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
        }

        h1, h2, p {
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #111827;
            margin-bottom: 18px;
            padding-bottom: 12px;
            text-align: center;
        }

        .meta, .summary {
            margin-bottom: 14px;
            width: 100%;
        }

        .summary td {
            border: 1px solid #d1d5db;
            padding: 8px;
            width: 33.33%;
        }

        table.data {
            border-collapse: collapse;
            width: 100%;
        }

        table.data th, table.data td {
            border: 1px solid #d1d5db;
            padding: 6px;
            vertical-align: top;
        }

        table.data th {
            background: #f3f4f6;
            font-weight: bold;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Integrated Monitoring Operation</h1>
        <h2>Stasiun Ngawi</h2>
        <p>Laporan Bulanan {{ now()->month($month)->translatedFormat('F') }} {{ $year }}</p>
    </div>

    <table class="meta">
        <tr>
            <td>Filter Jabatan</td>
            <td>: {{ $jabatan ?: 'Semua Pegawai' }}</td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>: {{ now()->format('d M Y H:i') }}</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td><strong>Upload Lengkap</strong><br>{{ $summary['lengkap'] }}</td>
            <td><strong>Upload Belum Lengkap</strong><br>{{ $summary['belum_lengkap'] }}</td>
            <td><strong>Tidak Upload</strong><br>{{ $summary['tidak_upload'] }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIP</th>
                <th>Jabatan</th>
                <th>Tanggal Dinasan</th>
                <th>Tanggal Upload</th>
                <th>Status</th>
                <th>File PDF</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['user']->name }}</td>
                    <td>{{ $row['user']->nip ?: '-' }}</td>
                    <td>{{ $row['user']->jabatan }}</td>
                    <td>{{ $row['tanggal_dinasan']?->format('d M Y') ?: '-' }}</td>
                    <td>{{ $row['tanggal_upload']?->format('d M Y H:i') ?: '-' }}</td>
                    <td>{{ $row['status']->label() }}</td>
                    <td>{{ $row['upload']?->file_pdf ? 'Ada' : '-' }}</td>
                    <td>{{ $row['upload']?->dokumentasi?->count() ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data pada filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
