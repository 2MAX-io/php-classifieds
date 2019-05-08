<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use Doctrine\ORM\EntityManagerInterface;

class CustomFieldService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
}
