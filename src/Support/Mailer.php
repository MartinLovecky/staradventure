<?php

namespace Mlkali\Sa\Support;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer extends PHPMailer
{
    public function subject($subject): void
    {
        $this->Subject = $subject;
    }

    public function body($body): void
    {
        $this->Body = $body;
    }

    public function sender(string $body, string $subject, string $to): bool
    {
        $this->IsSMTP();
        $this->body($body);
        $this->Host = $_ENV['EMAIL_HOST'];
        $this->SMTPDebug = false;
        $this->CharSet = 'utf-8';
        $this->SMTPAuth = true;
        $this->Username = $_ENV['EMAIL_NAME'];
        $this->Password = $_ENV['EMAIL_PASS'];
        $this->SMTPSecure = 'ssl';
        $this->Port = $_ENV['EMAIL_PORT'];
        $this->subject($subject);
        $this->isHTML(true);
        $this->setFrom($_ENV['EMAIL_NAME'], 'sadventure.com');
        $this->addAddress($to);
        //$this->addAttachment
        return parent::send();
    }
}
