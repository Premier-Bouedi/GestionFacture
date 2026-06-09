<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Product;
use App\Http\Requests\StoreInvoiceRequest;
use App\Services\InvoiceService;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Liste paginée avec recherche par numéro de facture ou nom du client.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $invoices = Invoice::with(['client', 'products'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('number', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($clientQuery) use ($search) {
                            $clientQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('invoices.index', compact('invoices', 'search'));
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
        $invoice = Invoice::with(['client', 'products'])->findOrFail($id);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('facture-' . $invoice->id . '.pdf');
    }

    /**
     * Génère le bon de décharge PDF (factures payées, non encore livrées).
     */
    public function generateDecharge($id)
    {
        $invoice = Invoice::with(['client', 'products'])->findOrFail($id);

        if (! $invoice->isPaid()) {
            return redirect()->back()->with('error', 'Bon de décharge indisponible : la facture n\'est pas payée.');
        }

        if ($invoice->products->isEmpty()) {
            return redirect()->back()->with('error', 'Bon de décharge indisponible : aucun article sur cette facture.');
        }

        $pdf = Pdf::loadView('invoices.decharge', compact('invoice'));

        $invoice->update(['statut_livraison' => 'Livré']);

        return $pdf->download('decharge-' . $invoice->number . '.pdf');
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
