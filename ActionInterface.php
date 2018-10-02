<?php

namespace LoremIpsum\ActionLoggerBundle;

use App\Entity\User;
use LoremIpsum\RouteGeneratorBundle\RouteGeneratorInterface;

interface ActionInterface
{
    /**
     * @return User|null $User
     */
    public function getUser();

    /**
     * @return string|null
     */
    public function getIcon();

    public function getLevel();

    public function skipPersisting();

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
     * @example ['App\Entity\User' => 1, 'App\Entity\Customer' => [1, 2]]
     * @return array
     */
    public function getRelations();
}
