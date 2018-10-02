<?php

namespace LoremIpsum\ActionLoggerBundle\Twig;

use LoremIpsum\ActionLoggerBundle\ActionInterface;
use LoremIpsum\ActionLoggerBundle\ActionLoggerInterface;
use LoremIpsum\RouteGeneratorBundle\RouteGeneratorInterface;

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
        if ($action->getUser() === $this->actionLogger->getCurrentUser()) {
            $message = $action->getMessage($this->router);
        } else {
            $message = $action->getUserMessage($this->router);
        }

        return $this->actionLogger->prepareMessage($message, function ($message) use ($env) {
            return \twig_escape_filter($env, $message);
        });
    }
}
