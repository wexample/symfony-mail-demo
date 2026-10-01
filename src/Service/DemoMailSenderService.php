<?php

namespace Wexample\SymfonyMailDemo\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Wexample\SymfonyMailDemo\Enum\DemoMailSample;
use Wexample\SymfonyTranslations\Translation\Translator;

/**
 * Sends a sample mail to the demo address. Every text, subject included,
 * comes from the yml next to the template, read as `@mail::`.
 *
 * Handed to the transport, not to the mailer: an application routing
 * SendEmailMessage to a queue would keep the sample there until a worker
 * runs, and the visitor would find the mailbox empty.
 */
class DemoMailSenderService
{
    public const string RECIPIENT = 'demo@example.com';

    private const string SENDER = 'Mail demo <noreply@example.com>';

    private const string TEMPLATES_PATH = '@WexampleSymfonyMailDemoBundle/mails/';

    public function __construct(
        private readonly TransportInterface $transport,
        private readonly Translator $translator,
    ) {
    }

    public function send(DemoMailSample $sample): void
    {
        $template = self::TEMPLATES_PATH.$sample->value.(DemoMailSample::PLAIN === $sample ? '.txt.twig' : '.html.twig');

        // Held for the rendering too, which the transport does within send().
        $this->translator->setDomainFromTemplatePath(Translator::DOMAIN_TYPE_MAIL, $template);

        try {
            $email = (new TemplatedEmail())
                ->from(self::SENDER)
                ->to(self::RECIPIENT)
                ->subject($this->translator->trans('@mail::subject'))
                ->context(['recipient' => self::RECIPIENT]);

            if (DemoMailSample::PLAIN === $sample) {
                $email->textTemplate($template);
            } else {
                $email->htmlTemplate($template);
            }

            if (DemoMailSample::INVOICE === $sample) {
                $email->attach($this->buildInvoice(), 'invoice-2026-001.txt', 'text/plain');
            }

            $this->transport->send($email);
        } finally {
            $this->translator->revertDomain(Translator::DOMAIN_TYPE_MAIL);
        }
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
