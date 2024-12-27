<?php
namespace App\MessageHandler;

use App\Message\AffiliateMessage;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\AffiliateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class AffiliateMessageHandler
{
    private $entityManager;
    private $affiliateRepository;
    private $bus;
    private $workflow;
    private $mailer;
    private $notifier;
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        AffiliateRepository $affiliateRepository,
        MessageBusInterface $bus,
        WorkflowInterface $affiliateStateMachine,
        MailerInterface $mailer,
        #[Autowire('%env(ADMIN_EMAIL)%')] private string $adminEmail,
        NotifierInterface $notifier,
        LoggerInterface $logger = null
    ) {
        $this->entityManager = $entityManager;
        $this->affiliateRepository = $affiliateRepository;
        $this->bus = $bus;
        $this->workflow = $affiliateStateMachine;
        $this->mailer = $mailer;
        $this->notifier = $notifier;
        $this->logger = $logger;
    }

    public function __invoke(AffiliateMessage $message)
    {
        $affiliate = $this->affiliateRepository->find($message->getId());
        $this->logger->info('Processing AffiliateMessage', ['message' => $message]);
        $this->logger->info('Current state of affiliate', [
            'affiliate_id' => $affiliate->getId(),
            'state' => $affiliate->getState(),
        ]);
        if ($this->workflow->can($affiliate, 'send_to_admin')) {
            $email = (new NotificationEmail())
                ->subject('New affiliate wants to join!')
                ->htmlTemplate('email/affiliate_notification.html.twig')
                ->from($this->adminEmail)
                ->to($this->adminEmail)
                ->context(['affiliate' => $affiliate]);

            try {
                $this->mailer->send($email);
            } catch (\Exception $e) {
                $this->logger->error('Error sending  email', ['error' => $e->getMessage()]);
            }

            $this->workflow->apply($affiliate, 'send_to_admin');
            $this->entityManager->flush();
        }
        elseif (!$affiliate) {
            $this->logger?->error('Affiliate not found', ['id' => $message->getId()]);
            return;
        }
        elseif ($this->logger) {
            $this->logger->debug('Dropping comment message', ['comment' => $affiliate->getId(), 'state' => $affiliate->getState(), 'owner_id' => $affiliate->getOwner()]);
        }
    }
 }
