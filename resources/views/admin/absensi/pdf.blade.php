<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Absensi Pegawai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px; /* Ukuran font sedikit dikecilkan karena Portrait */
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #003f83;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #003f83;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: center;
        }
        th {
            background-color: #f0f0f8;
            color: #003f83;
            font-weight: bold;
        }
        .text-left { text-align: left; }
        .text-red { color: #ba1a1a; font-weight: bold; }
        .text-green { color: #15803d; font-weight: bold; }
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
        <h2>DATA ABSENSI PEGAWAI</h2>
        <p><strong>Disdukcapil Kota Cirebon</strong></p>
        <p>Filter Waktu: <strong>{{ $labelWaktu }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%" class="text-left">Nama Pegawai</th>
                <th width="20%">Tanggal</th>
                <th width="15%">Check In</th>
                <th width="15%">Check Out</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensis as $index => $absen)
                @php
                    $isLate = false;
                    $checkInDisplay = '--:--';
                    $checkOutDisplay = '--:--';
                    $statusText = 'Belum Absen';
                    $statusClass = '';
                    
                    $isSpecialStatus = in_array(strtolower($absen->status), ['cuti', 'tidak aktif']);

                    if (!$isSpecialStatus) {
                        if ($absen->check_in) {
                            $checkInDisplay = \Carbon\Carbon::parse($absen->check_in)->format('H:i');
                            if ($checkInDisplay > '07:30') {
                                $isLate = true;
                            }
                        }
                        if ($absen->check_out) {
                            $checkOutDisplay = \Carbon\Carbon::parse($absen->check_out)->format('H:i');
                        }
                        
                        if ($absen->check_in && !$absen->check_out) {
                            $statusText = $isLate ? 'Terlambat' : 'Tepat Waktu';
                            $statusClass = $isLate ? 'text-red' : 'text-green';
                        } elseif ($absen->check_in && $absen->check_out) {
                            $statusText = 'Selesai';
                        }
                    } else {
                        $checkInDisplay = '-';
                        $checkOutDisplay = '-';
                        $statusText = ucwords($absen->status);
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">
                        <strong>{{ $absen->pegawai->nama }}</strong><br>
                        <span style="font-size: 9px; color: #666;">NIK: {{ $absen->pegawai->nik }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}</td>
                    <td class="{{ $isLate ? 'text-red' : '' }}">{{ $checkInDisplay }}</td>
                    <td>{{ $checkOutDisplay }}</td>
                    <td class="{{ $statusClass }}">{{ $statusText }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 20px;">Tidak ada data absensi untuk filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>