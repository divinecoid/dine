<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $table->name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .print-container {
            text-align: center;
            border: 2px solid #000;
            padding: 40px;
            border-radius: 20px;
            width: 300px;
        }

        .store-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .qr-code {
            width: 250px;
            height: 250px;
            margin: 0 auto;
        }

        .table-name {
            font-size: 32px;
            font-weight: bold;
            margin-top: 20px;
        }

        .instruction {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }

        @media print {
            body {
                background: none;
            }

            .print-container {
                border: none;
                width: 100%;
                padding: 0;
            }

            @page {
                size: auto;
                margin: 0mm;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="print-container">
        <div class="store-name">{{ $table->store->name }}</div>

        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="qr-code">

        <div class="table-name">{{ $table->name }}</div>
        <div class="instruction">Scan untuk melihat menu & pesan</div>
    </div>
</body>

</html>