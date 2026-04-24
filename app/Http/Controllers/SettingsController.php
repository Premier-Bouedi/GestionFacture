<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Http\Request;
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
}
