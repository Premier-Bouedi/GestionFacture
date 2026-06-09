@extends('layouts.app')

@section('content')
{{-- ===== PAGE PARAMÈTRES NOTIFICATIONS ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-bell me-2 text-primary"></i>Paramètres de Notification
    </h2>
    <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Retour aux paramètres
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.settings.notifications.update') }}" method="POST">
    @csrf

    <div class="row g-4">

        {{-- ===== FENÊTRE A : WhatsApp ===== --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center gap-2" style="background:#25d366;">
                    <span style="font-size:20px;">📱</span>
                    <h5 class="mb-0 text-white fw-bold">Alertes WhatsApp</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">
                        Recevez des alertes instantanées sur WhatsApp via 
                        <a href="https://www.callmebot.com/" target="_blank" class="text-decoration-none">CallMeBot</a>.
                        Gratuit, sans application supplémentaire.
                    </p>

                    {{-- Numéro WhatsApp --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-phone me-1 text-success"></i>
                            Numéro WhatsApp (avec indicatif pays)
                        </label>
                        <input type="text"
                               name="whatsapp_number"
                               class="form-control @error('whatsapp_number') is-invalid @enderror"
                               value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}"
                               placeholder="Ex : +22670123456">
                        @error('whatsapp_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Format international requis, ex : +226 pour le Burkina, +33 pour la France.
                        </small>
                    </div>

                    {{-- Instructions CallMeBot --}}
                    <div class="alert alert-info py-2 small">
                        <strong>📋 Activation CallMeBot :</strong><br>
                        1. Ajoutez <code>+34 644 35 75 80</code> dans vos contacts.<br>
                        2. Envoyez : <code>I allow callmebot to send me messages</code><br>
                        3. Ajoutez la clé reçue dans votre fichier <code>.env</code> : <code>CALLMEBOT_API_KEY=...</code>
                    </div>

                    <hr>

                    <p class="fw-semibold mb-3">Recevoir une alerte quand :</p>

                    {{-- Case : Rupture de stock --}}
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox"
                               name="notify_whatsapp_rupture" value="1" id="wa_rupture"
                               {{ ($settings['notify_whatsapp_rupture'] ?? '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="wa_rupture">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                            <strong>Alerte Rupture de Stock</strong><br>
                            <small class="text-muted">Message envoyé si un produit passe sous 5 unités lors d'une vente.</small>
                        </label>
                    </div>

                    {{-- Case : Suivi des ventes --}}
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="notify_whatsapp_sales" value="1" id="wa_sales"
                               {{ ($settings['notify_whatsapp_sales'] ?? '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="wa_sales">
                            <i class="fas fa-chart-line text-success me-1"></i>
                            <strong>Suivi des Ventes</strong><br>
                            <small class="text-muted">Rapport WhatsApp envoyé à la fin de chaque journée.</small>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== FENÊTRE B : E-mail ===== --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center gap-2 bg-primary">
                    <span style="font-size:20px;">📧</span>
                    <h5 class="mb-0 text-white fw-bold">Rapports par E-mail</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">
                        Recevez des bilans d'activité complets (CA, factures, ruptures) 
                        directement dans votre boîte e-mail.
                    </p>

                    {{-- E-mail du gestionnaire --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-envelope me-1 text-primary"></i>
                            E-mail du Gestionnaire
                        </label>
                        <input type="email"
                               name="manager_email"
                               class="form-control @error('manager_email') is-invalid @enderror"
                               value="{{ old('manager_email', $settings['manager_email'] ?? '') }}"
                               placeholder="Ex : patron@maboutique.com">
                        @error('manager_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Adresse qui recevra les bilans automatiques.
                        </small>
                    </div>

                    <div class="alert alert-warning py-2 small">
                        <strong>⚙️ Configuration SMTP :</strong> Pour un envoi réel, configurez
                        <code>MAIL_MAILER</code>, <code>MAIL_HOST</code>, etc. dans votre <code>.env</code>.
                        En mode <code>log</code>, les emails sont enregistrés dans <code>storage/logs/laravel.log</code>.
                    </div>

                    <hr>

                    <p class="fw-semibold mb-3">Recevoir un bilan :</p>

                    {{-- Case : Bilan du Jour --}}
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox"
                               name="notify_email_daily" value="1" id="email_daily"
                               {{ ($settings['notify_email_daily'] ?? '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="email_daily">
                            <i class="fas fa-sun text-warning me-1"></i>
                            <strong>Bilan Journalier</strong><br>
                            <small class="text-muted">Rapport du CA, nombre de factures et ruptures chaque soir.</small>
                        </label>
                    </div>

                    {{-- Case : Bilan du Mois --}}
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="notify_email_monthly" value="1" id="email_monthly"
                               {{ ($settings['notify_email_monthly'] ?? '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="email_monthly">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>
                            <strong>Bilan Mensuel</strong><br>
                            <small class="text-muted">Rapport complet le 1er de chaque mois avec les stats du mois précédent.</small>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bouton de sauvegarde --}}
    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save me-2"></i>Enregistrer les préférences de notification
        </button>
    </div>

</form>
@endsection
