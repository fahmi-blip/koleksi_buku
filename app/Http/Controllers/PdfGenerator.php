<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfGenerator extends Controller
{
    // A. Generate Sertifikat (Landscape)
    public function cetakSertifikat()
    {
        $data = ['nama' => 'Fahmi Syihaab', 'kegiatan' => 'Workshop Framework Laravel'];
        
        $pdf = Pdf::loadView('pages.sertifikat', $data)
                  ->setPaper('a4', 'landscape');
                  
        return $pdf->stream('Sertifikat.pdf');
    }

    public function cetakPengumuman()
    {
        $data = ['nomor_surat' => '123/FAK/2026', 'tanggal' => '23 Februari 2026'];
        
        $pdf = Pdf::loadView('pages.pengumuman', $data)
                  ->setPaper('a4', 'portrait');
                  
        return $pdf->stream('Pengumuman_Fakultas.pdf');
    }
}