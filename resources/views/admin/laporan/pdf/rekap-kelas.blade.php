<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Per Kelas</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            color: #111827;
        }

        h1,
        p {
            margin: 0;
        }

        .pdf-meta {
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #9ca3af;
            padding: 2px 4px;
            vertical-align: top;
        }

        thead th {
            background: #f3f4f6;
            text-align: center;
            font-weight: bold;
        }

        .font-semibold {
            font-weight: bold;
        }

        .text-gray-500,
        .text-gray-600,
        .text-gray-300 {
            color: #6b7280;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="pdf-meta">
        <h1>Rekap Pembayaran Per Kelas</h1>
        <p>Tahun Pelajaran: {{ $selectedTahunAjaran->nama ?? '-' }} | Tingkatan: {{ $selectedTingkatan ?: '-' }} |
            Kelas: {{ $selectedKelas ?: '-' }}</p>
    </div>

    @include('admin.laporan.partials.rekap-kelas-preview')
</body>

</html>
