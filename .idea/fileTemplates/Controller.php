<?php

declare(strict_types=1);

namespace ${NAMESPACE};

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ${NAME} extends AbstractController
{
    /**
     * @Route("/", name="app_")
     */
    public function action(): Response {

        return ${DS}this->render('index.html.twig', [
        ]);
    }
}
