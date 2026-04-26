<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice->number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .company-info { float: left; width: 50%; }
        .invoice-info { float: right; width: 50%; text-align: right; }
        .clear { clear: both; }
        .client-info { margin: 30px 0; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #444; color: #fff; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .totals { float: right; width: 300px; }
        .totals-row { padding: 5px 0; border-bottom: 1px solid #eee; }
        .totals-row.grand-total { font-weight: bold; font-size: 14px; border-bottom: 2px solid #444; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h2>{{ $companyName ?? 'Gestion Facture' }}</h2>
            <p>Spécialiste de la gestion commerciale</p>
        </div>
        <div class="invoice-info">
            <h1>FACTURE</h1>
            <p><strong>N° :</strong> {{ $invoice->number }}</p>
            <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="client-info">
        <p><strong>Facturé à :</strong></p>
        <p>{{ $invoice->client->name }}</p>
        <p>{{ $invoice->client->email }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th style="text-align: right;">Prix Unitaire</th>
                <th style="text-align: center;">Qté</th>
                <th style="text-align: right;">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->products as $product)
            <tr>
                <td>{{ $product->designation }}</td>
                <td style="text-align: right;">{{ number_format($product->pivot->unit_price, 2, ',', ' ') }} DH</td>
                <td style="text-align: center;">{{ $product->pivot->quantity }}</td>
                <td style="text-align: right;">{{ number_format($product->pivot->unit_price * $product->pivot->quantity, 2, ',', ' ') }} DH</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span style="float: left;">Total HT :</span>
            <span style="float: right;">{{ number_format($invoice->total_ht, 2, ',', ' ') }} DH</span>
            <div class="clear"></div>
        </div>
        <div class="totals-row">
            <span style="float: left;">TVA (20%) :</span>
            <span style="float: right;">{{ number_format($invoice->total_tva, 2, ',', ' ') }} DH</span>
            <div class="clear"></div>
        </div>
        <div class="totals-row grand-total">
            <span style="float: left;">TOTAL TTC :</span>
            <span style="float: right;">{{ number_format($invoice->total_ttc, 2, ',', ' ') }} DH</span>
            <div class="clear"></div>
        </div>
    </div>

    <div class="footer">
        <p>Merci de votre confiance ! Cette facture est générée numériquement.</p>
    </div>
</body>
</html>
