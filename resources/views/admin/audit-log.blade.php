@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📋 Journal d'Audit (Boîte Noire)</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Retour</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Élément</th>
                        <th>Description</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="small">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $log->user_name }}</strong></td>
                        <td>
                            @switch($log->action)
                                @case('created')
                                    <span class="badge bg-success">Création</span>
                                    @break
                                @case('updated')
                                    <span class="badge bg-warning text-dark">Modification</span>
                                    @break
                                @case('deleted')
                                    <span class="badge bg-danger">Suppression</span>
                                    @break
                                @case('restored')
                                    <span class="badge bg-info">Restauration</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $log->action }}</span>
                            @endswitch
                        </td>
                        <td>{{ $log->model_type }} #{{ $log->model_id }}</td>
                        <td class="small">{{ $log->description }}</td>
                        <td class="small text-muted">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Aucune action enregistrée pour le moment.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
