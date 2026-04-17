<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailService
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function sendNotificationEmail(string $toEmail, string $userName, string $message): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@flahasmart.com', 'FlahaSmart Notifications'))
            ->to($toEmail)
            ->subject('🔔 Nouvelle notification sur FlahaSmart')
            ->htmlTemplate('emails/notification.html.twig')
            ->context([
                'userName' => $userName,
                'message' => $message,
                'date' => new \DateTime(),
            ]);

        $this->mailer->send($email);
    }
}
