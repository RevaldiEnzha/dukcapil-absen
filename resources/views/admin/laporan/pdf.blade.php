<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #003f83;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #003f83;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px 10px;
            text-align: center;
        }
        th {
            background-color: #f0f0f8;
            color: #003f83;
            font-weight: bold;
        }
        .text-left { text-align: left; }
        .text-red { color: #ba1a1a; font-weight: bold; }
        .text-blue { color: #003f83; font-weight: bold; }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #777;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN REKAPITULASI KEHADIRAN PEGAWAI NON-ASN</h2>
        <p><strong>Disdukcapil Kota Cirebon</strong></p>
        <p>Periode: <strong>{{ $namaBulan }} {{ $tahun }}</strong> (Total Hari Kerja: {{ $totalHariKerja }} Hari)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIK</th>
                <th width="25%" class="text-left">Nama Pegawai</th>
                <th width="10%">Hadir</th>
                <th width="10%">Terlambat</th>
                <th width="10%">Izin/Cuti</th>
                <th width="10%">Alpha</th>
                <th width="15%">% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawais as $index => $pegawai)
                @php
                    $hadir = $pegawai->hadir_count;
                    $terlambat = $pegawai->terlambat_count;
                    $izin = $pegawai->cuti_count;
                    
                    $alpha = max(0, $totalHariKerja - ($hadir + $izin));
                    $persentase = $totalHariKerja > 0 ? ($hadir / $totalHariKerja) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $pegawai->nik }}</td>
                    <td class="text-left">{{ $pegawai->nama }}</td>
                    <td>{{ $hadir }}</td>
                    <td>{{ $terlambat }}</td>
                    <td>{{ $izin }}</td>
                    <td>{{ $alpha }}</td>
                    <td class="{{ $persentase < 80 ? 'text-red' : 'text-blue' }}">
                        {{ number_format($persentase, 1) }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d M Y H:i:s') }}
    </div>

    <!-- ================= BLOK TANDA TANGAN ================= -->
    <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; margin-top: 40px; page-break-inside: avoid; border: none; border-collapse: collapse;">
        <tr style="border: none;">
            <!-- Kolom kosong di kiri (60%) agar tanda tangan terdorong ke kanan -->
            <td style="width: 60%; border: none;"></td>
            
            <!-- Kolom tanda tangan di kanan (40%) -->
            <td style="width: 40%; text-align: center; font-size: 11px; line-height: 1.4; border: none;">
                <p style="margin: 0; padding-bottom: 5px;">Cirebon, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p style="margin: 0; font-weight: bold;">Kepala Sub Bagian Umum dan Kepegawaian,</p>
                
                <!-- Jarak kosong vertikal untuk coretan tanda tangan asli / stempel basah -->
                <div style="height: 70px;"></div>
                
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">Sri Cartini, S.Kom</p>
                <p style="margin: 0;">NIP. 198405222009022002</p>
            </td>
        </tr>
    </table>

</body>
</html>