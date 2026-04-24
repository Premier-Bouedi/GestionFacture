@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Clients</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Retour au Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom du Client</th>
                        <th>Email</th>
                        <th style="text-align: center;">Nombre de Factures</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                    <tr>
                        <td class="fw-bold">{{ $client->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td style="text-align: center;">
                            <span class="badge bg-info text-white">{{ $client->invoices_count }}</span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Supprimer ce client ? Cela ne supprimera pas ses factures (elles resteront liées).');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
