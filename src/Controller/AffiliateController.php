<?php

namespace App\Controller;

use App\Entity\Affiliate;
use App\Form\AffiliateType;
use App\Message\AffiliateMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\MessageHandler\AffiliateMessageHandler;

class AffiliateController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $bus,
        private AffiliateMessageHandler $affiliateMessageHandler
     ) {
     }
    #[Route('/affiliate/create', name: 'app_affiliate')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $affiliate = new Affiliate();
        $owner = $this->getUser();
        $affiliate->setOwner($owner);
        $form = $this->createForm(AffiliateType::class, $affiliate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($affiliate);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            $context = [
                'url' => $affiliate->getUrl(),
                'email' => $affiliate->getEmail(),
                'categories' => $affiliate->getCategories(),
                'state' => $affiliate->getState(),
                'owner' => $affiliate->getOwner(),
            ];
            $this->bus->dispatch(new AffiliateMessage($affiliate->getId(), $context));
        }

        return $this->render('affiliate/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
