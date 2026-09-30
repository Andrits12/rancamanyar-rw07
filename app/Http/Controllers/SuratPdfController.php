<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;


class SuratPdfController extends Controller
{
    /**
     * Preview PDF di browser
     */
    public function preview(Surat $surat)
    {
        if ($surat->status !== 'selesai') {
            return back()->with('error', 'Surat belum selesai diproses.');
        }

        $this->checkAccess($surat);

        $pdf = $this->generatePdf($surat);
        $output = $pdf->output();
        $filename = 'surat-' . $surat->kode_surat . '.pdf';

        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Content-Length', strlen($output))
            ->header('Cache-Control', 'private, max-age=0, must-revalidate')
            ->header('Pragma', 'public');
    }

    /**
     * Tampilkan PDF di dalam halaman HTML (via <embed>)
     */
    public function viewInline(Surat $surat)
    {
        if ($surat->status !== 'selesai') {
            return back()->with('error', 'Surat belum selesai diproses.');
        }

        $this->checkAccess($surat);

        $pdf = $this->generatePdf($surat);
        $output = base64_encode($pdf->output());

        return view('surat.view-inline', compact('surat', 'output'));
    }

    /**
     * Download PDF
     */
    public function download(Surat $surat)
    {
        if ($surat->status !== 'selesai') {
            return back()->with('error', 'Surat belum selesai diproses.');
        }

        $this->checkAccess($surat);

        $pdf = $this->generatePdf($surat);

        return $pdf->download('surat-' . $surat->kode_surat . '.pdf');
    }

    /**
     * Verifikasi QR code (publik)
     */
    public function verifikasi($qrCode)
    {
        $surat = Surat::where('qr_code', $qrCode)->first();

        if (!$surat) {
            return view('surat.verifikasi-invalid');
        }

        return view('surat.verifikasi', compact('surat'));
    }

    /**
     * Generate PDF object
     */
    protected function generatePdf(Surat $surat)
    {
        $verifyUrl = route('surat.verifikasi', $surat->qr_code);

        // Generate QR lokal pakai chillerlan/php-qrcode (tanpa imagick)
        $options = new \chillerlan\QRCode\QROptions([
            'version' => 5,
            'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
            'eccLevel' => \chillerlan\QRCode\Common\EccLevel::L,
            'svgViewBoxSize' => 300,
            'outputBase64' => false,
        ]);

        $qrcode = new \chillerlan\QRCode\QRCode($options);
        $qrCodeSvg = $qrcode->render($verifyUrl);
        $qrCodeSvgBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);


        $data = [
            'surat' => $surat,
            'qrCodeSvg' => $qrCodeSvg,
            'qrCodeSvgBase64' => $qrCodeSvgBase64,
            'verifyUrl' => $verifyUrl,
            'tanggalSurat' => $surat->tanggal_surat ?? now(),
        ];

        $pdf = Pdf::loadView('surat.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        // Setting remote image
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isPhpEnabled', true); // Tambahkan ini
        $pdf->getDomPDF()->set_option('http_context', stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ]));

        return $pdf;
    }

    /**
     * Cek hak akses
     */
    protected function checkAccess(Surat $surat)
    {
        $user = auth()->user();

        // Super admin, ketua RW, sekretaris: bisa lihat semua
        if ($user->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
            return;
        }

        if ($user->hasRole('ketua_rt') && $user->rt_id === $surat->rt_id) {
            return;
        }

        // Pemohon sendiri
        if ($surat->user_id === $user->id) {
            return;
        }

        abort(403, 'Anda tidak berhak mengakses surat ini.');
    }
}