@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Générer une Nouvelle Facture</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('invoices.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Client <span class="text-danger">*</span></label>
                        <select name="client_id" id="client_select" class="form-control @error('client_id') is-invalid @enderror">
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                            <option value="new" {{ old('client_id') == 'new' ? 'selected' : '' }}>+ Ajouter un nouveau client</option>
                        </select>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="new_client_fields" class="border p-3 mb-3 bg-light rounded {{ old('client_id') == 'new' ? '' : 'd-none' }}">
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

                    <div class="mb-3">
                        <label class="form-label">Date de Facture <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', date('Y-m-d')) }}">
                        @error('invoice_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h5>Produits</h5>
                    <div id="product-rows">
                        <div class="row g-2 mb-2 product-row">
                            <div class="col-md-7">
                                <select name="products[0][id]" class="form-control">
                                    <option value="">Sélectionner un produit</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->designation }} ({{ $product->prix_unitaire }} DH)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="products[0][quantity]" class="form-control" placeholder="Quantité" min="1" value="1">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger w-100 remove-row">X</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-product">Ajouter un produit</button>

                    @error('products')
                        <div class="text-danger mb-3">{{ $message }}</div>
                    @enderror

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Enregistrer la Facture</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('client_select').addEventListener('change', function() {
        const fields = document.getElementById('new_client_fields');
        if (this.value === 'new') {
            fields.classList.remove('d-none');
        } else {
            fields.classList.add('d-none');
        }
    });

    let rowIdx = 1;
    document.getElementById('add-product').addEventListener('click', function() {
        const container = document.getElementById('product-rows');
        const row = document.querySelector('.product-row').cloneNode(true);
        
        row.querySelector('select').name = `products[${rowIdx}][id]`;
        row.querySelector('input').name = `products[${rowIdx}][quantity]`;
        row.querySelector('input').value = 1;
        
        container.appendChild(row);
        rowIdx++;
    });

    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('remove-row')) {
            const rows = document.querySelectorAll('.product-row');
            if(rows.length > 1) {
                e.target.closest('.product-row').remove();
            }
        }
    });
</script>
@endsection
