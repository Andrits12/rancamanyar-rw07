<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat {{ $surat->kode_surat }}</title>
    <style>
        @page { margin: 2cm 2.5cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop h1 { font-size: 16pt; margin: 0; font-weight: bold; letter-spacing: 1px; }
        .kop h2 { font-size: 14pt; margin: 3px 0; font-weight: bold; }
        .kop p { font-size: 10pt; margin: 2px 0; }
        .kop .logo { width: 80px; height: 80px; float: left; }
        .kop-text { margin-left: 90px; }
        .judul-surat { text-align: center; margin: 20px 0; }
        .judul-surat h3 { font-size: 14pt; font-weight: bold; text-decoration: underline; margin: 0; }
        .judul-surat p { margin: 3px 0; font-size: 11pt; }
        .content { text-align: justify; }
        .content p { margin: 8px 0; }
        .data-table { margin: 15px 0 15px 30px; }
        .data-table td { padding: 3px 0; vertical-align: top; }
        .data-table td:first-child { width: 150px; }
        .data-table td:nth-child(2) { width: 15px; }
        .ttd-section { margin-top: 40px; }
        .ttd-box { text-align: center; float: right; width: 250px; }
        .ttd-box p { margin: 3px 0; }
        .ttd-space { height: 80px; }
        .ttd-name { font-weight: bold; text-decoration: underline; }
        .qr-box { text-align: center; float: left; width: 180px; margin-top: 20px; }
        .qr-box svg { width: 130px; height: 130px; display: block; margin: 0 auto; }
        .qr-box p { font-size: 8pt; margin: 5px 0 0 0; text-align: center; }
        .clear { clear: both; }
        .footer-note { margin-top: 40px; font-size: 9pt; color: #666; font-style: italic; border-top: 1px solid #ccc; padding-top: 10px; clear: both; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="kop">
        @if (file_exists(public_path('images/logo-rw.png')))
            <img src="{{ public_path('images/logo-rw.png') }}" class="logo" alt="Logo">
        @endif
        <div class="kop-text">
            <h1>PENGURUS RUKUN WARGA 07</h1>
            <h2>PERUMAHAN RANCAMANYAR</h2>
            <p>Desa Wancimekar, Kecamatan Kotabaru, Kabupaten Karawang</p>
            <p>Jawa Barat — 41374</p>
        </div>
    </div>

    {{-- JUDUL SURAT --}}
    <div class="judul-surat">
        <h3>{{ strtoupper($surat->jenisSurat->nama ?? 'SURAT KETERANGAN') }}</h3>
        <p>Nomor: {{ $surat->nomor_surat ?? '-' }}</p>
    </div>

    {{-- ISI SURAT --}}
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Ketua RW 07 Perumahan Rancamanyar, Desa Wancimekar, Kecamatan Kotabaru, Kabupaten Karawang, dengan ini menerangkan bahwa:</p>

        <table class="data-table">
            <tr><td>Nama</td><td>:</td><td><strong>{{ $surat->user->name }}</strong></td></tr>
            <tr><td>NIK</td><td>:</td><td>{{ $surat->user->nik }}</td></tr>
            <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{ $surat->user->tempat_lahir ?? '-' }}, {{ $surat->user->tgl_lahir?->format('d F Y') ?? '-' }}</td></tr>
            <tr><td>Jenis Kelamin</td><td>:</td><td>{{ $surat->user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
            <tr><td>Agama</td><td>:</td><td>{{ $surat->user->agama ?? '-' }}</td></tr>
            <tr><td>Pekerjaan</td><td>:</td><td>{{ $surat->user->pekerjaan ?? '-' }}</td></tr>
            <tr><td>Alamat</td><td>:</td><td>{{ $surat->user->alamat ?? '-' }}<br>RT {{ $surat->rt->nomor_rt ?? '-' }} / RW 07</td></tr>
        </table>

        <p>Bahwa nama tersebut di atas adalah benar warga RW 07 Perumahan Rancamanyar dan berdomisili di alamat tersebut.</p>

        <p>Surat keterangan ini dibuat untuk keperluan: <strong>{{ $surat->keperluan }}</strong></p>

        <p>Demikian surat keterangan ini dibuat dengan sebenarnya, untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    {{-- TANDA TANGAN & QR --}}
    <div class="ttd-section">
        <div class="qr-box">
            <img src="{{ $qrCodeSvgBase64 }}" style="width: 130px; height: 130px;" alt="QR Code">
        </div>

        <div class="ttd-box">
            <p>Wancimekar, {{ $tanggalSurat->translatedFormat('d F Y') }}</p>
            <p>Ketua RW 07</p>
            <div class="ttd-space"></div>
            <p class="ttd-name">{{ $surat->penyetujuRw->name ?? '..............................' }}</p>
        </div>
        <div class="clear"></div>
    </div>

    {{-- FOOTER --}}
    <div class="footer-note">
        Dokumen ini diterbitkan secara digital melalui Sistem Informasi RW Digital.
        Keaslian dapat diverifikasi dengan memindai QR code di atas.
        Kode Surat: {{ $surat->kode_surat }}
    </div>

</body>
</html>