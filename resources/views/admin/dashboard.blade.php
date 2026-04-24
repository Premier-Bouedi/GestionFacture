@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tableau de Bord Admin</h2>
        <div class="text-muted">Aujourd'hui, le {{ date('d/m/Y') }}</div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-5">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small">Total Revenu</h6>
                    <h3 class="mb-0">{{ number_format($stats['total_revenue'], 2) }} DH</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small">Factures</h6>
                    <h3 class="mb-0">{{ $stats['total_invoices'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small">Clients</h6>
                    <h3 class="mb-0">{{ $stats['total_clients'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 {{ $stats['out_of_stock'] > 0 ? 'bg-danger' : 'bg-secondary' }} text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small">Rupture de Stock</h6>
                    <h3 class="mb-0">{{ $stats['out_of_stock'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Dernières Factures -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Dernières Factures</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>N°</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latest_invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->number }}</td>
                                <td>{{ $invoice->client->name }}</td>
                                <td>{{ $invoice->invoice_date }}</td>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="{{ route('invoices.index') }}" class="btn btn-link btn-sm">Voir toutes les factures</a>
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Actions Rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary text-start">
                            📄 Nouvelle Facture
                        </a>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary text-start">
                            👥 Gérer les Clients
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary text-start">
                            👤 Gérer les Utilisateurs
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-dark text-start">
                            ⚙️ Paramètres Société
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
