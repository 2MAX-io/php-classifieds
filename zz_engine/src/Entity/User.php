<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\UserRoleEnum;
use App\Helper\StringHelper;
use App\Security\Base\EnablableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"})
 * @UniqueEntity(fields={"username"})
 */
class User implements UserInterface, UserRoleEnum, EnablableInterface, EncoderAwareInterface
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=70, unique=true, nullable=false)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=70, unique=true, nullable=false)
     */
    protected $email;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $displayUsername;

    /**
     * @var string[]
     *
     * @ORM\Column(type="json", nullable=false)
     */
    protected $roles = [];

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $enabled;

    /**
     * @var string The hashed password
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $password;

    /**
     * @var string|null
     */
    private $plainPassword;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $registrationDate;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var Collection|Listing[]
     *
     * @Assert\Type(groups={"skipAutomaticValidation"}, type="")
     * @ORM\OneToMany(targetEntity="App\Entity\Listing", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $listings;

    /**
     * @var Collection|UserBalanceChange[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserBalanceChange", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $userBalanceChanges;

    /**
     * @var Collection|PaymentForBalanceTopUp[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentForBalanceTopUp", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $paymentForBalanceTopUpList;

    /**
     * @var Collection|Payment[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $payments;

    /**
     * @var Collection|UserInvoiceDetails[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserInvoiceDetails", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $userInvoiceDetails;

    /**
     * @var Collection|Invoice[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Invoice", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $invoices;

    public function __construct()
    {
        $this->listings = new ArrayCollection();
        $this->userBalanceChanges = new ArrayCollection();
        $this->paymentForBalanceTopUpList = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->userInvoiceDetails = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    /**
     * prevents big session, when serializing user security token.
     *
     * @return array|mixed
     */
    public function __sleep()
    {
        return [
            'id',
            'username',
            'email',
            'roles',
            'enabled',
            'password',
        ];
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    /**
     * Gets the name of the encoder used to encode the password.
     *
     * If the method returns null, the standard way to retrieve the encoder
     * will be used instead.
     *
     * @return string
     */
    public function getEncoderName(): ?string
    {
        if (StringHelper::beginsWith($this->getPassword() ?? '', '$2a$')) {
            return 'legacy_phpass'; // security.yaml
        }

        return null; // use the default encoder
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        if ($this->username && \str_contains($this->getUsername(), '@')) {
            $this->setUsername($email);
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = UserRoleEnum::ROLE_USER;

        return \array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Listing[]
     */
    public function getListings(): Collection
    {
        return $this->listings;
    }

    public function addListing(Listing $listing): self
    {
        if (!$this->listings->contains($listing)) {
            $this->listings[] = $listing;
            $listing->setUser($this);
        }

        return $this;
    }

    public function removeListing(Listing $listing): self
    {
        if ($this->listings->contains($listing)) {
            $this->listings->removeElement($listing);
            // set the owning side to null (unless already changed)
            if ($listing->getUser() === $this) {
                $listing->setUser(null);
            }
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection|UserBalanceChange[]
     */
    public function getUserBalanceChanges(): Collection
    {
        return $this->userBalanceChanges;
    }

    public function addUserBalanceChange(UserBalanceChange $userBalanceChange): self
    {
        if (!$this->userBalanceChanges->contains($userBalanceChange)) {
            $this->userBalanceChanges[] = $userBalanceChange;
            $userBalanceChange->setUser($this);
        }

        return $this;
    }

    public function removeUserBalanceChange(UserBalanceChange $userBalanceChange): self
    {
        if ($this->userBalanceChanges->contains($userBalanceChange)) {
            $this->userBalanceChanges->removeElement($userBalanceChange);
            // set the owning side to null (unless already changed)
            if ($userBalanceChange->getUser() === $this) {
                $userBalanceChange->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PaymentForBalanceTopUp[]
     */
    public function getPaymentForBalanceTopUpList(): Collection
    {
        return $this->paymentForBalanceTopUpList;
    }

    public function addPaymentForBalanceTopUpList(PaymentForBalanceTopUp $paymentForBalanceTopUpList): self
    {
        if (!$this->paymentForBalanceTopUpList->contains($paymentForBalanceTopUpList)) {
            $this->paymentForBalanceTopUpList[] = $paymentForBalanceTopUpList;
            $paymentForBalanceTopUpList->setUser($this);
        }

        return $this;
    }

    public function removePaymentForBalanceTopUpList(PaymentForBalanceTopUp $paymentForBalanceTopUpList): self
    {
        if ($this->paymentForBalanceTopUpList->contains($paymentForBalanceTopUpList)) {
            $this->paymentForBalanceTopUpList->removeElement($paymentForBalanceTopUpList);
            // set the owning side to null (unless already changed)
            if ($paymentForBalanceTopUpList->getUser() === $this) {
                $paymentForBalanceTopUpList->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }

        return $this;
    }

    public function getUserInvoiceDetails(): ?UserInvoiceDetails
    {
        return $this->userInvoiceDetails->last() ?: null;
    }

    public function setUserInvoiceDetails(UserInvoiceDetails $userInvoiceDetails): void
    {
        $this->userInvoiceDetails[] = $userInvoiceDetails;
    }

    public function addUserInvoiceDetail(UserInvoiceDetails $userInvoiceDetail): self
    {
        if (!$this->userInvoiceDetails->contains($userInvoiceDetail)) {
            $this->userInvoiceDetails[] = $userInvoiceDetail;
            $userInvoiceDetail->setUser($this);
        }

        return $this;
    }

    public function removeUserInvoiceDetail(UserInvoiceDetails $userInvoiceDetail): self
    {
        if ($this->userInvoiceDetails->contains($userInvoiceDetail)) {
            $this->userInvoiceDetails->removeElement($userInvoiceDetail);
            // set the owning side to null (unless already changed)
            if ($userInvoiceDetail->getUser() === $this) {
                $userInvoiceDetail->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setUser($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->contains($invoice)) {
            $this->invoices->removeElement($invoice);
            // set the owning side to null (unless already changed)
            if ($invoice->getUser() === $this) {
                $invoice->setUser(null);
            }
        }

        return $this;
    }

    public function getDisplayUsername(): ?string
    {
        return $this->displayUsername;
    }

    public function setDisplayUsername(?string $displayUsername): void
    {
        $this->displayUsername = $displayUsername;
    }
}
