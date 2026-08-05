<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { padding: 20px; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Invoice</h2>
        <p><strong>Nama:</strong> {{ $item['name'] }}</p>
        <p><strong>Jumlah:</strong> Rp{{ number_format($item['amount'], 0, ',', '.') }}</p>
    </div>
</body>
</html>
