<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $message = strtolower(trim($request->input('message', '')));
        $user = auth()->user();
        $isAdmin = $user && $user->isAdmin();

        // Réponses contextuelles basées sur les mots-clés
        $response = $this->processMessage($message, $isAdmin);

        return response()->json([
            'reply' => $response,
            'timestamp' => now()->format('H:i'),
        ]);
    }

    private function processMessage(string $message, bool $isAdmin): string
    {
        // --- SALUTATIONS ---
        if ($this->contains($message, ['bonjour', 'salut', 'hello', 'hi', 'hey', 'bonsoir'])) {
            $name = auth()->user()->name ?? 'utilisateur';
            return "👋 Bonjour {$name} ! Je suis votre assistant de facturation. Voici ce que je peux faire :\n\n"
                . "📊 **Statistiques** : \"chiffre d'affaires\", \"nombre de factures\"\n"
                . "📦 **Stock** : \"stock bas\", \"rupture de stock\"\n"
                . "👥 **Clients** : \"meilleur client\", \"nombre de clients\"\n"
                . ($isAdmin ? "🛡️ **Admin** : \"utilisateurs\", \"dernière facture\"\n" : "")
                . "\nPosez-moi une question !";
        }

        // --- AIDE ---
        if ($this->contains($message, ['aide', 'help', 'commandes', 'quoi faire', 'comment'])) {
            return "🤖 **Commandes disponibles :**\n\n"
                . "💰 \"chiffre d'affaires\" - Total des ventes\n"
                . "📄 \"nombre de factures\" - Compteur de factures\n"
                . "📦 \"stock bas\" - Produits en alerte\n"
                . "🔴 \"rupture\" - Produits en rupture totale\n"
                . "👥 \"meilleur client\" - Client le plus actif\n"
                . "📊 \"résumé\" - Vue d'ensemble complète\n"
                . ($isAdmin ? "🛡️ \"utilisateurs\" - Nombre d'utilisateurs (Admin)\n" : "");
        }

        // --- CHIFFRE D'AFFAIRES ---
        if ($this->contains($message, ['chiffre', 'affaires', 'revenu', 'ca', 'ventes', 'total vente'])) {
            if (!$isAdmin) {
                return "🔒 Cette information est réservée aux administrateurs. Contactez votre responsable.";
            }
            $total = Invoice::with('products')->get()->sum(function ($invoice) {
                return $invoice->products->sum(fn($p) => $p->price * $p->pivot->quantity);
            });
            return "💰 **Chiffre d'affaires total** : " . number_format($total, 2) . " DH\n\n"
                . "📊 Basé sur " . Invoice::count() . " facture(s) enregistrée(s).";
        }

        // --- NOMBRE DE FACTURES ---
        if ($this->contains($message, ['nombre facture', 'combien facture', 'factures'])) {
            $count = Invoice::count();
            $lastInvoice = Invoice::latest()->first();
            $last = $lastInvoice ? "Dernière : **{$lastInvoice->number}**" : "Aucune facture";
            return "📄 **{$count}** facture(s) dans le système.\n{$last}";
        }

        // --- STOCK BAS ---
        if ($this->contains($message, ['stock bas', 'alerte stock', 'stock faible'])) {
            $lowStock = Product::where('stock', '>', 0)->where('stock', '<=', 3)->get();
            if ($lowStock->isEmpty()) {
                return "✅ Aucun produit en alerte de stock bas. Tout va bien !";
            }
            $list = $lowStock->map(fn($p) => "⚠️ **{$p->name}** : {$p->stock} unité(s)")->join("\n");
            return "📦 **Produits en stock bas :**\n\n{$list}";
        }

        // --- RUPTURE DE STOCK ---
        if ($this->contains($message, ['rupture', 'stock 0', 'plus de stock', 'épuisé'])) {
            $outOfStock = Product::where('stock', 0)->get();
            if ($outOfStock->isEmpty()) {
                return "✅ Aucun produit en rupture de stock !";
            }
            $list = $outOfStock->map(fn($p) => "🔴 **{$p->name}** (Prix: {$p->price} DH)")->join("\n");
            return "🚨 **Produits en rupture :**\n\n{$list}\n\nAction requise !";
        }

        // --- MEILLEUR CLIENT ---
        if ($this->contains($message, ['meilleur client', 'client fidèle', 'top client'])) {
            $client = Client::withCount('invoices')->orderByDesc('invoices_count')->first();
            if (!$client) {
                return "👥 Aucun client enregistré pour le moment.";
            }
            return "🏆 **Meilleur client** : {$client->name}\n📄 {$client->invoices_count} facture(s) émise(s)\n📧 {$client->email}";
        }

        // --- NOMBRE DE CLIENTS ---
        if ($this->contains($message, ['nombre client', 'combien client', 'clients'])) {
            return "👥 **" . Client::count() . "** client(s) enregistré(s) dans le système.";
        }

        // --- PRODUITS ---
        if ($this->contains($message, ['produit', 'catalogue', 'article'])) {
            $total = Product::count();
            $totalStock = Product::sum('stock');
            return "📦 **{$total}** produit(s) dans le catalogue.\n📊 Stock total : **{$totalStock}** unités.";
        }

        // --- UTILISATEURS (ADMIN ONLY) ---
        if ($this->contains($message, ['utilisateur', 'user', 'accès', 'compte'])) {
            if (!$isAdmin) {
                return "🔒 Information réservée aux administrateurs.";
            }
            $admins = User::where('role', 'admin')->count();
            $users = User::where('role', 'user')->count();
            return "👤 **Utilisateurs du système :**\n🛡️ Admins : **{$admins}**\n👥 Users : **{$users}**\n📊 Total : **" . ($admins + $users) . "**";
        }

        // --- RÉSUMÉ GLOBAL ---
        if ($this->contains($message, ['résumé', 'resume', 'dashboard', 'overview', 'vue ensemble'])) {
            if (!$isAdmin) {
                return "🔒 Le résumé global est réservé aux administrateurs.";
            }
            $totalRevenue = Invoice::with('products')->get()->sum(function ($invoice) {
                return $invoice->products->sum(fn($p) => $p->price * $p->pivot->quantity);
            });
            $outOfStock = Product::where('stock', 0)->count();
            return "📊 **Résumé Global :**\n\n"
                . "💰 CA Total : **" . number_format($totalRevenue, 2) . " DH**\n"
                . "📄 Factures : **" . Invoice::count() . "**\n"
                . "👥 Clients : **" . Client::count() . "**\n"
                . "📦 Produits : **" . Product::count() . "**\n"
                . "🔴 Ruptures : **{$outOfStock}**\n"
                . "👤 Utilisateurs : **" . User::count() . "**";
        }

        // --- DERNIÈRE FACTURE ---
        if ($this->contains($message, ['dernière facture', 'derniere facture', 'last'])) {
            $invoice = Invoice::with('client')->latest()->first();
            if (!$invoice) {
                return "📄 Aucune facture n'a encore été créée.";
            }
            return "📄 **Dernière facture :** {$invoice->number}\n👤 Client : {$invoice->client->name}\n📅 Date : {$invoice->invoice_date}";
        }

        // --- MERCI ---
        if ($this->contains($message, ['merci', 'thanks', 'thank', 'super', 'parfait', 'top'])) {
            return "😊 Avec plaisir ! N'hésitez pas si vous avez d'autres questions. Je suis là pour vous aider !";
        }

        // --- RÉPONSE PAR DÉFAUT ---
        return "🤔 Je n'ai pas compris votre demande. Essayez :\n\n"
            . "• \"aide\" pour voir les commandes\n"
            . "• \"résumé\" pour un aperçu global\n"
            . "• \"stock bas\" pour les alertes\n"
            . "• \"chiffre d'affaires\" pour le CA";
    }

    private function contains(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }
        return false;
    }
}
