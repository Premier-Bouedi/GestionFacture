@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Liste des Factures</h2>
    <a href="{{ route('invoices.create') }}" class="btn btn-success">Générer une Facture</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total TTC</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->number ?? 'N/A' }}</td>
                        <td>{{ $invoice->client->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</td>
                        <td><strong>{{ number_format($invoice->total_ttc, 2, ',', ' ') }} DH</strong></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-info text-white">Voir</a>
                                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-sm btn-warning text-white">Edit</a>
                                <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-sm btn-outline-danger">PDF</a>
                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Supprimer cette facture ?');" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Suppr</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $invoices->links() }}
    </div>
</div>
@endsection
