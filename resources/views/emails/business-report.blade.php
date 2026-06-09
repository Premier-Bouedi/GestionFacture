<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport {{ $reportType === 'monthly' ? 'Mensuel' : 'Journalier' }} — FAC+</title>
</head>
<body style="margin:0;padding:0;background:#f0f2f5;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f0f2f5;padding:40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0"
                       style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.1);">

                    {{-- EN-TÊTE --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#1a1a2e 0%,#0f3460 100%);padding:36px 40px;text-align:center;">
                            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;letter-spacing:1px;">
                                📊 FAC+
                            </h1>
                            <p style="margin:8px 0 0;color:rgba(255,255,255,0.75);font-size:14px;">
                                Rapport {{ $reportType === 'monthly' ? 'Mensuel' : 'Journalier' }}
                                — {{ now()->format('d/m/Y') }}
                            </p>
                        </td>
                    </tr>

                    {{-- CORPS --}}
                    <tr>
                        <td style="padding:40px;">

                            <p style="color:#555;font-size:15px;line-height:1.6;">
                                Bonjour,<br><br>
                                Voici votre rapport d'activité {{ $reportType === 'monthly' ? 'du mois' : 'du jour' }}
                                <strong>{{ $reportType === 'monthly' ? now()->format('F Y') : now()->format('d/m/Y') }}</strong>.
                            </p>

                            {{-- MÉTRIQUES CLÉS --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:24px 0;">
                                <tr>
                                    {{-- CA --}}
                                    <td width="33%" style="padding:4px;">
                                        <div style="background:linear-gradient(135deg,#1a1a2e,#0f3460);border-radius:12px;padding:20px;text-align:center;">
                                            <div style="font-size:22px;font-weight:700;color:#fff;">
                                                {{ number_format($data['ca'] ?? 0, 0, ',', ' ') }}
                                            </div>
                                            <div style="font-size:11px;color:rgba(255,255,255,0.7);margin-top:4px;text-transform:uppercase;letter-spacing:1px;">
                                                CA (FCFA)
                                            </div>
                                        </div>
                                    </td>
                                    {{-- NB FACTURES --}}
                                    <td width="33%" style="padding:4px;">
                                        <div style="background:#f8f9fa;border:2px solid #e9ecef;border-radius:12px;padding:20px;text-align:center;">
                                            <div style="font-size:22px;font-weight:700;color:#1a1a2e;">
                                                {{ $data['nb_factures'] ?? 0 }}
                                            </div>
                                            <div style="font-size:11px;color:#888;margin-top:4px;text-transform:uppercase;letter-spacing:1px;">
                                                Factures
                                            </div>
                                        </div>
                                    </td>
                                    {{-- NB CLIENTS --}}
                                    <td width="33%" style="padding:4px;">
                                        <div style="background:#f8f9fa;border:2px solid #e9ecef;border-radius:12px;padding:20px;text-align:center;">
                                            <div style="font-size:22px;font-weight:700;color:#1a1a2e;">
                                                {{ $data['nb_clients'] ?? 0 }}
                                            </div>
                                            <div style="font-size:11px;color:#888;margin-top:4px;text-transform:uppercase;letter-spacing:1px;">
                                                Clients
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            {{-- MEILLEUR PRODUIT --}}
                            @if(!empty($data['top_product']))
                            <div style="background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:16px 20px;margin:20px 0;">
                                <div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#92400e;font-weight:600;">
                                    🏆 Meilleur produit {{ $reportType === 'monthly' ? 'du mois' : 'du jour' }}
                                </div>
                                <div style="font-size:16px;font-weight:700;color:#1a1a2e;margin-top:6px;">
                                    {{ $data['top_product'] }}
                                </div>
                            </div>
                            @endif

                            {{-- PRODUITS EN RUPTURE --}}
                            @if(!empty($data['ruptures']) && count($data['ruptures']) > 0)
                            <div style="background:#fff1f2;border-left:4px solid #ef4444;border-radius:0 8px 8px 0;padding:16px 20px;margin:20px 0;">
                                <div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#991b1b;font-weight:600;">
                                    ⚠️ Produits en rupture de stock
                                </div>
                                <ul style="margin:8px 0 0;padding-left:20px;color:#1a1a2e;">
                                    @foreach($data['ruptures'] as $rupture)
                                        <li style="margin-top:4px;">{{ $rupture }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <hr style="border:none;border-top:1px solid #eee;margin:30px 0;">

                            <p style="color:#888;font-size:13px;text-align:center;">
                                Ce rapport a été généré automatiquement par <strong>FAC+</strong>.<br>
                                Pour modifier les préférences de notification, rendez-vous dans les Paramètres.
                            </p>

                        </td>
                    </tr>

                    {{-- PIED DE PAGE --}}
                    <tr>
                        <td style="background:#f8f9fa;padding:20px 40px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#aaa;font-size:12px;">
                                © {{ date('Y') }} FAC+ — Système de gestion de facturation
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
