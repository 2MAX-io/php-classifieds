<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class UserMessageThread
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdByUser;

    /**
     * @var Listing|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing")
     * @ORM\JoinColumn(nullable=true)
     */
    private $listing;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdDatetime;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $latestMessageDatetime;

    /**
     * @return User[]
     */
    public function getAllUsersArray(): array
    {
        return [$this->listing->getUserNotNull(), $this->getCreatedByUser()];
    }

    public function getOtherUser(User $user): User
    {
        /** @var User[] $userList */
        $userList = [$this->listing->getUser(), $this->getCreatedByUser()];
        foreach ($userList as $otherUser) {
            if ($otherUser->getId() !== $user->getId()) {
                return $otherUser;
            }
        }

        throw new \RuntimeException('other user not found in thread');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedByUser(): User
    {
        return $this->createdByUser;
    }

    public function setCreatedByUser(User $createdByUser): void
    {
        $this->createdByUser = $createdByUser;
    }

    public function getListing(): ?Listing
    {
        return $this->listing;
    }

    public function getListingNotNull(): Listing
    {
        if (null === $this->listing) {
            throw new \RuntimeException('listing is null');
        }

        return $this->listing;
    }

    public function setListing(?Listing $listing): void
    {
        $this->listing = $listing;
    }

    public function getCreatedDatetime(): DateTimeInterface
    {
        return $this->createdDatetime;
    }

    public function setCreatedDatetime(DateTimeInterface $createdDatetime): void
    {
        $this->createdDatetime = $createdDatetime;
    }

    public function getLatestMessageDatetime(): DateTimeInterface
    {
        return $this->latestMessageDatetime;
    }

    public function setLatestMessageDatetime(DateTimeInterface $latestMessageDatetime): void
    {
        $this->latestMessageDatetime = $latestMessageDatetime;
    }
}
