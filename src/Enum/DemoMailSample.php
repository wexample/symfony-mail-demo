<?php

namespace Wexample\SymfonyMailDemo\Enum;

/**
 * The mails the demo sends, one per shape a mailbox has to show: an HTML
 * mail with a link, a mail with an attachment, a text-only mail.
 */
enum DemoMailSample: string
{
    case WELCOME = 'welcome';
    case INVOICE = 'invoice';
    case PLAIN = 'plain';

    public function getIcon(): string
    {
        return match ($this) {
            self::WELCOME => 'ph:bold/hand-waving',
            self::INVOICE => 'ph:bold/paperclip',
            self::PLAIN => 'ph:bold/text-aa',
        };
    }
}
