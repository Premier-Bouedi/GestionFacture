@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Facture N° {{ $invoice->number }}</h2>
        <div class="d-flex">
            <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-danger me-2">Télécharger PDF</a>
            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Supprimer cette facture ?');" class="me-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Supprimer</button>
            </form>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Client :</strong> {{ $invoice->client->name }}</p>
                    <p><strong>Email :</strong> {{ $invoice->client->email }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><strong>Date :</strong> {{ $invoice->invoice_date }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Produit</th>
                        <th class="text-end">Prix Unitaire</th>
                        <th class="text-center">Quantité</th>
                        <th class="text-end">Sous-total HT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->products as $product)
                        @php
                            $sub = $product->pivot->unit_price * $product->pivot->quantity;
                        @endphp
                        <tr>
                            <td>{{ $product->designation }}</td>
                            <td class="text-end">{{ number_format($product->pivot->unit_price, 2, ',', ' ') }} DH</td>
                            <td class="text-center">{{ $product->pivot->quantity }}</td>
                            <td class="text-end">{{ number_format($sub, 2, ',', ' ') }} DH</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <td colspan="3" class="text-end">Total HT</td>
                        <td class="text-end">{{ number_format($invoice->total_ht, 2, ',', ' ') }} DH</td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="3" class="text-end">TVA (20%)</td>
                        <td class="text-end">{{ number_format($invoice->total_tva, 2, ',', ' ') }} DH</td>
                    </tr>
                    <tr class="table-primary">
                        <td colspan="3" class="text-end"><strong>TOTAL TTC</strong></td>
                        <td class="text-end"><strong>{{ number_format($invoice->total_ttc, 2, ',', ' ') }} DH</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
