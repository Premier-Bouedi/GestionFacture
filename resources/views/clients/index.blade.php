@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users me-2 text-primary"></i>Gestion des Clients</h2>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClientModal">
                <i class="fas fa-plus me-1"></i>Nouveau Client
            </button>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Bandeau Statistiques --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-white bg-primary">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="display-4"><i class="fas fa-users"></i></div>
                    <div>
                        <h5 class="card-title mb-0">Nombre total de clients</h5>
                        <h2 class="fw-bold mb-0">{{ $totalClients }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recherche --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, email ou téléphone..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Tableau des Clients --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom & Prénom</th>
                            <th>Numéro de téléphone</th>
                            <th>Adresse E-mail</th>
                            <th>Adresse physique</th>
                            <th class="text-center">Nombre de factures</th>
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <th class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                        <tr>
                            <td class="fw-bold">{{ $client->name }}</td>
                            <td>{{ $client->phone ?? 'Non renseigné' }}</td>
                            <td>{{ $client->email ?? 'Non renseigné' }}</td>
                            <td>{{ $client->address ?? 'Non renseigné' }}</td>
                            <td class="text-center">
                                <a href="{{ route('invoices.index', ['search' => $client->name]) }}" class="text-decoration-none">
                                    <span class="badge bg-primary rounded-pill px-3 py-2" style="cursor: pointer;" title="Voir les factures">
                                        {{ $client->invoices_count }} {{ Str::plural('facture', $client->invoices_count) }}
                                    </span>
                                </a>
                            </td>
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <td class="text-center">
                                    <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce client ? Ses factures seront conservées.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ (auth()->check() && auth()->user()->isAdmin()) ? '6' : '5' }}" class="text-center text-muted py-4">
                                <i class="fas fa-users-slash fs-1 d-block mb-3 opacity-25"></i>
                                Aucun client trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Création de Client --}}
<div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createClientModalLabel"><i class="fas fa-user-plus me-2 text-primary"></i>Nouveau Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom & Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Ex: Jean Dupont" value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Numéro de téléphone</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Ex: +212 600 000 000" value="{{ old('phone') }}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Ex: jean.dupont@email.com" value="{{ old('email') }}">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse physique</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Ex: 123 Rue de la Liberté, Casablanca">{{ old('address') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Enregistrer le client</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
