@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🛡️ Gestion des Utilisateurs & Accès</h2>
        <div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                ➕ Ajouter un Membre
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Retour</a>
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

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Changer le Rôle</th>
                        <th>Mot de Passe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $badgeClass = 'bg-secondary';
                                if($user->role === 'admin') $badgeClass = 'bg-danger';
                                if($user->role === 'manager') $badgeClass = 'bg-success';
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                @method('PUT')
                                <select name="role" class="form-select form-select-sm" style="width: auto;">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>USER</option>
                                    <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>MANAGER</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>ADMIN</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Valider</button>
                            </form>
                        </td>
                        <td>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.forceReset', $user->id) }}" method="POST" onsubmit="return confirm('Réinitialiser le mot de passe de {{ $user->name }} ?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning">🔑 Reset MDP</button>
                            </form>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement {{ $user->name }} ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                            @else
                            <span class="badge bg-success">Vous</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm mt-4 border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">📋 Légende</h5>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li><strong>Changer le Rôle</strong> : Passer un utilisateur de <code>USER</code> à <code>ADMIN</code> (et inversement).</li>
                <li><strong>🔑 Reset MDP</strong> : Réinitialise le mot de passe à <code>password123</code>. L'utilisateur devra le changer à sa prochaine connexion.</li>
                <li><strong>Supprimer</strong> : Supprime définitivement le compte. <span class="text-danger">Irréversible.</span></li>
            </ul>
        </div>
    </div>

    <!-- Modal Ajouter Utilisateur -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">➕ Nouveau Membre de l'Équipe</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="name" class="form-control" placeholder="ex: Jean Dupont" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Professionnel</label>
                            <input type="email" name="email" class="form-control" placeholder="jean@facplus.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de Passe Provisoire</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimum 8 caractères" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rôle</label>
                            <select name="role" class="form-select" required>
                                <option value="user">Utilisateur simple</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Créer le compte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
