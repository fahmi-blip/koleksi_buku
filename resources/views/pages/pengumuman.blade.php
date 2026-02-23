<!DOCTYPE html>
<html>
<head>
    <title>Pengumuman</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; margin: 0; padding: 30px; }
        .header { width: 100%; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 14px; }
        .content { line-height: 1.6; font-size: 12pt; text-align: justify; }
        .signature { margin-top: 50px; width: 300px; float: right; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <h1>UNIVERSITAS AIRLANGGA</h1>
                <h2>FAKULTAS VOKASI</h2>
                <p>Jalan Raya Kampus No. 123, Kota, Provinsi, Kode Pos 12345</p>
                <p>Email: info@fakultas.univ.ac.id | Telp: (021) 1234567</p>
            </td>
        </tr>
    </table>

    <div class="content">
        <p style="text-align: right">Tanggal: {{ $tanggal }}</p>
        <p>Nomor: {{ $nomor_surat }}<br>Perihal: <b>Pengumuman Kegiatan Akademik</b></p>
        
        <p>Dengan hormat,</p>
        <p>Bersama surat ini kami mengumumkan kepada seluruh mahasiswa dan jajaran staf akademik bahwa akan diadakan perbaikan sistem secara menyeluruh pada pekan depan. Diharapkan seluruh civitas akademika dapat menyesuaikan jadwal kegiatan belajar mengajar.</p>
        <p>Demikian pengumuman ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
        
        <div class="signature">
            <p>Dekan Fakultas,</p>
            <br><br><br>
            <p><b>Prof. Dr. Ir. Fahmi, M.Kom.</b></p>
        </div>
    </div>
</body>
</html>