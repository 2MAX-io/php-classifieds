<?php

declare(strict_types=1);

namespace App\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="zzzz_executed_upgrade")
 */
class ExecutedUpgrade
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $upgradeId;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $executedDatetime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getExecutedDatetime(): ?\DateTimeInterface
    {
        return $this->executedDatetime;
    }

    public function setExecutedDatetime(\DateTimeInterface $executedDatetime): self
    {
        $this->executedDatetime = $executedDatetime;

        return $this;
    }

    public function getUpgradeId(): ?int
    {
        return $this->upgradeId;
    }

    public function setUpgradeId(int $upgradeId): self
    {
        $this->upgradeId = $upgradeId;

        return $this;
    }
}
