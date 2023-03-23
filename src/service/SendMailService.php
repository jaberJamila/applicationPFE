<?php
namespace App\service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
       $this->mailer = $mailer;
    }
    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void 
    {
        // on crée le mail
        $mail = (new TemplatedEmail())
               ->from($from)
               ->to($to)
               ->subject($subject)
               ->htmlTemplate("emails/$template.html.twig")
               ->context($context);

               // en envoie le mail
               $this->mailer->send($mail);
    }
}