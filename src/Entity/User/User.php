<?php

namespace App\Entity\User;

use App\Entity\Contact\Contact;
use App\Entity\LoginAttempt\LoginAttempt;
use App\Entity\Workspace\WorkspaceUser;
use App\Repository\User\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(type:'json',nullable: true)]
    private ?array $roles = ['ROLE_USER'];

    #[ORM\Column(length: 128)]
    private ?string $password = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Contact>
     */
    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'owner')]
    private Collection $contacts;

    /**
     * @var Collection<int, WorkspaceUser>
     */
    #[ORM\OneToMany(targetEntity: WorkspaceUser::class, mappedBy: 'user')]
    private Collection $workspaceMemberships;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deleted_at = null;

    #[ORM\Column(type: 'datetime_immutable',nullable: true)]
    private ?\DateTimeImmutable $anonymized_at = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $is_active = true;

    /**
     * @var Collection<int, LoginAttempt>
     */
    #[ORM\OneToMany(targetEntity: LoginAttempt::class, mappedBy: 'user')]
    private Collection $login_attempts;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->workspaceMemberships = new ArrayCollection();
        $this->login_attempts = new ArrayCollection();
    }

    public function getFullName() : ?string 
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));    
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(?array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }
    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
       return (string) $this->email;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setOwner($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getOwner() === $this) {
                $contact->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkspaceUser>
     */
    public function getWorkspaceMemberships(): Collection
    {
        return $this->workspaceMemberships;
    }

    public function addWorkspaceMembership(WorkspaceUser $workspaceMembership): static
    {
        if (!$this->workspaceMemberships->contains($workspaceMembership)) {
            $this->workspaceMemberships->add($workspaceMembership);
            $workspaceMembership->setUser($this);
        }

        return $this;
    }

    public function removeWorkspaceMembership(WorkspaceUser $workspaceMembership): static
    {
        if ($this->workspaceMemberships->removeElement($workspaceMembership)) {
            // set the owning side to null (unless already changed)
            if ($workspaceMembership->getUser() === $this) {
                $workspaceMembership->setUser(null);
            }
        }

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function softDelete() : void {
        $this->deleted_at = new \DateTimeImmutable();
    }

    public function restore() : void {
        $this->deleted_at = null;
    }

    public function isDeleted() : bool {
        return $this->deleted_at !== null;
    }

    public function getAnonymizedAt(): ?\DateTimeImmutable
    {
        return $this->anonymized_at;
    }

    public function setAnonymizedAt(?\DateTimeImmutable $anonymized_at): static
    {
        $this->anonymized_at = $anonymized_at;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function anonymize() : void {
        if($this->anonymized_at !== null) return;

        $this->email = sprintf('anon_%s@deleted.local', $this->getId());
        $this->first_name = 'Anonim-' . bin2hex(random_bytes(4));
        $this->last_name = 'Anonim-' . bin2hex(random_bytes(4));
        $this->is_active = false;
        $this->anonymized_at = new \DateTimeImmutable();
    }

    /**
     * @return Collection<int, LoginAttempt>
     */
    public function getLoginAttempts(): Collection
    {
        return $this->login_attempts;
    }

    public function addLoginAttempt(LoginAttempt $loginAttempt): static
    {
        if (!$this->login_attempts->contains($loginAttempt)) {
            $this->login_attempts->add($loginAttempt);
            $loginAttempt->setUser($this);
        }

        return $this;
    }

    public function removeLoginAttempt(LoginAttempt $loginAttempt): static
    {
        if ($this->login_attempts->removeElement($loginAttempt)) {
            // set the owning side to null (unless already changed)
            if ($loginAttempt->getUser() === $this) {
                $loginAttempt->setUser(null);
            }
        }

        return $this;
    }
}
