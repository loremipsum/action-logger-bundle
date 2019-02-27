<?php

namespace LoremIpsum\ActionLoggerBundle\Action;

use App\Entity\User;
use LoremIpsum\ActionLoggerBundle\Entity\LogAction;
use LoremIpsum\RouteGeneratorBundle\Model\RouteGeneratorInterface;
use Doctrine\Common\Persistence\ObjectManager;

abstract class Action implements ActionInterface
{
    /**
     * @var LogAction
     */
    protected $log;

    /**
     * @var array
     */
    protected $meta;

    /**
     * @var User|null
     */
    protected $user;

    public function setLog(LogAction $log)
    {
        $this->log = $log;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getIcon()
    {
        return null;
    }

    public function skipPersisting()
    {
        return false;
    }

    public function getLogMetaData()
    {
        if (! $this->meta) {
            $this->meta = $this->getMetaData();
        }
        return $this->meta;
    }

    abstract protected function getMetaData();

    public function loadFromLogAction(ObjectManager $em, LogAction $log)
    {
        $this->setLog($log);
        $this->setUser($log->getUser());
        $this->meta = $log->getMetaData();
        $this->load($em);
    }

    abstract protected function load(ObjectManager $em);

    protected function getUserLink(RouteGeneratorInterface $routeGenerator = null)
    {
        $user = $this->getUser();
        if (! $routeGenerator) {
            return ['%user%', ['%user%' => (string)$user]];
        }

        return [
            "<a href=\"{$routeGenerator->generate($user)}\">%user%</a>",
            ['%user%' => (string)$user],
        ];
    }
}
