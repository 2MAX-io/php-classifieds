<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Listing;

use App\Entity\Listing;
use App\Form\ValidityExtendType;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ValidityExtendController extends AbstractController
{
    /**
     * @Route("/user/validity-extend/{id}", name="app_user_validity_extend")
     */
    public function validityExtend(Request $request, Listing $listing, ValidUntilSetService $validUntilSetService): Response
    {
        $form = $this->createForm(ValidityExtendType::class, []);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $validUntilSetService->setValidUntil($listing, (int) $form->get('validityTimeDays')->getData());
            $listing->setUserDeactivated(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($listing);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_validity_extend', ['id' => $listing->getId()]);
        }

        return $this->render('user/listing/validity_extend.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
        ]);
    }
}
