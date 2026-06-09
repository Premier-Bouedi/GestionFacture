<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Client;
use App\Mail\BusinessReportMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $companyName = Setting::where('key', 'company_name')->value('value');
        $companyLogo = Setting::where('key', 'company_logo')->value('value');
        return view('settings', compact('companyName', 'companyLogo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        // Mise à jour du nom
        Setting::updateOrCreate(
            ['key' => 'company_name'],
            ['value' => $request->company_name]
        );

        // Gestion du logo
        if ($request->hasFile('company_logo')) {
            // Supprimer l'ancien logo si nécessaire
            $oldLogo = Setting::where('key', 'company_logo')->value('value');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('company_logo')->store('logos', 'public');
            Setting::updateOrCreate(
                ['key' => 'company_logo'],
                ['value' => $path]
            );
        }

        return redirect()->back()->with('success', 'Paramètres mis à jour !');
    }

    public function resetData()
    {
        try {
            // Désactiver les contraintes pour SQLite
            DB::statement('PRAGMA foreign_keys = OFF;');
            
            // 1. Supprimer les données métiers
            Invoice::truncate();
            Product::truncate();
            Client::truncate();
            DB::table('invoice_product')->truncate();

            // 2. Optionnel : Supprimer les paramètres et logos si vous voulez un reset TOTAL
            $logo = Setting::where('key', 'company_logo')->value('value');
            if ($logo) {
                Storage::disk('public')->delete($logo);
            }
            Setting::truncate();

            // Réactiver les contraintes
            DB::statement('PRAGMA foreign_keys = ON;');

            return redirect()->back()->with('success', 'Toutes les données et paramètres ont été réinitialisés avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la réinitialisation : ' . $e->getMessage());
        }
    }

    /**
     * Affiche la page des paramètres de notification (WhatsApp + Email).
     */
    public function notifications()
    {
        // Clés de notification à charger depuis la table settings
        $keys = [
            'whatsapp_number',
            'notify_whatsapp_rupture',
            'notify_whatsapp_sales',
            'manager_email',
            'notify_email_daily',
            'notify_email_monthly',
        ];

        // Charger toutes les valeurs en un seul appel
        $settings = Setting::whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();

        return view('settings.notifications', compact('settings'));
    }

    /**
     * Enregistre les préférences de notification.
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'nullable|string|max:20',
            'manager_email'   => 'nullable|email|max:255',
        ]);

        // Liste des clés à enregistrer (booléens = 0 si non coché)
        $settingsToSave = [
            'whatsapp_number'          => $request->input('whatsapp_number', ''),
            'notify_whatsapp_rupture'  => $request->has('notify_whatsapp_rupture') ? '1' : '0',
            'notify_whatsapp_sales'    => $request->has('notify_whatsapp_sales') ? '1' : '0',
            'manager_email'            => $request->input('manager_email', ''),
            'notify_email_daily'       => $request->has('notify_email_daily') ? '1' : '0',
            'notify_email_monthly'     => $request->has('notify_email_monthly') ? '1' : '0',
        ];

        // Sauvegarder chaque clé-valeur
        foreach ($settingsToSave as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Si bilan journalier activé et email configuré, envoyer un email test
        if ($request->has('notify_email_daily') && $request->input('manager_email')) {
            try {
                $reportData = $this->buildReportData('daily');
                Mail::to($request->input('manager_email'))
                    ->send(new BusinessReportMail($reportData, 'daily'));
            } catch (\Exception $e) {
                // Ne pas bloquer la sauvegarde si l'email échoue
            }
        }

        return redirect()->back()->with('success', 'Préférences de notification enregistrées !');
    }

    /**
     * Construit les données du rapport d'activité.
     *
     * @param string $type 'daily' ou 'monthly'
     * @return array
     */
    private function buildReportData(string $type): array
    {
        $isMonthly = $type === 'monthly';

        // Période de calcul
        $invoicesQuery = Invoice::with('products')
            ->when($isMonthly,
                fn($q) => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                fn($q) => $q->whereDate('created_at', today())
            );

        $invoices   = $invoicesQuery->get();
        $ca         = $invoices->sum('total_ttc');
        $nbFactures = $invoices->count();
        $nbClients  = $invoices->pluck('client_id')->unique()->count();

        // Meilleur produit (par quantité vendue)
        $topProduct = null;
        $productCounts = [];
        foreach ($invoices as $invoice) {
            foreach ($invoice->products as $product) {
                $productCounts[$product->designation] =
                    ($productCounts[$product->designation] ?? 0) + $product->pivot->quantity;
            }
        }
        if (!empty($productCounts)) {
            arsort($productCounts);
            $topProduct = array_key_first($productCounts);
        }

        // Produits en rupture (stock < 5)
        $ruptures = Product::where('stock', '<', 5)
            ->pluck('designation')
            ->toArray();

        return compact('ca', 'nbFactures', 'nbClients', 'topProduct', 'ruptures');
    }
}
