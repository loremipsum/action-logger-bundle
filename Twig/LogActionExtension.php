<?php

namespace LoremIpsum\ActionLoggerBundle\Twig;

use LoremIpsum\ActionLoggerBundle\Action\ActionInterface;
use LoremIpsum\ActionLoggerBundle\Model\ActionLoggerInterface;
use LoremIpsum\RouteGeneratorBundle\Model\RouteGeneratorInterface;

class LogActionExtension extends \Twig_Extension
{
    /**
     * @var RouteGeneratorInterface
     */
    protected $router;

    /**
     * @var ActionLoggerInterface
     */
    protected $actionLogger;

    public function __construct(RouteGeneratorInterface $router, ActionLoggerInterface $actionLogger)
    {
        $this->router       = $router;
        $this->actionLogger = $actionLogger;
    }

    public function getFilters()
    {
        return [
            new \Twig_Filter('actionMessage', [$this, 'actionMessage'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function actionMessage(\Twig_Environment $env, ActionInterface $action)
    {
        $message = $action->getUser() === $this->actionLogger->getCurrentUser() ?
            $action->getMessage($this->router) :
            $action->getUserMessage($this->router);

        return $this->actionLogger->prepareMessage($message, function ($message) use ($env) {
            return \twig_escape_filter($env, $message);
        });
    }
}
