<?php

namespace LoremIpsum\ActionLoggerBundle\Entity;

use App\Entity\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LoremIpsum\ActionLoggerBundle\Action\ActionInterface;
use LoremIpsum\ActionLoggerBundle\Factory\ActionFactory;

#[ORM\Table(name: 'log_action', indexes: [new ORM\Index(name: 'logAction_action_idx', columns: ['action'])])]
#[ORM\Entity(repositoryClass: 'LoremIpsum\ActionLoggerBundle\Repository\LogActionRepository')]
class LogAction
{
    const LEVEL_ERROR = 400;
    const LEVEL_WARNING = 300;
    const LEVEL_NOTICE = 250;
    const LEVEL_INFO = 200;
    const LEVEL_DEBUG = 100;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\Column(name: 'time', type: 'datetime')]
    private DateTime $time;

    #[ORM\Column(name: 'action', type: 'string', length: 191)]
    private string $action = "";

    #[ORM\Column(name: 'meta_data', type: 'array', nullable: true)]
    private ?array $metaData = null;

    #[ORM\Column(name: 'level', type: 'smallint')]
    private int $level = 0;

    #[ORM\Column(name: 'extra', type: 'array', nullable: true)]
    private ?array $extra = null;

    /**
     * @var LogActionRelation[]|ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: LogActionRelation::class, mappedBy: 'log')]
    private $relations;

    public function __construct(ActionFactory $factory, ActionInterface $action, array $extra)
    {
        $action->setLog($this);
        $this->setUser($action->getUser());
        $this->setTime(new DateTime('now'));
        $this->setAction($factory->getAction(get_class($action)));
        $this->setMetaData($action->getLogMetaData());
        $this->setLevel($action->getLevel());
        $this->setExtra($extra);

        $this->relations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    private function setTime(DateTime $time): void
    {
        $this->time = $time;
    }

    public function getTime(): DateTime
    {
        return $this->time;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    private function setMetaData(?array $metaData): void
    {
        $this->metaData = $metaData;
    }

    public function getMetaData(): ?array
    {
        return $this->metaData;
    }

    private function setExtra(?array $extra): void
    {
        $this->extra = $extra;
    }

    public function getExtra(): ?array
    {
        return $this->extra;
    }

    private function setUser(?User $user = null): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    private function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getLevel(): int
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
