<?php

declare(strict_types=1);

namespace sutchu\chatserver\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;
use DateTimeImmutable;
use DateTimeInterface;


#[Entity, Table(name: 'auth_tokens')]
class AuthToken implements JsonSerializable
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[Column(type: 'string')]
    private string $token;

    #[Column(name: 'expires_at', type: 'datetime')]
    private DateTimeInterface $expires_at;

    public function __construct(User $user, int $tokenLength = 32, int $expiresInDays = 7)
    {
        $this->user = $user;
        $this->token = bin2hex(random_bytes($tokenLength));
        $this->expires_at = (new DateTimeImmutable("now"))->modify("+$expiresInDays days");
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expires_at;
    }

    public function isValid(): bool
    {
        return $this->expires_at > new DateTimeImmutable("now");
    }

    public function jsonSerialize(): array
    {
        return [
            'token' => $this->getToken(),
            'expires_at' => $this->getExpiresAt()->format(DateTimeInterface::ATOM)
        ];
    }
}
