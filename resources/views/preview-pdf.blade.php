<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview: {{ basename($path) }}</title>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; background: #525659; }
        iframe { width: 100%; height: 100vh; border: 0; }
    </style>
</head>
<body>
    <iframe src="data:application/pdf;base64,{{ $pdf }}"></iframe>
</body>
</html>