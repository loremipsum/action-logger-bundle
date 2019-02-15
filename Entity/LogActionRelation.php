<?php

namespace LoremIpsum\ActionLoggerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="log_relations", indexes={@ORM\Index(name="logActionRelations_keyHash_idx", columns={"key_hash"})})
 * @ORM\Entity(repositoryClass="LoremIpsum\ActionLoggerBundle\Repository\LogActionRelationRepository")
 */
class LogActionRelation
{
    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var LogAction
     * @ORM\ManyToOne(targetEntity="LogAction", inversedBy="relations")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $log;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $keyId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $keyEntity;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    private $keyHash;

    public function __construct(LogAction $log, $keyId, $keyEntity)
    {
        $this->log       = $log;
        $this->keyId     = $keyId;
        $this->keyEntity = $keyEntity;
        $this->keyHash   = self::hash($keyId, $keyEntity);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return LogAction
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @return int
     */
    public function getKeyId()
    {
        return $this->keyId;
    }

    /**
     * @return string
     */
    public function getKeyEntity()
    {
        return $this->keyEntity;
    }

    /**
     * @return string
     */
    public function setKeyHash()
    {
        return $this->keyHash;
    }

    /**
     * @param int|string $id
     * @param string     $entity
     * @return string
     */
    public static function hash($id, $entity)
    {
        return hash("sha256", $entity . ':' . $id);
    }
}
