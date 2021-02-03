<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExecutedUpgradeRepository")
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
    private $executedAt;

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

    public function getExecutedAt(): ?\DateTimeInterface
    {
        return $this->executedAt;
    }

    public function setExecutedAt(\DateTimeInterface $executedAt): self
    {
        $this->executedAt = $executedAt;

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
