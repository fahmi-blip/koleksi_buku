<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfGenerator extends Controller
{

    public function cetakLabel()
    {
        $data = ['nomor_surat' => '123/FAK/2026', 'tanggal' => '23 Februari 2026'];
        
        $pdf = Pdf::loadView('pages.pengumuman', $data)
                  ->setPaper('a4', 'portrait');
                  
        return $pdf->stream('Pengumuman_Fakultas.pdf');
    }
}