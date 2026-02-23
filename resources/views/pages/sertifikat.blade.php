<!DOCTYPE html>
<html>
<head>
    <title>Sertifikat</title>
    <style>
        body { font-family: sans-serif; text-align: center; margin: 0; padding: 20px; }
        .title { font-size: 50px; font-weight: bold; margin-bottom: 20px; color: #2c3e50; margin-top: 50px; }
        .subtitle { font-size: 20px; margin-bottom: 40px; }
        .name { font-size: 40px; text-decoration: underline; font-weight: bold; margin-bottom: 40px; }
        .footer { margin-top: 100px; text-align: right; padding-right: 50px; }
    </style>
</head>
<body>
    <div>
        <div class="title">SERTIFIKAT PENGHARGAAN</div>
        <div class="subtitle">Diberikan kepada:</div>
        <div class="name">{{ $nama }}</div>
        <div class="subtitle">Atas partisipasi dan dedikasinya dalam kegiatan:<br><b>{{ $kegiatan }}</b></div>
        
        <div class="footer">
            <p>Surabaya, 23 Februari 2026</p>
            <br><br><br>
            <p><b>Ketua Penyelenggara</b></p>
        </div>
    </div>
</body>
</html>