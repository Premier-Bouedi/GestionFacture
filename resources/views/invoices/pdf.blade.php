<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice->number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        .header { text-align: right; margin-bottom: 50px; }
        .header h1 { color: #2563eb; margin: 0; }
        .client-info { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f3f4f6; padding: 12px; border: 1px solid #e5e7eb; text-align: left; }
        td { padding: 12px; border: 1px solid #e5e7eb; }
        .total-section { margin-top: 40px; text-align: right; }
        .total-section p { margin: 5px 0; }
        .total-ttc { font-size: 1.5em; color: #1e40af; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        @if($companyLogo)
            <img src="{{ public_path('storage/' . $companyLogo) }}" style="max-height: 80px; float: left;">
        @endif
        <h1>FACTURE</h1>
        <p>Facture N° : <strong>{{ $invoice->number }}</strong></p>
        <p>Date : {{ $invoice->invoice_date }}</p>
    </div>

    <div style="clear: both;"></div>

    <div class="client-info">
        <h3>Destinataire :</h3>
        <p>
            <strong>{{ $invoice->client->name }}</strong><br>
            {{ $invoice->client->email }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th style="text-align: right;">Prix Unitaire</th>
                <th style="text-align: center;">Quantité</th>
                <th style="text-align: right;">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @php $totalHT = 0; @endphp
            @foreach($invoice->products as $product)
                @php 
                    $sub = $product->price * $product->pivot->quantity;
                    $totalHT += $sub;
                @endphp
                <tr>
                    <td>{{ $product->name }}</td>
                    <td style="text-align: right;">{{ $product->formatted_price }}</td>
                    <td style="text-align: center;">{{ $product->pivot->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($sub, 2) }} DH</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <p>Total HT : <strong>{{ number_format($totalHT, 2) }} DH</strong></p>
        <p>TVA (20%) : <strong>{{ number_format($totalHT * 0.2, 2) }} DH</strong></p>
        <div class="total-ttc">TOTAL TTC : {{ number_format($totalHT * 1.2, 2) }} DH</div>
    </div>

    <div class="footer" style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 12px; color: #6b7280;">
        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin-bottom: 10px;">
        <p>Merci de votre confiance — <strong>{{ $companyName }}</strong></p>
        <p style="font-size: 10px;">Généré automatiquement le {{ date('d/m/Y') }}</p>
    </div>
</body>
</html>
