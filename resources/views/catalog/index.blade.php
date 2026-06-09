@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-store text-primary"></i> Catalogue des Produits</h2>
    </div>

    <form method="GET"><input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher..."><button type="submit">🔍</button></form>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse($products as $product)
            <div class="col">
                <div class="card h-100 shadow-sm border-0" style="transition: transform 0.2s;">
                    <!-- Image du produit -->
                    @if($product->image)
                        <div class="bg-white text-center p-3" style="height: 220px;">
                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" alt="{{ $product->designation }}" style="max-height: 100%; object-fit: contain;">
                        </div>
                    @else
                        <div class="card-img-top bg-light d-flex justify-content-center align-items-center" style="height: 200px;">
                            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-dark">{{ $product->designation }}</h5>
                        
                        <p class="card-text text-muted small flex-grow-1">
                            {{ Str::limit($product->description ?? 'Aucune description disponible pour cet article.', 80) }}
                        </p>

                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold text-primary">{{ number_format($product->prix_unitaire, 2, ',', ' ') }} DH</span>
                            
                            @if($product->stock > 0)
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check"></i> En stock</span>
                            @else
                                <span class="badge bg-danger rounded-pill px-3 py-2"><i class="fas fa-times"></i> Épuisé</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3"><i class="fas fa-box-open" style="font-size: 4rem;"></i></div>
                <h4>Notre catalogue est vide pour le moment</h4>
                <p class="text-muted">Revenez plus tard pour découvrir nos nouveaux articles.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    /* Effet de survol sur les cartes du catalogue */
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endsection
