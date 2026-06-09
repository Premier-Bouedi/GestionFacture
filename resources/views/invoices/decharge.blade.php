<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bon de Décharge {{ $invoice->number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .company-info { float: left; width: 50%; }
        .doc-info { float: right; width: 50%; text-align: right; }
        .clear { clear: both; }
        .client-info { margin: 30px 0; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #444; color: #fff; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .legal { margin-top: 40px; padding: 20px; border: 1px solid #ccc; line-height: 1.6; }
        .signature { margin-top: 50px; }
        .signature-line { margin-top: 60px; border-top: 1px solid #333; width: 300px; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h2>{{ $companyName ?? 'Gestion Facture' }}</h2>
            <p>Document logistique — Bon de décharge</p>
        </div>
        <div class="doc-info">
            <h1>BON DE DÉCHARGE</h1>
            <p><strong>Facture N° :</strong> {{ $invoice->number }}</p>
            <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="client-info">
        <p><strong>Client :</strong> {{ $invoice->client->name }}</p>
        <p><strong>Email :</strong> {{ $invoice->client->email }}</p>
    </div>

    <p><strong>Articles remis au client :</strong></p>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th style="text-align: center;">Quantité remise</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->products as $product)
            <tr>
                <td>{{ $product->designation }}</td>
                <td style="text-align: center;">{{ $product->pivot->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legal">
        <p>
            Je soussigné(e) <strong>{{ $invoice->client->name }}</strong>,
            certifie avoir récupéré les produits listés ci-dessus en bon état,
            conformément à la facture n° <strong>{{ $invoice->number }}</strong>
            en date du <strong>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</strong>.
        </p>
        <p>
            Ce bon de décharge vaut attestation de livraison et de réception des marchandises.
        </p>
    </div>

    <div class="signature">
        <p>Fait le : ____________________</p>
        <div class="signature-line">Signature du client</div>
    </div>
</body>
</html>
