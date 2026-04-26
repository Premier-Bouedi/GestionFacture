<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Product;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_invoices' => Invoice::count(),
            'total_clients' => Client::count(),
            'total_products' => Product::count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'total_revenue' => Invoice::sum('total_ttc'),
        ];

        $latest_invoices = Invoice::with('client')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latest_invoices'));
    }

    /**
     * Journal d'Audit (Boîte Noire)
     */
    public function auditLog()
    {
        $logs = AuditLog::latest()->paginate(25);
        return view('admin.audit-log', compact('logs'));
    }

    /**
     * Corbeille : Éléments supprimés (Soft Deleted)
     */
    public function trash()
    {
        $deletedInvoices = Invoice::onlyTrashed()->with('client')->get();
        $deletedClients = Client::onlyTrashed()->get();
        $deletedProducts = Product::onlyTrashed()->get();

        return view('admin.trash', compact('deletedInvoices', 'deletedClients', 'deletedProducts'));
    }

    /**
     * Restaurer un élément supprimé
     */
    public function restore(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        $model = match($type) {
            'invoice' => Invoice::onlyTrashed()->findOrFail($id),
            'client' => Client::onlyTrashed()->findOrFail($id),
            'product' => Product::onlyTrashed()->findOrFail($id),
            default => abort(404),
        };

        $model->restore();

        AuditLog::log('restored', $model, "Restauration de {$type} #{$id}");

        return redirect()->back()->with('success', ucfirst($type) . " restauré avec succès !");
    }
}

