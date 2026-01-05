<?php

namespace App\Entity\Workspace;

use App\Entity\Contact\Contact;
use App\Repository\Workspace\WorkspaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkspaceRepository::class)]
class Workspace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Contact>
     */
    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'workspace', orphanRemoval: true)]
    private Collection $contacts;

    /**
     * @var Collection<int, WorkspaceUser>
     */
    #[ORM\OneToMany(targetEntity: WorkspaceUser::class, mappedBy: 'workspace')]
    private Collection $memberships;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->memberships = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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
            $contact->setWorkspace($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getWorkspace() === $this) {
                $contact->setWorkspace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkspaceUser>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(WorkspaceUser $membership): static
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
            $membership->setWorkspace($this);
        }

        return $this;
    }

    public function removeMembership(WorkspaceUser $membership): static
    {
        if ($this->memberships->removeElement($membership)) {
            // set the owning side to null (unless already changed)
            if ($membership->getWorkspace() === $this) {
                $membership->setWorkspace(null);
            }
        }

        return $this;
    }
}
