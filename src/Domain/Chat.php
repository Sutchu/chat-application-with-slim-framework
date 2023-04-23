<?php

declare(strict_types=1);

namespace sutchu\chatserver\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\EntityManager;

use JsonSerializable;


#[Entity, Table(name: 'chats', uniqueConstraints: [new UniqueConstraint(name: 'unique_users_chat', columns: ['user1_id', 'user2_id'])])]
class Chat implements JsonSerializable
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user1_id', referencedColumnName: 'id', nullable: false)]
    private User $user1;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user2_id', referencedColumnName: 'id', nullable: false)]
    private User $user2;

    private function __construct(User $user1, User $user2)
    {
        $this->user1 = $user1;
        $this->user2 = $user2;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser1(): User
    {
        return $this->user1;
    }

    public function getUser2(): User
    {
        return $this->user2;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'user1' => $this->user1,
            'user2' => $this->user2,
        ];
    }

    public static function getOrCreateChat(EntityManager $em, User $user1, User $user2): ?Chat
    {
        // Ensure user1 has the lower id
        if ($user1->getId() > $user2->getId()) {
            [$user1, $user2] = [$user2, $user1];
        }

        $chat = $em->getRepository(Chat::class)
            ->findOneBy([
                'user1' => $user1,
                'user2' => $user2,
            ]);

        // If no chat was found, create a new one
        if ($chat === null) {
            $chat = new Chat($user1, $user2);
            $em->persist($chat);
            $em->flush();
        }

        return $chat;
    }
}
