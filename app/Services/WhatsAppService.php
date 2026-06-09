<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsAppService — Envoi d'alertes WhatsApp via CallMeBot (API gratuite).
 *
 * Configuration requise dans .env :
 *   CALLMEBOT_API_KEY=votre_cle_ici
 *
 * Pour obtenir une clé CallMeBot :
 *   1. Ajoutez +34644357579 dans vos contacts WhatsApp.
 *   2. Envoyez le message : "I allow callmebot to send me messages"
 *   3. Vous recevrez votre apikey par WhatsApp.
 */
class WhatsAppService
{
    /**
     * URL de l'API CallMeBot.
     */
    private const API_URL = 'https://api.callmebot.com/whatsapp.php';

    /**
     * Envoie un message WhatsApp au numéro configuré dans les paramètres.
     *
     * @param string $message Texte du message à envoyer
     * @return bool True si envoyé avec succès
     */
    public function sendAlert(string $message): bool
    {
        // Récupérer le numéro WhatsApp depuis les paramètres de l'application
        $phone  = Setting::where('key', 'whatsapp_number')->value('value');
        $apiKey = env('CALLMEBOT_API_KEY');

        // Vérifications préalables
        if (empty($phone)) {
            Log::warning('[WhatsApp] Numéro non configuré dans les paramètres.');
            return false;
        }

        if (empty($apiKey)) {
            // Mode dégradé : log uniquement (pas de clé API)
            Log::warning('[WhatsApp SIMULATION] Message qui aurait été envoyé : ' . $message);
            return false;
        }

        try {
            // Appel à l'API CallMeBot
            $response = Http::timeout(10)->get(self::API_URL, [
                'phone'   => $phone,
                'text'    => $message,
                'apikey'  => $apiKey,
            ]);

            if ($response->successful()) {
                Log::info('[WhatsApp] Message envoyé avec succès à ' . $phone);
                return true;
            } else {
                Log::error('[WhatsApp] Échec envoi. Code: ' . $response->status());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('[WhatsApp] Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie une alerte de rupture de stock pour un produit spécifique.
     *
     * @param Product $product Le produit en rupture
     */
    public function sendStockAlert(Product $product): void
    {
        // Vérifier que l'alerte rupture est activée dans les paramètres
        $alertEnabled = Setting::where('key', 'notify_whatsapp_rupture')->value('value');

        if ($alertEnabled !== '1') {
            return; // Alerte désactivée, on ne fait rien
        }

        $message = "🚨 *ALERTE RUPTURE DE STOCK*\n\n"
                 . "Produit : *{$product->designation}*\n"
                 . "Stock restant : *{$product->stock} unités*\n\n"
                 . "⚠️ Veuillez réapprovisionner rapidement.\n"
                 . "_Message automatique — " . now()->format('d/m/Y H:i') . "_";

        $this->sendAlert($message);
    }

    /**
     * Envoie un résumé des ventes du jour (si option activée).
     *
     * @param float $totalVentes CA du jour
     * @param int   $nbFactures  Nombre de factures du jour
     */
    public function sendSalesReport(float $totalVentes, int $nbFactures): void
    {
        $alertEnabled = Setting::where('key', 'notify_whatsapp_sales')->value('value');

        if ($alertEnabled !== '1') {
            return;
        }

        $message = "📊 *RAPPORT DE VENTES*\n\n"
                 . "📅 Date : " . now()->format('d/m/Y') . "\n"
                 . "🧾 Factures émises : *{$nbFactures}*\n"
                 . "💰 Chiffre d'affaires : *" . number_format($totalVentes, 0, ',', ' ') . " FCFA*\n\n"
                 . "_Rapport automatique FAC+_";

        $this->sendAlert($message);
    }
}
