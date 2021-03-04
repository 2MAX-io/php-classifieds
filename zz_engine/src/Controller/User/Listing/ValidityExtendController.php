<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Form\ValidityExtendType;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ValidityExtendController extends AbstractUserController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/user/validity-extend/{id}", name="app_user_validity_extend")
     */
    public function validityExtend(
        Request $request,
        Listing $listing,
        ValidUntilSetService $validUntilSetService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        $form = $this->createForm(ValidityExtendType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $validUntilSetService->setValidityDaysFromNow(
                $listing,
                (int) $form->get('validityTimeDays')->getData(),
            );
            $validUntilSetService->onValidityExtendedByUser($listing);

            $this->em->persist($listing);
            $this->em->flush();

            return $this->redirectToRoute('app_user_validity_extend', ['id' => $listing->getId()]);
        }

        return $this->render('user/listing/validity_extend.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
        ]);
    }
}
