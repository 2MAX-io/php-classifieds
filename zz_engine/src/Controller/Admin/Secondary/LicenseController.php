<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Helper\Json;
use App\Service\System\License\LicenseService;
use App\Service\System\Signature\SignatureVerifyNormalSecurity;
use App\Service\System\Upgrade\Base\UpgradeApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LicenseController extends AbstractController
{
    /**
     * @Route("/private/is9WCVzaXS2HUDjAzjWa4/license/show", name="app_license_show")
     */
    public function licenseShow(Request $request, UrlGeneratorInterface $urlGenerator, LicenseService $licenseService): Response
    {
        $payload = $request->getContent();
        if (!SignatureVerifyNormalSecurity::authenticate(
            $payload,
            $request->headers->get(UpgradeApi::HEADER_SIGNATURE_NORMAL_SECURITY, '')
        )) {
            return $this->json([
                'error' => 'invalid signature',
            ], Response::HTTP_FORBIDDEN);
        }

        $payloadArray = Json::decodeToArray($payload);
        $licenseShowAbsoluteUrl = $urlGenerator->generate('app_license_show', [], UrlGeneratorInterface::ABSOLUTE_URL);
        if ($payloadArray['licenseShowAbsoluteUrl'] ?? '' !== $licenseShowAbsoluteUrl) {
            return $this->json([
                'error' => 'payload for invalid licenseShowAbsoluteUrl',
                'licenseShowAbsoluteUrl' => $licenseShowAbsoluteUrl,
            ], Response::HTTP_FORBIDDEN);
        }

        return $this->json([
            'license' => $licenseService->getLicenseText(),
            'url' => $licenseService->getCurrentUrlOfLicense(),
        ]);
    }
}
