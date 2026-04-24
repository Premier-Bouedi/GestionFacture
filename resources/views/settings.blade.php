@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Paramètres de l'Application</h2>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Retour</a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Personnalisation</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4 text-center">
                            @if($companyLogo)
                                <img src="{{ asset('storage/' . $companyLogo) }}" alt="Logo" class="img-thumbnail mb-2" style="max-height: 100px;">
                                <p class="small text-muted">Logo actuel</p>
                            @else
                                <div class="p-4 bg-light border rounded mb-2">
                                    <span class="text-muted">Aucun logo</span>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nom de la Société</label>
                            <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $companyName) }}" placeholder="Ex: Ma Société SARL">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Logo de la Société</label>
                            <input type="file" name="company_logo" class="form-control @error('company_logo') is-invalid @enderror">
                            <small class="text-muted">Format recommandé : PNG ou SVG (max 2MB).</small>
                            @error('company_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        </div>
                    </form>

                    <hr class="my-4">
                    
                    <form action="{{ route('admin.settings.reset') }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir tout remettre à zéro ?');">
                        @csrf
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Remettre à zéro les paramètres</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Zone de Danger</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Attention : Cette action est irréversible. Toutes vos factures, clients et produits seront définitivement supprimés.</p>
                    
                    <form action="{{ route('admin.settings.reset') }}" method="POST" onsubmit="return confirm('Êtes-vous ABSOLUMENT sûr de vouloir réinitialiser toutes les données ? Cette action est irréversible.')">
                        @csrf
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash"></i> Réinitialiser toutes les données
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
