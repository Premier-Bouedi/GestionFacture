@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users-cog me-2 text-primary"></i>Gestion du Personnel (Caisses)</h2>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus me-1"></i>Ajouter un Membre
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== BANDEAU STATISTIQUES ===== --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-white bg-dark">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="display-4"><i class="fas fa-cash-register"></i></div>
                    <div>
                        <h6 class="card-title mb-0 opacity-75">Caissiers Actifs</h6>
                        <h2 class="fw-bold mb-0">{{ $totalCaissiers }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-white bg-danger">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="display-4"><i class="fas fa-user-shield"></i></div>
                    <div>
                        <h6 class="card-title mb-0 opacity-75">Administrateurs</h6>
                        <h2 class="fw-bold mb-0">{{ $totalAdmins }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-white bg-primary">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="display-4"><i class="fas fa-users"></i></div>
                    <div>
                        <h6 class="card-title mb-0 opacity-75">Total du Personnel</h6>
                        <h2 class="fw-bold mb-0">{{ $totalCaissiers + $totalAdmins }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TABLEAU DU PERSONNEL ===== --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Nom & Prénom</th>
                            <th>Adresse E-mail</th>
                            <th class="text-center">Rôle</th>
                            <th class="text-center">Nombre de factures</th>
                            <th class="text-center">Changer le Rôle</th>
                            <th class="text-center">Mot de Passe</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="fw-bold">
                                @if(auth()->id() === $user->id)
                                    <i class="fas fa-circle text-success me-1" style="font-size: 0.6em; vertical-align: middle;" title="C'est vous"></i>
                                @endif
                                {{ $user->name }}
                            </td>
                            <td>{{ $user->email }}</td>
                            <td class="text-center">
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger px-3 py-2"><i class="fas fa-shield-alt me-1"></i>ADMIN</span>
                                @else
                                    <span class="badge bg-success px-3 py-2"><i class="fas fa-cash-register me-1"></i>CAISSIER</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('invoices.index', ['search' => $user->name]) }}" class="text-decoration-none">
                                    <span class="badge bg-primary rounded-pill px-3 py-2" style="cursor: pointer;" title="Voir les factures de {{ $user->name }}">
                                        {{ $user->invoices_count }} {{ Str::plural('facture', $user->invoices_count) }}
                                    </span>
                                </a>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="d-inline-flex gap-2 align-items-center">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="form-select form-select-sm" style="width: auto;">
                                        <option value="caissier" {{ $user->role === 'caissier' ? 'selected' : '' }}>CAISSIER</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>ADMIN</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-check"></i></button>
                                </form>
                            </td>
                            <td class="text-center">
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.forceReset', $user->id) }}" method="POST" onsubmit="return confirm('Réinitialiser le mot de passe de {{ $user->name }} ?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Réinitialiser le mot de passe">
                                        <i class="fas fa-key"></i> Reset
                                    </button>
                                </form>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement {{ $user->name }} ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer cet utilisateur">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Vous</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== LÉGENDE ===== --}}
    <div class="card shadow-sm mt-4 border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Légende</h5>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li><strong>Changer le Rôle</strong> : Promouvoir/rétrograder un utilisateur entre <code>CAISSIER</code> et <code>ADMIN</code>.</li>
                <li><strong><i class="fas fa-key"></i> Reset</strong> : Réinitialise le mot de passe à <code>password123</code>. L'utilisateur devra le changer.</li>
                <li><strong><i class="fas fa-trash"></i> Supprimer</strong> : Supprime définitivement le compte. <span class="text-danger">Irréversible.</span></li>
                <li><strong>Badge Factures</strong> : Cliquez dessus pour voir toutes les factures du membre concerné.</li>
            </ul>
        </div>
    </div>

    {{-- ===== MODAL AJOUTER UN MEMBRE ===== --}}
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Nouveau Membre de l'Équipe</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Ex: Jean Dupont" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Professionnel <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="jean@facplus.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de Passe Provisoire <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimum 8 caractères" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rôle</label>
                            <select name="role" class="form-select" required>
                                <option value="caissier" selected>Caissier</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Créer le compte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
