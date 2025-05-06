<?php

namespace LoremIpsum\ActionLoggerBundle\Twig;

use LoremIpsum\ActionLoggerBundle\Action\ActionInterface;
use LoremIpsum\ActionLoggerBundle\Model\ActionLoggerInterface;
use LoremIpsum\RouteGeneratorBundle\Model\RouteGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Environment;
use Twig\TwigFilter;

class LogActionExtension extends AbstractExtension
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
            new TwigFilter('actionMessage', [$this, 'actionMessage'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function actionMessage(Environment $env, ActionInterface $action)
    {
        $message = $action->getUser() === $this->actionLogger->getCurrentUser() ?
            $action->getMessage($this->router) :
            $action->getUserMessage($this->router);

        return $this->actionLogger->prepareMessage($message, function ($message) use ($env) {
            return \twig_escape_filter($env, $message);
        });
    }
}
