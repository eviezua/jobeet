<?php

namespace App\Controller;

use App\Entity\Affiliate;
use App\Form\AffiliateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AffiliateController extends AbstractController
{
    #[Route('/affiliate', name: 'app_affiliate')]
    public function create(Request $request): Response
    {
        $affiliate = new Affiliate();
        $form = $this->createForm(AffiliateType::class, $affiliate);

        return $this->render('affiliate/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
