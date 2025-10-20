<?php

namespace LoremIpsum\ActionLoggerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'log_action_relation', indexes: [new ORM\Index(name: 'logActionRelation_keyHash_idx', columns: ['key_hash'])])]
#[ORM\Entity(repositoryClass: 'LoremIpsum\ActionLoggerBundle\Repository\LogActionRelationRepository')]
class LogActionRelation
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: LogAction::class, inversedBy: 'relations')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private LogAction $log;

    #[ORM\Column(type: 'integer')]
    private int $keyId = 0;

    #[ORM\Column(type: 'string')]
    private string $keyEntity = "";

    #[ORM\Column(type: 'string', length: 64)]
    private string $keyHash = "";

    public function __construct(LogAction $log, $keyId, $keyEntity)
    {
        $this->log       = $log;
        $this->keyId     = $keyId;
        $this->keyEntity = $keyEntity;
        $this->keyHash   = self::hash($keyId, $keyEntity);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLog(): LogAction
    {
        return $this->log;
    }

    public function getKeyId(): int
    {
        return $this->keyId;
    }

    public function getKeyEntity(): string
    {
        return $this->keyEntity;
    }

    public function getKeyHash(): string
    {
        return $this->keyHash;
    }

    public static function hash(int|string $id, string $entity): string
    {
        return hash("sha256", $entity . ':' . $id);
    }
}
