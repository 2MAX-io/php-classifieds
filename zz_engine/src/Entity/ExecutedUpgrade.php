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
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $upgradeId;

    /**
     * @ORM\Column(type="datetime")
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
