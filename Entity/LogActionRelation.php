<?php

namespace LoremIpsum\ActionLoggerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="log_relations")
 * @ORM\Entity(repositoryClass="LoremIpsum\ActionLoggerBundle\Repository\LogActionRelationRepository")
 */
class LogActionRelation
{
    /**
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
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $keyId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $keyEntity;

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
     * @param LogAction $log
     */
    public function setLog(LogAction $log)
    {
        $this->log = $log;
    }

    /**
     * @return int
     */
    public function getKeyId()
    {
        return $this->keyId;
    }

    /**
     * @param int $keyId
     */
    public function setKeyId($keyId)
    {
        $this->keyId = $keyId;
    }

    /**
     * @return string
     */
    public function getKeyEntity()
    {
        return $this->keyEntity;
    }

    /**
     * @param string $keyEntity
     */
    public function setKeyEntity($keyEntity)
    {
        $this->keyEntity = $keyEntity;
    }
}
