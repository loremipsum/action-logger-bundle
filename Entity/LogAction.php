<?php

namespace LoremIpsum\ActionLoggerBundle\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LoremIpsum\ActionLoggerBundle\Action;
use LoremIpsum\ActionLoggerBundle\ActionFactory;

/**
 * @ORM\Table(name="log")
 * @ORM\Entity(repositoryClass="LoremIpsum\ActionLoggerBundle\Repository\LogActionRepository")
 */
class LogAction
{
    const LEVEL_ERROR = 400;
    const LEVEL_WARNING = 300;
    const LEVEL_NOTICE = 250;
    const LEVEL_INFO = 200;
    const LEVEL_DEBUG = 100;

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @var \DateTime
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var string
     * @ORM\Column(name="action", type="string", length=192)
     */
    private $action;

    /**
     * @var array
     * @ORM\Column(name="meta_data", type="array", nullable=true)
     */
    private $meta_data;

    /**
     * @var integer
     * @ORM\Column(name="level", type="smallint")
     */
    private $level;

    /**
     * @var array
     * @ORM\Column(name="extra", type="array", nullable=true)
     */
    private $extra;

    /**
     * @ORM\OneToMany(targetEntity="LogActionRelation", mappedBy="log")
     */
    private $relations;

    public function __construct(ActionFactory $factory, Action $action, array $extra)
    {
        $action->setLog($this);
        $this->setUser($action->getUser());
        $this->setTime(new \DateTime('now'));
        $this->setAction($factory->getAction(get_class($action)));
        $this->setMetaData($action->getLogMetaData());
        $this->setLevel($action->getLevel());
        $this->setExtra($extra);

        $this->relations = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $time
     */
    private function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param array $metaData
     */
    private function setMetaData($metaData)
    {
        $this->meta_data = $metaData;
    }

    /**
     * @return array
     */
    public function getMetaData()
    {
        return $this->meta_data;
    }

    /**
     * @param array $extra
     */
    private function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param User $user
     */
    private function setUser(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param integer $level
     */
    private function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return LogActionRelation[]|ArrayCollection
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
