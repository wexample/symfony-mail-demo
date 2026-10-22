<?php

namespace Wexample\SymfonyMailDemo\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Wexample\SymfonyMail\Service\MailSenderService;
use Wexample\SymfonyMailDemo\Enum\DemoMailSample;

/**
 * Sends a sample mail to the demo address, through the sender every mail of
 * an application goes through: texts beside the template, layout, locale.
 */
class DemoMailSenderService
{
    public const string RECIPIENT = 'demo@example.com';

    private const string SENDER = 'Mail demo <noreply@example.com>';

    private const string TEMPLATES_PATH = '@WexampleSymfonyMailDemoBundle/mails/';

    public function __construct(
        private readonly MailSenderService $sender,
    ) {
    }

    /**
     * @param string $locale the visitor's, so the sample reads in the language of the page
     */
    public function send(
        DemoMailSample $sample,
        string $locale
    ): void {
        $email = (new TemplatedEmail())
            ->from(self::SENDER)
            ->to(self::RECIPIENT)
            ->context(['recipient' => self::RECIPIENT]);

        if (DemoMailSample::PLAIN === $sample) {
            $email->textTemplate(self::TEMPLATES_PATH.$sample->value.'.txt.twig');
        } else {
            $email->htmlTemplate(self::TEMPLATES_PATH.$sample->value.'.html.twig');
        }

        if (DemoMailSample::INVOICE === $sample) {
            $email->attach($this->buildInvoice(), 'invoice-2026-001.txt', 'text/plain');
        }

        $this->sender->send($email, $locale);
    }

    private function buildInvoice(): string
    {
        return implode("\n", [
            'INVOICE 2026-001',
            '',
            '1 x Design system demo     0.00 EUR',
            'Total                      0.00 EUR',
        ]);
    }
}
