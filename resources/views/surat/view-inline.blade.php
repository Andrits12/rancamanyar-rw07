<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview Surat: {{ $surat->kode_surat }}</title>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; }
        embed { width: 100%; height: 100vh; }
    </style>
</head>
<body>
    <embed src="data:application/pdf;base64,{{ $output }}" type="application/pdf" width="100%" height="100%">
</body>
</html>
