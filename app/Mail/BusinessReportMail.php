<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * BusinessReportMail — E-mail de rapport d'activité (bilan jour ou bilan mois).
 *
 * Contient les données calculées en PHP (CA, nb factures, meilleur produit...)
 * et utilise la vue emails.business-report pour le rendu HTML.
 */
class BusinessReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array  $reportData   Données du rapport (CA, factures, etc.)
     * @param string $reportType   'daily' ou 'monthly'
     */
    public function __construct(
        public readonly array  $reportData,
        public readonly string $reportType = 'daily'
    ) {}

    /**
     * Sujet de l'email selon le type de rapport.
     */
    public function envelope(): Envelope
    {
        $label = $this->reportType === 'monthly' ? 'Mensuel' : 'Journalier';
        $date  = now()->format('d/m/Y');

        return new Envelope(
            subject: "📊 Rapport {$label} FAC+ — {$date}",
        );
    }

    /**
     * Vue Blade utilisée pour le contenu de l'email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.business-report',
            with: [
                'data'       => $this->reportData,
                'reportType' => $this->reportType,
            ],
        );
    }
}
