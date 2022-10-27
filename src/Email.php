<?php

namespace Atelier;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class Email
{
    public static function send(string $toEmail, string $subject, string $body): bool
    {
        if (Settings::getByName('smtp_email')) {
            $login = explode('@', Settings::getByName('smtp_email'))[0];
            $dsn = "smtp://$login:" . Settings::getByName('smtp_password') . '@' . Settings::getByName('smtp_host') . ':' . Settings::getByName('smtp_port');
            $transport = Transport::fromDsn($dsn);
            $mailer = new Mailer($transport);
            $email = (new \Symfony\Component\Mime\Email())
                ->from(Settings::getByName('smtp_email'))
                ->to($toEmail)
                ->subject($subject)
                ->html($body);
            $mailer->send($email);

            return true;
        } else {
            Logger::error('Укажите настройку SMTP_EMAIL');

            return false;
        }
    }
}