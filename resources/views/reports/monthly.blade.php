<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan IMO</title>
    <style>
        @page {
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #202124;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.35;
            margin: 0;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .page-break {
            page-break-after: always;
        }

        .sheet {
            border: 1px solid #d6d9de;
            height: 194mm;
            padding: 6mm;
            position: relative;
            width: 281mm;
        }

        .logos {
            border-bottom: 1px solid #d6d9de;
            margin-bottom: 3mm;
            padding-bottom: 3mm;
            width: 100%;
        }

        .logos td {
            text-align: center;
            vertical-align: middle;
        }

        .logo-danantara {
            height: 13mm;
            max-width: 43mm;
        }

        .logo-citar {
            height: 12mm;
            max-width: 48mm;
        }

        .logo-service {
            height: 14mm;
            max-width: 43mm;
        }

        .logo-kai {
            height: 13mm;
            max-width: 32mm;
        }

        .logo-fallback {
            color: #2c276e;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .meta-line {
            color: #5f6670;
            font-size: 8px;
            padding-top: 1mm;
            text-align: right;
        }

        .report-frame {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
        }

        .report-frame td {
            border: 1px solid #d6d9de;
            vertical-align: top;
        }

        .side-col {
            width: 21%;
        }

        .document-col {
            width: 45%;
        }

        .photo-col {
            width: 34%;
        }

        .side-table {
            border-collapse: collapse;
            height: 162mm;
            width: 100%;
        }

        .side-table th {
            background: #f5f6f8;
            border: 1px solid #d6d9de;
            color: #202124;
            font-size: 12px;
            letter-spacing: 0;
            padding: 2mm;
            text-align: center;
            width: 42%;
        }

        .side-table td {
            border: 1px solid #d6d9de;
            font-size: 13px;
            font-weight: 700;
            padding: 3mm 2mm;
            width: 58%;
        }

        .side-table .details {
            color: #4b5563;
            font-size: 9px;
            font-weight: 400;
            line-height: 1.45;
            padding-top: 2mm;
        }

        .section-title {
            border-bottom: 1px solid #d6d9de;
            color: #202124;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0;
            padding: 2mm;
            text-align: center;
        }

        .document-panel {
            height: 153mm;
            padding: 4mm;
        }

        .document-preview {
            background: #f8fafc;
            border: 1px solid #bfc5ce;
            height: 124mm;
            padding: 7mm 6mm;
            text-align: center;
        }

        .document-preview .label {
            color: #2c276e;
            font-size: 20px;
            font-weight: 800;
            margin-top: 32mm;
        }

        .document-preview .file-name {
            color: #374151;
            font-size: 10px;
            margin-top: 3mm;
            word-break: break-all;
        }

        .document-preview .empty {
            color: #9ca3af;
            font-size: 15px;
            font-weight: 700;
            margin-top: 38mm;
        }

        .document-info {
            color: #4b5563;
            font-size: 9px;
            margin-top: 3mm;
            width: 100%;
        }

        .document-info td {
            border: 0;
            padding: 0.6mm 0;
        }

        .document-info .key {
            width: 27mm;
        }

        .photo-panel {
            height: 153mm;
            padding: 4mm;
        }

        .photo-grid {
            border-collapse: collapse;
            height: 145mm;
            table-layout: fixed;
            width: 100%;
        }

        .photo-grid td {
            background: #f8fafc;
            border: 2mm solid #ffffff;
            height: 72.5mm;
            overflow: hidden;
            position: relative;
            text-align: center;
            vertical-align: middle;
        }

        .photo-grid img {
            height: 68mm;
            width: 100%;
        }

        .photo-empty {
            color: #a3aab5;
            font-size: 12px;
            font-weight: 700;
        }

        .summary-footer {
            bottom: 2mm;
            color: #6b7280;
            font-size: 8px;
            left: 6mm;
            position: absolute;
            right: 6mm;
        }

        .summary-footer table {
            width: 100%;
        }

        .summary-footer td {
            border: 0;
        }
    </style>
</head>
<body>
    @php
        $period = \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        $filterLabel = $jabatan ?: 'Semua Pegawai';
    @endphp

    @forelse ($rows as $row)
        <div class="sheet {{ ! $loop->last ? 'page-break' : '' }}">
            <table class="logos">
                <tr>
                    <td>
                        @if ($logos['danantara'])
                            <img class="logo-danantara" src="{{ $logos['danantara'] }}" alt="Danantara Indonesia">
                        @else
                            <span class="logo-fallback">Danantara Indonesia</span>
                        @endif
                    </td>
                    <td>
                        @if ($logos['citar'])
                            <img class="logo-citar" src="{{ $logos['citar'] }}" alt="CITAR">
                        @else
                            <span class="logo-fallback">CITAR</span>
                        @endif
                    </td>
                    <td>
                        @if ($logos['semakin_melayani'])
                            <img class="logo-service" src="{{ $logos['semakin_melayani'] }}" alt="Semakin Melayani">
                        @else
                            <span class="logo-fallback">Semakin Melayani</span>
                        @endif
                    </td>
                    <td>
                        @if ($logos['kai'])
                            <img class="logo-kai" src="{{ $logos['kai'] }}" alt="KAI">
                        @else
                            <span class="logo-fallback">KAI</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="meta-line" colspan="4">
                        Laporan Bulanan IMO Stasiun Ngawi - {{ $period }} - Filter: {{ $filterLabel }}
                    </td>
                </tr>
            </table>

            <table class="report-frame">
                <tr>
                    <td class="side-col">
                        <table class="side-table">
                            <tr>
                                <th>TANGGAL</th>
                                <td>{{ $row['tanggal_dinasan']?->translatedFormat('j F Y') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>KEGIATAN</th>
                                <td>
                                    {{ $row['kegiatan'] }}
                                    <div class="details">
                                        Nama: {{ $row['user']->name }}<br>
                                        NIP: {{ $row['user']->nip ?: '-' }}<br>
                                        Jabatan: {{ $row['user']->jabatan ?: '-' }}<br>
                                        Status: {{ $row['status']->label() }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="document-col">
                        <div class="section-title">SERAH TERIMA DINASAN</div>
                        <div class="document-panel">
                            <div class="document-preview">
                                @if ($row['file_pdf_name'])
                                    <div class="label">PDF SERAH TERIMA</div>
                                    <div class="file-name">{{ $row['file_pdf_name'] }}</div>
                                @else
                                    <div class="empty">BELUM ADA FILE</div>
                                @endif
                            </div>
                            <table class="document-info">
                                <tr>
                                    <td class="key">Tanggal upload</td>
                                    <td>: {{ $row['tanggal_upload']?->format('d M Y H:i') ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="key">File PDF</td>
                                    <td>: {{ $row['file_pdf_name'] ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td class="photo-col">
                        <div class="section-title">DOKUMENTASI</div>
                        <div class="photo-panel">
                            <table class="photo-grid">
                                @for ($rowIndex = 0; $rowIndex < 2; $rowIndex++)
                                    <tr>
                                        @for ($columnIndex = 0; $columnIndex < 2; $columnIndex++)
                                            @php
                                                $photoIndex = ($rowIndex * 2) + $columnIndex;
                                                $photo = $row['dokumentasi_images'][$photoIndex] ?? null;
                                            @endphp
                                            <td>
                                                @if ($photo)
                                                    <img src="{{ $photo['src'] }}" alt="{{ $photo['name'] }}">
                                                @else
                                                    <span class="photo-empty">FOTO {{ $photoIndex + 1 }}</span>
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="summary-footer">
                <table>
                    <tr>
                        <td>Lengkap: {{ $summary['lengkap'] }} | Belum Lengkap: {{ $summary['belum_lengkap'] }} | Tidak Upload: {{ $summary['tidak_upload'] }}</td>
                        <td style="text-align: right;">Dicetak: {{ $generatedAt->format('d M Y H:i:s') }} WIB</td>
                    </tr>
                </table>
            </div>
        </div>
    @empty
        <div class="sheet">
            <table class="logos">
                <tr>
                    <td><span class="logo-fallback">Danantara Indonesia</span></td>
                    <td><span class="logo-fallback">CITAR</span></td>
                    <td><span class="logo-fallback">Semakin Melayani</span></td>
                    <td><span class="logo-fallback">KAI</span></td>
                </tr>
            </table>

            <div class="document-preview">
                <div class="empty">TIDAK ADA DATA PADA FILTER INI</div>
                <div class="file-name">{{ $period }} - {{ $filterLabel }}</div>
            </div>
        </div>
    @endforelse
</body>
</html>
