<?php

declare(strict_types=1);

namespace Mlkali\Sa\Http;

use Mlkali\Sa\Engine\Blade;
use Mlkali\Sa\Support\Arr;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer extends PHPMailer
{
    public function __construct(private Blade $blade)
    {
    }

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
        $dir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
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
        $this->addEmbeddedImage("{$dir}public/img/favicon_io/android-chrome-192x192.png", 'image_cid');
        return parent::send();
    }

    public function sendMail(string $t, array $d): void
    {
        $emailData = $this->getEmailData(template:$t, data:$d);
        $this->sender(
            body: $emailData['body'],
            subject: $emailData['subject'],
            to: $emailData['to']
        );
    }

    private function getEmailData(string $template, array $data = []): array
    {
        $body = $this->emailMessage(t:$template, d:$data);
        $subject = 'SA|' . $this->emailSubject(t:$template);

        return [
            'body' => $body,
            'subject' => $subject,
            'to' => $data['email']
        ];
    }

    private function emailMessage(string $t, array $d): string
    {
        $path = Arr::$path . "views/templates/";
        $tFile = "{$path}{$t}.blade.php";
        if (!is_file($tFile)) {
            throw new \Exception("Template: {$t}.blade.php not found at {$path}");
        }

        return $this->blade->run('templates.' . $t, $d);
    }

    private function emailSubject(string $t): string
    {
        return match ($t) {
            'reset' => 'Reset hesla',
            'activate' => 'Potvrzení registrace',
            'user' => 'Zapomenutý username',
            default => 'No Subject'
        };
    }
}
