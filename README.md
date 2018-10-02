# ActionLogger

## Configuration

```yaml
# config/packages/li_action_logger.yaml 

li_action_logger:
    mapping:
        foo.bar: { class: App\Action\Foo\Bar }
        foo.baz: { class: App\Action\Foo\Baz, alias: 'Foo.Baz' }
        bar.foo: { class: App\Action\Bar\Foo, alias: ['Bar.Foo', 'BarFoo'] }
```

## Event subscriber example

```php
<?php

namespace App\EventListener;

use LoremIpsum\ActionLoggerBundle\Event\ActionEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActionLogListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onLog(ActionEvent $event)
    {
        $action = $event->getAction();
        $this->logger->debug('[ActionLogListener] ' . $event->getActionFactory()->getAction(get_class($action)), $action->getUserMessage());
    }

    public static function getSubscribedEvents()
    {
        return [
            ActionEvent::NAME => 'onLog',
        ];
    }
}
```