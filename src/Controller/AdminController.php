<?php
namespace App\Controller;

use App\Entity\Affiliate;
use App\Message\AffiliateMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use Twig\Environment;

class AdminController extends AbstractController
{
    public function __construct(
        private Environment $twig,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $bus,
    ) {
    }

    #[Route('/admin/affiliate/review/{id}', name: 'review_affiliate')]
    public function reviewAffiliate(Request $request, Affiliate $affiliate, WorkflowInterface $affiliateStateMachine): Response
    {
        $accepted = !$request->query->getBoolean('reject', false);

        if ($affiliateStateMachine->can($affiliate, 'accept')) {
            $transition = $accepted ? 'accept' : 'reject';
        } else {
            return new Response('Comment already reviewed or not in the right state.');
        }
        $affiliateStateMachine->apply($affiliate, $transition);
        $this->entityManager->flush();

        if ($accepted) {
            $user = $affiliate->getOwner();

            if ($user) {
                $roles = $user->getRoles();
                if (!in_array('ROLE_AFFILIATE', $roles)) {
                    $roles[] = 'ROLE_AFFILIATE';
                    $user->setRoles($roles);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }
            }
            $this->bus->dispatch(new AffiliateMessage($affiliate->getId()));
        }

        return new Response($this->twig->render('admin/review.html.twig', [
            'transition' => $transition,
            'affiliate' => $affiliate,
        ]));
    }
}