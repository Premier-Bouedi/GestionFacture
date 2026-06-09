@extends('layouts.app')

@section('content')
{{-- ===== PAGE D'ERREUR 403 — Accès Refusé ===== --}}
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh;">

    {{-- Icône grande --}}
    <div class="mb-4" style="font-size: 5rem; line-height: 1;">
        🔒
    </div>

    {{-- Code d'erreur --}}
    <h1 class="display-1 fw-bold text-danger mb-2">403</h1>

    {{-- Message principal --}}
    <h2 class="h4 fw-semibold text-dark mb-3">Vous n'avez pas ce droit</h2>

    {{-- Description --}}
    <p class="text-muted text-center mb-4" style="max-width: 420px;">
        Cette zone est réservée aux administrateurs. 
        Si vous pensez qu'il s'agit d'une erreur, contactez votre responsable.
    </p>

    {{-- Badge rôle actuel --}}
    @auth
        <div class="mb-4">
            <span class="badge bg-secondary fs-6 px-3 py-2">
                <i class="fas fa-user me-1"></i>
                Rôle actuel : {{ Auth::user()->role }}
            </span>
        </div>
    @endauth

    {{-- Boutons de retour --}}
    <div class="d-flex gap-3">
        <a href="{{ route('invoices.index') }}" class="btn btn-primary px-4">
            <i class="fas fa-home me-2"></i>Retour à l'accueil
        </a>
        <a href="javascript:history.back()" class="btn btn-outline-secondary px-4">
            <i class="fas fa-arrow-left me-2"></i>Page précédente
        </a>
    </div>

</div>
@endsection
