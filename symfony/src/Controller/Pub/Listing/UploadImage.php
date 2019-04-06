<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadImage extends AbstractController
{
    /**
     * @Route("/listing/upload-image", name="app_upload_image")
     */
    public function getCustomFields(): Response
    {
        return new Response('{"initialPreview":"/easyapp/uploads/images/listings/2000x2000/acafe03.png","initialPreviewConfig":[{"caption":"/easyapp/uploads/images/listings/2000x2000/acafe03.png","key":72,"url":"/easyapp/listing/remove-ad-image"}]}');
    }
}


