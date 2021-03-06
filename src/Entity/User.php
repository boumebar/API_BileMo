<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_users_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *     "create",
 *     href =  @Hateoas\Route(
 *          "api_users_add",
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href =  @Hateoas\Route(
 *          "api_users_delete",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * 
 * 
 * @Serializer\ExclusionPolicy("all")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="you must enter a lastname")
     * @Serializer\Expose
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="you must enter a firstname")
     * @Serializer\Expose
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="you must enter an email")
     * @Assert\Email(message="Your email is not a valid email address")
     * @Serializer\Expose
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Expose
     */
    private $customer;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
