<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker;

use App\Entity\Page;
use App\Service\Setting\SettingsService;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InvalidLinkToPagesHealthCheckerService implements HealthCheckerInterface
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        SettingsService $settingsService,
        TranslatorInterface $trans,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->trans = $trans;
        $this->settingsService = $settingsService;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        if (!$this->hasAllRequiredLinksToPages()) {
            return new HealthCheckResultDto(
                true,
                $this->trans->trans('trans.Some links to pages in settings are not set or disabled')
            );
        }

        return new HealthCheckResultDto(false);
    }

    private function hasAllRequiredLinksToPages(): bool
    {
        $requiredPages = $this->getSlugOfRequiredPages();

        $qb = $this->em->createQueryBuilder();
        $qb->select('page');
        $qb->from(Page::class, 'page');
        $qb->select('COUNT(1)');
        $qb->andWhere($qb->expr()->in('page.slug', ':slugList'));
        $qb->setParameter('slugList', $requiredPages, Connection::PARAM_STR_ARRAY);
        $qb->andWhere($qb->expr()->eq('page.enabled', 1));
        $foundPagesCount = (int) $qb->getQuery()->getSingleScalarResult();

        return \count($requiredPages) === $foundPagesCount;
    }

    /**
     * @return array<array-key, string|null>
     */
    private function getSlugOfRequiredPages(): array
    {
        $settingsDto = $this->settingsService->getSettingsDto();

        return [
            $settingsDto->getLinkTermsConditions() ?? 'LinkTermsConditions',
            $settingsDto->getLinkPrivacyPolicy() ?? 'LinkPrivacyPolicy',
            $settingsDto->getLinkRejectionReason() ?? 'LinkRejectionReason',
            $settingsDto->getLinkAdvertisement() ?? 'LinkAdvertisement',
            $settingsDto->getLinkContact() ?? 'LinkContact',
        ];
    }
}
