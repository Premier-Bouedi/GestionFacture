@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-dark text-center py-4">
                    <h4 class="mb-0">🔑 Mot de passe oublié</h4>
                    <p class="small mb-0">Contactez votre administrateur</p>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock" style="font-size: 3rem; color: #6c757d;"></i>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Procédure de réinitialisation :</strong>
                        <ol class="mb-0 mt-2">
                            <li>Contactez votre administrateur système.</li>
                            <li>Il réinitialisera votre mot de passe depuis l'<strong>Espace Admis</strong>.</li>
                            <li>Vous recevrez un mot de passe provisoire : <code>password123</code>.</li>
                            <li>Connectez-vous et changez-le immédiatement.</li>
                        </ol>
                    </div>

                    <div class="d-grid mt-4">
                        <a href="{{ route('login') }}" class="btn btn-secondary">
                            ← Retour à la connexion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
