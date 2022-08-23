<?php

namespace Adeliom\EasyAdminUserBundle\Entity;

use Adeliom\EasyAdminUserBundle\Repository\ResetPasswordRequestRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

#[ORM\HasLifecycleCallbacks]
#[ORM\MappedSuperclass(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    public function __construct(protected ?User $user, DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->initialize($expiresAt, $selector, $hashedToken);
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUser(): object
    {
        return $this->user;
    }
}
