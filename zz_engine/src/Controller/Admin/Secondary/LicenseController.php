<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Enum\ParamEnum;
use App\Exception\UserVisibleException;
use App\Helper\JsonHelper;
use App\Service\System\License\LicenseService;
use App\Service\System\Signature\VerifySignature;
use App\Service\System\Upgrade\Base\UpgradeApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LicenseController extends AbstractController
{
    /**
     * /zzzz/secret-is9WCVzaXS2HUDjAzjWa4cFtFiNSSbLDYki6WmTiwh/license/show-license
     *
     * @Route("/zzzz/{urlSecret}/license/show-license", name="app_license_show")
     * @noinspection SpellCheckingInspection
     */
    public function licenseShow(
        string $urlSecret,
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        LicenseService $licenseService
    ): Response {
        if (!isset($_ENV['APP_2MAX_URL_SECRET'])) {
            throw new UserVisibleException('ENV APP_2MAX_URL_SECRET not found');
        }
        if ($urlSecret !== $_ENV['APP_2MAX_URL_SECRET']) {
            throw new UserVisibleException('urlSecret not correct');
        }

        $requestContent = $request->getContent();
        if (!VerifySignature::authenticate(
            $requestContent,
            $request->headers->get(UpgradeApi::HEADER_SIGNATURE, '')
        )) {
            return $this->json([
                ParamEnum::ERROR => 'invalid signature',
            ], Response::HTTP_FORBIDDEN);
        }

        $requestArray = JsonHelper::toArray($requestContent);
        $appGeneratedUrl = $urlGenerator->generate(
            'app_license_show',
            [
                'urlSecret' => $urlSecret,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
        $urlFromRequest = $requestArray['licenseShowAbsoluteUrl'] ?? '';
        $signedUrlMatchActualUrl = $urlFromRequest !== $appGeneratedUrl;
        if (!$signedUrlMatchActualUrl) {
            return $this->json([
                ParamEnum::ERROR => 'payload for invalid licenseShowAbsoluteUrl',
                'licenseShowAbsoluteUrl' => $appGeneratedUrl,
                'urlFromRequest' => $urlFromRequest,
            ], Response::HTTP_FORBIDDEN);
        }

        return $this->json([
            'license' => $licenseService->getLicenseText(),
            'urlOfLicense' => $licenseService->getCurrentUrlOfLicense(),
            'licenseShowAbsoluteUrl' => $appGeneratedUrl,
            'urlFromRequest' => $urlFromRequest,
        ]);
    }
}
