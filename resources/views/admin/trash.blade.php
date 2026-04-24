@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🗑️ Corbeille (Éléments Supprimés)</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Retour</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Factures Supprimées --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">📄 Factures supprimées ({{ count($deletedInvoices) }})</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr><th>N°</th><th>Client</th><th>Date</th><th>Supprimé le</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($deletedInvoices as $inv)
                    <tr>
                        <td>{{ $inv->number }}</td>
                        <td>{{ $inv->client->name ?? 'N/A' }}</td>
                        <td>{{ $inv->invoice_date }}</td>
                        <td class="small text-muted">{{ $inv->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.restore') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="type" value="invoice">
                                <input type="hidden" name="id" value="{{ $inv->id }}">
                                <button class="btn btn-sm btn-success">♻️ Restaurer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Aucune facture supprimée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Clients Supprimés --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">👥 Clients supprimés ({{ count($deletedClients) }})</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr><th>Nom</th><th>Email</th><th>Supprimé le</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($deletedClients as $client)
                    <tr>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td class="small text-muted">{{ $client->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.restore') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="type" value="client">
                                <input type="hidden" name="id" value="{{ $client->id }}">
                                <button class="btn btn-sm btn-success">♻️ Restaurer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Aucun client supprimé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Produits Supprimés --}}
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">📦 Produits supprimés ({{ count($deletedProducts) }})</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr><th>Nom</th><th>Prix</th><th>Stock</th><th>Supprimé le</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($deletedProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ number_format($product->price, 2) }} DH</td>
                        <td>{{ $product->stock }}</td>
                        <td class="small text-muted">{{ $product->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.restore') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="type" value="product">
                                <input type="hidden" name="id" value="{{ $product->id }}">
                                <button class="btn btn-sm btn-success">♻️ Restaurer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Aucun produit supprimé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
