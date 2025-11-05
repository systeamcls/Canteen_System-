<?php

namespace App\Mail;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Email;

class SendGridApiTransport extends AbstractTransport
{
    protected $client;
    protected $key;

    public function __construct($key)
    {
        parent::__construct();
        
        $this->key = $key;
        $this->client = new Client([
            'base_uri' => 'https://api.sendgrid.com/v3/',
            'timeout' => 10,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
        try {
            $payload = $this->buildPayload($email);

            $response = $this->client->post('mail/send', [
                'json' => $payload,
            ]);

            Log::info('Email sent successfully via SendGrid', [
                'to' => $this->getRecipients($email),
                'subject' => $email->getSubject(),
            ]);

        } catch (RequestException $e) {
            Log::error('SendGrid API Error', [
                'message' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            throw $e;
        }
    }

    protected function buildPayload(Email $email): array
    {
        $from = $email->getFrom()[0];
        $fromEmail = $from->getAddress();
        $fromName = $from->getName();

        $to = [];
        foreach ($email->getTo() as $recipient) {
            $to[] = [
                'email' => $recipient->getAddress(),
                'name' => $recipient->getName() ?? '',
            ];
        }

        // Get email body
        $htmlBody = $email->getHtmlBody();
        $textBody = $email->getTextBody();

        $content = [];
        if ($htmlBody) {
            $content[] = [
                'type' => 'text/html',
                'value' => $htmlBody,
            ];
        } elseif ($textBody) {
            $content[] = [
                'type' => 'text/plain',
                'value' => $textBody,
            ];
        }

        return [
            'personalizations' => [
                [
                    'to' => $to,
                    'subject' => $email->getSubject(),
                ],
            ],
            'from' => [
                'email' => $fromEmail,
                'name' => $fromName,
            ],
            'content' => $content,
        ];
    }

    protected function getRecipients(Email $email): array
    {
        $recipients = [];
        foreach ($email->getTo() as $recipient) {
            $recipients[] = $recipient->getAddress();
        }
        return $recipients;
    }

    public function __toString(): string
    {
        return 'sendgrid+api';
    }
}