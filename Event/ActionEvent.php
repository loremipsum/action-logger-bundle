<?php

namespace LoremIpsum\ActionLoggerBundle\Event;

use LoremIpsum\ActionLoggerBundle\Action\ActionInterface;
use LoremIpsum\ActionLoggerBundle\ActionFactory;
use Symfony\Component\EventDispatcher\Event;

class ActionEvent extends Event
{
    const NAME = 'actionlogger.log';

    protected $factory;
    protected $action;

    public function __construct(ActionFactory $factory, ActionInterface $action)
    {
        $this->factory = $factory;
        $this->action  = $action;
    }

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return ActionFactory
     */
    public function getActionFactory()
    {
        return $this->factory;
    }
}
