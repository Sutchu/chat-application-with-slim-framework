<?php

declare(strict_types=1);

namespace sutchu\chatserver\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use JsonSerializable;
use Doctrine\ORM\Mapping\Table;

use function password_hash;
use function password_verify;
use InvalidArgumentException;

#[Entity, Table(name: 'users')]
final class User implements JsonSerializable
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $email;

    #[Column(name: 'registered_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $registered_at;

    #[Column(type: 'string', unique:true, nullable: false, length: 16)]
    private string $username;

    #[Column(type: 'string', nullable: false, length: 60)]
    private string $password_hash;

    public function __construct(string $email, string $username, string $password)
    {
        $this->email = $email;
        $this->registered_at = new DateTimeImmutable('now');
        $this->username = $username;
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getregisteredAt(): DateTimeImmutable
    {
        return $this->registered_at;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    public function changePassword(string $new_password): void
    {
        if (empty($new_password) || strlen($new_password) < 6) {
            throw new InvalidArgumentException('The password must be at least 6 characters long');
        }

        $this->password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'username' => $this->getUsername(),
            'email' => $this->getEmail()
        ];
    }
}