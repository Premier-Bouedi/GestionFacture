<?php
// Test d'envoi d'email en mode log

$invoice = App\Models\Invoice::with(['client', 'products'])->first();

if (!$invoice) {
    echo "Aucune facture en base.\n";
    return;
}

echo "Facture: {$invoice->number}\n";
echo "Client: {$invoice->client->name} ({$invoice->client->email})\n";

$mail = new App\Mail\InvoiceMail($invoice);
Illuminate\Support\Facades\Mail::to($invoice->client->email)->send($mail);

echo "✅ Email envoyé avec succès (mode log) !\n";
