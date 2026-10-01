<?php

namespace Wexample\SymfonyMailDemo\Controller\Pages;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use Wexample\SymfonyLoader\Controller\AbstractPagesController;
use Wexample\SymfonyMailDemo\Enum\DemoMailSample;
use Wexample\SymfonyMailDemo\Service\DemoMailSenderService;
use Wexample\SymfonyMailDemo\Traits\SymfonyMailDemoBundleClassTrait;
use Wexample\SymfonyMailDs\Controller\Pages\MailboxController;

/**
 * Sends sample mails into the development mailbox, then opens it. Routed
 * where the mailbox is, in dev and test only.
 */
#[Route(path: '/mail/', name: 'mail_demo_', env: ['dev', 'test'])]
final class MailController extends AbstractPagesController
{
    use SymfonyMailDemoBundleClassTrait;

    final public const string ROUTE_INDEX = VariableHelper::INDEX;

    final public const string ROUTE_SEND = 'send';

    public const string CSRF_SEND = 'mail_demo_send';

    #[Route(path: '', name: self::ROUTE_INDEX)]
    public function index(): Response
    {
        return $this->renderPage(self::ROUTE_INDEX, [
            'samples' => DemoMailSample::cases(),
            'recipient' => DemoMailSenderService::RECIPIENT,
        ]);
    }

    #[Route(path: 'send/{sample}', name: self::ROUTE_SEND, methods: [Request::METHOD_POST])]
    public function send(
        Request $request,
        DemoMailSample $sample,
        DemoMailSenderService $sender
    ): RedirectResponse {
        if (! $this->isCsrfTokenValid(self::CSRF_SEND, $request->request->getString('_token'))) {
            return $this->redirectToRoute('mail_demo_'.self::ROUTE_INDEX);
        }

        $sender->send($sample);

        return $this->redirectToRoute(MailboxController::ROUTE_INDEX);
    }
}
