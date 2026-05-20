<?php
// src/Service/DocumentReceiptService.php
namespace App\Service;

use App\Entity\Document;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumentReceiptService
{
    private string $uploadDir;

    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        string $kernelProjectDir
    ) {
        $this->uploadDir = $kernelProjectDir . '/public/uploads/documents';
    }

    public function generateAndSendReceipt(Document $document, UserInterface $user): array
    {
        try {
            // 1. Génération du PDF
            $pdfPath = $this->generateReceiptPdf($document, $user);

            // 2. Envoi par email
            $this->sendReceiptEmail($document, $user, $pdfPath);

            $this->logger->info("Receipt PDF generated and sent", [
                'document_id' => $document->getId(),
                'user_id' => $user->getId(),
                'email' => $user->getEmail()
            ]);

            return [
                'success' => true,
                'message' => 'Certificat de réception généré et envoyé par email.'
            ];

        } catch (\Exception $e) {
            $this->logger->error("Error generating/sending receipt", [
                'error' => $e->getMessage(),
                'document_id' => $document->getId(),
                'user_id' => $user->getId()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de la génération/envoi du certificat : ' . $e->getMessage()
            ];
        }
    }

    private function generateReceiptPdf(Document $document, UserInterface $user): string
    {
        $options = new Options();
        $options->setDefaultFont('Arial');
        $options->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($options);

        $html = $this->renderReceiptHtml($document, $user);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfPath = $this->uploadDir . '/receipts/receipt_' . $document->getId() . '_' . time() . '.pdf';

        // Création du dossier receipts s'il n'existe pas
        $receiptDir = $this->uploadDir . '/receipts';
        if (!is_dir($receiptDir)) {
            mkdir($receiptDir, 0775, true);
        }

        file_put_contents($pdfPath, $dompdf->output());

        return $pdfPath;
    }

    private function renderReceiptHtml(Document $document, User $user): string
    {
        return "
        <html lang='fr'>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                h1 { color: #2c3e50; }
                .info { margin: 20px 0; }
                .footer { margin-top: 50px; font-size: 0.9em; color: #7f8c8d; }
            </style>
        </head>
        <body>
            <h1>Certificat de Réception</h1>
            <p><strong>Document :</strong> {$document->getTitle()}</p>
            <p><strong>Fichier :</strong> {$document->getOriginalName()}</p>
            <p><strong>Catégorie :</strong> " . ($document->getCategory()?->getLabel() ?? 'Non classé') . "</p>

            <div class='info'>
                <p><strong>Reçu par :</strong> {$user->getEmail()}</p>
                <p><strong>Date :</strong> " . (new \DateTime())->format('d/m/Y à H:i') . "</p>
            </div>

            <div class='footer'>
                <p>Ce document certifie que le fichier a bien été reçu et stocké sur nos serveurs.</p>
                <p>DocShare - " . date('Y') . "</p>
            </div>
        </body>
        </html>";
    }

    private function sendReceiptEmail(Document $document, UserInterface $user, string $pdfPath): void
    {

        $mail = (new TemplatedEmail())
            ->from(new Address('noreply@docshare.fr', 'DocShare'))
            ->to($user->getEmail())
            ->subject("Réception de votre document : {$document->getTitle()}")
            ->htmlTemplate('mail/confirm-depot.html.twig')
            ->context(['user' => $user,'document' => $document,])
            ->attachFromPath($pdfPath, 'Certificat_Reception_' . $document->getTitle() . '.pdf');

        $this->mailer->send($mail);
    }
}
