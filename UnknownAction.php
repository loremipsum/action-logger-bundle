<?php

namespace LoremIpsum\ActionLoggerBundle;

use LoremIpsum\ActionLoggerBundle\Entity\LogAction;
use LoremIpsum\RouteGeneratorBundle\RouteGeneratorInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class UnknownAction extends Action
{
    public function __construct()
    {
    }

    public function getIcon()
    {
        return 'fa fa-exclamation bg-red';
    }

    public function getLevel()
    {
        return LogAction::LEVEL_WARNING;
    }

    public function getMetaData()
    {
        return [];
    }

    public function getRelations()
    {
        return [];
    }

    protected function load(ObjectManager $em)
    {
    }

    public function getAction()
    {
        return $this->getLog()->getAction();
    }

    public function getMessage(RouteGeneratorInterface $routeGenerator = null)
    {
        $action = $this->getLog()->getAction();
        return ["Unbekannte Aktion \"%action%\" (Log #%id%).", '%action%' => $action, '%id%' => $this->getLog()->getId()];
    }

    public function getUserMessage(RouteGeneratorInterface $routeGenerator = null)
    {
        $action = $this->getLog()->getAction();
        list($user, $users) = $this->getUserLink($routeGenerator);
        return ["Unbekannte Aktion \"%action%\" von Benutzer $user (Log #%id%).", '%action%' => $action, $users, '%id%' => $this->getLog()->getId()];
    }
}
