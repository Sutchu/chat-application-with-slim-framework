<?php

declare(strict_types=1);

namespace sutchu\chatserver\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\EntityManager;

use JsonSerializable;


#[Entity, Table(name: 'messages')]
class Message implements JsonSerializable
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ManyToOne(targetEntity: Chat::class)]
    #[JoinColumn(name: 'chat_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Chat $chat;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'sender_id', referencedColumnName: 'id', nullable: false)]
    private User $sender;

    #[Column(type: 'text', nullable: false)]
    private string $content;

    #[Column(type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $created_at;

    #[Column(type: 'boolean', nullable: false)]
    private bool $is_seen;

    #[ManyToOne(targetEntity: self::class)]
    #[JoinColumn(name: 'reply_to_id', referencedColumnName: 'id', nullable: true)]
    private ?Message $reply_to;

    public function __construct(
        Chat $chat,
        User $sender,
        string $content,
        bool $is_seen = false,
        ?Message $reply_to = null
    ) {
        $this->chat = $chat;
        $this->sender = $sender;
        $this->content = $content;
        $this->created_at = new DateTimeImmutable("now");
        $this->is_seen = $is_seen;
        $this->reply_to = $reply_to;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getChat(): Chat
    {
        return $this->chat;
    }

    public function getSender(): User
    {
        return $this->sender;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    public function isSeen(): bool
    {
        return $this->is_seen;
    }

    public function getReplyTo(): ?Message
    {
        return $this->reply_to;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'chat' => $this->chat,
            'sender' => $this->sender,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'is_seen' => $this->is_seen,
            'reply_to' => $this->reply_to,
        ];
    }
}
