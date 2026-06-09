<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice->number }}</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    {{-- EN-TÊTE --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 36px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: 1px;">
                                📄 FAC+
                            </h1>
                            <p style="margin: 8px 0 0; color: #94a3b8; font-size: 14px;">
                                Gestion de Facturation Professionnelle
                            </p>
                        </td>
                    </tr>

                    {{-- CORPS --}}
                    <tr>
                        <td style="padding: 40px;">
                            {{-- Salutation --}}
                            <p style="margin: 0 0 20px; font-size: 16px; color: #1e293b;">
                                Bonjour <strong>{{ $invoice->client->name }}</strong>,
                            </p>

                            <p style="margin: 0 0 28px; font-size: 15px; color: #475569; line-height: 1.6;">
                                Veuillez trouver ci-joint votre facture. Nous vous remercions pour votre confiance.
                            </p>

                            {{-- CARTE RÉSUMÉ --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Facture N°</span>
                                                </td>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                    <strong style="color: #1e293b; font-size: 15px;">{{ $invoice->number }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Date</span>
                                                </td>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                    <strong style="color: #1e293b; font-size: 15px;">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Total HT</span>
                                                </td>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                    <span style="color: #475569; font-size: 15px;">{{ number_format($invoice->total_ht, 2, ',', ' ') }} DH</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">TVA (20%)</span>
                                                </td>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                    <span style="color: #475569; font-size: 15px;">{{ number_format($invoice->total_tva, 2, ',', ' ') }} DH</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 14px 0 4px;">
                                                    <span style="color: #1e293b; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Total TTC</span>
                                                </td>
                                                <td style="padding: 14px 0 4px; text-align: right;">
                                                    <span style="color: #2563eb; font-size: 22px; font-weight: 800;">{{ number_format($invoice->total_ttc, 2, ',', ' ') }} DH</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- MESSAGE PDF --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 0 8px 8px 0; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0; color: #1e40af; font-size: 14px;">
                                            📎 <strong>Le document PDF de votre facture est joint à cet email.</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- SIGNATURE --}}
                            <p style="margin: 0; font-size: 15px; color: #475569; line-height: 1.6;">
                                Cordialement,<br>
                                <strong style="color: #1e293b;">L'équipe {{ config('app.name') }}</strong>
                            </p>
                        </td>
                    </tr>

                    {{-- PIED DE PAGE --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 40px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0 0 4px; color: #94a3b8; font-size: 12px;">
                                Cet email a été généré automatiquement par FAC+ — Gestion de Facturation.
                            </p>
                            <p style="margin: 0; color: #cbd5e1; font-size: 11px;">
                                © {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>