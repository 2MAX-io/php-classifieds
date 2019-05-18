<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker;

use App\Entity\Page;
use App\Service\Setting\SettingsService;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InvalidLinkToPagesHealthChecker implements HealthCheckerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(EntityManagerInterface $em, SettingsService $settingsService, TranslatorInterface $trans)
    {
        $this->em = $em;
        $this->trans = $trans;
        $this->settingsService = $settingsService;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        if (!$this->hasAllRequired()) {
            return new HealthCheckResultDto(true, $this->trans->trans('trans.Some links to pages in settings are not set or disabled'));
        }

        return new HealthCheckResultDto(false);
    }

    private function hasAllRequired(): bool
    {
        $requiredPages = $this->getRequiredPages();

        $qb = $this->em->getRepository(Page::class)->createQueryBuilder('page');
        $qb->select('COUNT(1)');
        $qb->andWhere($qb->expr()->in('page.slug', ':slugList'));
        $qb->setParameter('slugList', $requiredPages, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
        $qb->andWhere($qb->expr()->eq('page.enabled', 1));
        $foundPagesCount = (int) $qb->getQuery()->getSingleScalarResult();

        return \count($requiredPages) === $foundPagesCount;
    }

    private function getRequiredPages(): array
    {
        $settingsDto = $this->settingsService->getSettingsDto();

        return [
            $settingsDto->getLinkTermsConditions(),
            $settingsDto->getLinkPrivacyPolicy(),
            $settingsDto->getLinkRejectionReason(),
        ];
    }
}
