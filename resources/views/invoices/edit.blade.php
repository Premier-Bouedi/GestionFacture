@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Modifier la Facture {{ $invoice->number }}</h2>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Retour</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Client <span class="text-danger">*</span></label>
                            <select name="client_id" id="client_select" class="form-control @error('client_id') is-invalid @enderror">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $invoice->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                                <option value="new" {{ old('client_id') == 'new' ? 'selected' : '' }}>+ Ajouter un nouveau client</option>
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="new_client_fields" style="display: {{ old('client_id') == 'new' ? 'block' : 'none' }};" class="border p-3 mb-3 bg-light rounded">
                            <h6 class="mb-3 text-primary">Informations du Nouveau Client</h6>
                            <div class="mb-2">
                                <input type="text" name="new_client_name" placeholder="Nom complet" class="form-control @error('new_client_name') is-invalid @enderror" value="{{ old('new_client_name') }}">
                                @error('new_client_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <input type="email" name="new_client_email" placeholder="Email" class="form-control @error('new_client_email') is-invalid @enderror" value="{{ old('new_client_email') }}">
                                @error('new_client_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Date de Facture <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', $invoice->invoice_date) }}">
                            @error('invoice_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Produits</h5>
                <div id="product-rows">
                    @php 
                        $oldProducts = old('products', $invoice->products->map(fn($p) => ['id' => $p->id, 'quantity' => $p->pivot->quantity])->toArray());
                    @endphp

                    @foreach($oldProducts as $index => $oldProduct)
                        <div class="row mb-3 product-row align-items-end">
                            <div class="col-md-7">
                                <label class="form-label">Produit</label>
                                <select name="products[{{ $index }}][id]" class="form-control" required>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $oldProduct['id'] == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->formatted_price }} - Stock: {{ $product->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantité</label>
                                <input type="number" name="products[{{ $index }}][quantity]" class="form-control" min="1" value="{{ $oldProduct['quantity'] }}" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger remove-product w-100">Supprimer</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-outline-primary mb-4" id="add-product">
                    + Ajouter un produit
                </button>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Mettre à jour la Facture</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('client_select').addEventListener('change', function() {
        document.getElementById('new_client_fields').style.display = (this.value === 'new') ? 'block' : 'none';
    });

    let rowIdx = {{ count($oldProducts) }};
    document.getElementById('add-product').addEventListener('click', function() {
        const container = document.getElementById('product-rows');
        const row = document.createElement('div');
        row.className = 'row mb-3 product-row align-items-end';
        row.innerHTML = `
            <div class="col-md-7">
                <select name="products[${rowIdx}][id]" class="form-control" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }} ({{ $product->formatted_price }} - Stock: {{ $product->stock }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="products[${rowIdx}][quantity]" class="form-control" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger remove-product w-100">Supprimer</button>
            </div>
        `;
        container.appendChild(row);
        rowIdx++;
    });

    document.getElementById('product-rows').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product')) {
            const rows = document.querySelectorAll('.product-row');
            if (rows.length > 1) {
                e.target.closest('.product-row').remove();
            } else {
                alert('La facture doit contenir au moins un produit.');
            }
        }
    });
</script>
@endsection
