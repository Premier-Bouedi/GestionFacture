<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Product;
use App\Http\Requests\StoreInvoiceRequest;
use App\Services\InvoiceService;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    // Index optimisé (Eager Loading)
    public function index()
    {
        // Seulement 2 requêtes SQL au lieu de N+1
        $invoices = Invoice::with(['client', 'products'])->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::all();
        $products = Product::all();
        return view('invoices.create', compact('clients', 'products'));
    }

    public function edit($id)
    {
        $invoice = Invoice::with('products')->findOrFail($id);
        $clients = Client::all();
        $products = Product::all();
        return view('invoices.edit', compact('invoice', 'clients', 'products'));
    }

    public function update(StoreInvoiceRequest $request, $id)
    {
        try {
            $this->invoiceService->updateInvoice($id, $request->validated());
            return redirect()->route('invoices.index')->with('success', 'Facture mise à jour !');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Création sécurisée via Service
    public function store(StoreInvoiceRequest $request)
    {
        try {
            $this->invoiceService->createInvoice($request->validated());
            return redirect()->route('invoices.index')->with('success', 'Facture générée !');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with(['client', 'products'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    public function destroy($id)
    {
        $this->invoiceService->deleteInvoice($id);
        return redirect()->route('invoices.index')->with('success', 'Facture supprimée et stock restauré !');
    }

    public function download($id)
    {
        // Récupération de la facture avec les relations
        $invoice = Invoice::with(['client', 'products'])->findOrFail($id);

        // Chargement de la vue spécifique pour le PDF
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        // Téléchargement du fichier
        return $pdf->download('facture-' . $invoice->id . '.pdf');
    }

    /**
     * Envoyer la facture par email au client avec le PDF en pièce jointe.
     */
    public function sendEmail($id)
    {
        try {
            $invoice = Invoice::with(['client', 'products'])->findOrFail($id);

            // Vérifier que le client a un email valide
            if (empty($invoice->client->email)) {
                return redirect()->back()->with('error', 
                    "Impossible d'envoyer : le client « {$invoice->client->name} » n'a pas d'adresse email."
                );
            }

            // Envoyer l'email avec le PDF en pièce jointe
            Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));

            return redirect()->back()->with('success', 
                "✅ Facture {$invoice->number} envoyée avec succès à {$invoice->client->email} !"
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                "❌ Erreur lors de l'envoi : " . $e->getMessage()
            );
        }
    }
}
