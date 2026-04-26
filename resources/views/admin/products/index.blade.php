@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion du Stock & Produits</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            📦 Ajouter un Produit
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Désignation</th>
                        <th>Description</th>
                        <th class="text-end">Prix Unitaire</th>
                        <th class="text-center">Stock</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td><strong>{{ $product->designation }}</strong></td>
                        <td class="text-muted small">{{ Str::limit($product->description, 50) }}</td>
                        <td class="text-end">{{ number_format($product->prix_unitaire, 2, ',', ' ') }} DH</td>
                        <td class="text-center">
                            @if($product->stock <= 5)
                                <span class="badge bg-danger">Alerte : {{ $product->stock }}</span>
                            @elseif($product->stock <= 20)
                                <span class="badge bg-warning text-dark">{{ $product->stock }}</span>
                            @else
                                <span class="badge bg-success">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-outline-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editProductModal{{ $product->id }}">
                                    Modifier
                                </button>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Supprimer ce produit ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Suppr</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Modifier {{ $product->designation }}</h5>
                                        <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Désignation</label>
                                            <input type="text" name="designation" class="form-control" value="{{ $product->designation }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Prix Unitaire (DH)</label>
                                            <input type="number" step="0.01" name="prix_unitaire" class="form-control" value="{{ $product->prix_unitaire }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Stock Actuel</label>
                                            <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description (Optionnel)</label>
                                            <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-close="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Aucun produit en stock.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Nouveau Produit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-close="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Désignation</label>
                        <input type="text" name="designation" class="form-control" placeholder="ex: Ordinateur HP" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Prix Unitaire (DH)</label>
                        <input type="number" step="0.01" name="prix_unitaire" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Stock Initial</label>
                        <input type="number" name="stock" class="form-control" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Informations complémentaires..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-close="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary px-4">Créer le produit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
