<?php

namespace LoremIpsum\ActionLoggerBundle\Action;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use LoremIpsum\ActionLoggerBundle\Entity\LogAction;
use LoremIpsum\RouteGeneratorBundle\Model\RouteGeneratorInterface;

interface ActionInterface
{
    public function setUser(User $user);

    /**
     * @return User|null
     */
    public function getUser();

    /**
     * @param LogAction $log
     */
    public function setLog(LogAction $log);

    /**
     * @return LogAction|null
     */
    public function getLog();

    /**
     * @return string|null
     */
    public function getIcon();

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @return bool
     */
    public function skipPersisting();

    /**
     * @param ObjectManager $em
     * @param LogAction     $log
     */
    public function loadFromLogAction(ObjectManager $em, LogAction $log);

    /**
     * @return array
     */
    public function getLogMetaData();

    /**
     * @param RouteGeneratorInterface $routeGenerator
     * @return string|array
     */
    public function getMessage(RouteGeneratorInterface $routeGenerator = null);

    /**
     * @param RouteGeneratorInterface $routeGenerator
     * @return string|array
     */
    public function getUserMessage(RouteGeneratorInterface $routeGenerator = null);

    /**
     * Returns an associative array of related entities, where the entity name is the key and the id is the value, or
     * @return array
     * @example ['App\Entity\User' => 1, 'App\Entity\Customer' => [1, 2]]
     */
    public function getRelations();
}
