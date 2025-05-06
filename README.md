# ActionLogger bundle

Symfony bundle to log custom actions and events with doctrine.

## Configuration

```yaml
# config/packages/lorem_ipsum_action_logger.yaml

lorem_ipsum_action_logger:
    # mapping is used to store actions in the database without using the class name
    mapping:
        user.add: { class: App\Action\User\UserAddAction }
        user.edit: { class: App\Action\User\UserEditAction, alias: 'user_edit' }
        settings.edit: { class: App\Action\SettingsEditAction, alias: ['settings_edit', 'settings_update'] }
    # entity_mapping is used to store action relations to entities in the database using the class name
    entity_mapping:
        user: App\Entity\User
        settings: App\Entity\Settings
```

## Action example

Usage example:
A new user has been created. All you have to do is call `log` or `flashLog`
on our ActionLogger with the `UserAddAction` and the new `$user` entity.

```php
/** @var LoremIpsum\ActionLoggerBundle\ActionLogger $actionLogger **/
$actionLogger->log(new UserAddAction($user));
// Display action message as flash message
$actionLogger->flashLog(new UserAddAction($user), 'success');
```

And here is our `UserAddAction`:

```php
<?php

namespace App\Action\User;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use LoremIpsum\ActionLoggerBundle\Action\EntityAction;
use LoremIpsum\ActionLoggerBundle\Entity\LogAction;
use LoremIpsum\RouteGeneratorBundle\Model\RouteGeneratorInterface;

class UserAddAction extends EntityAction
{
    /**
     * @var User
     */
    protected $entity;

    public function __construct(User $entity = null)
    {
        parent::__construct($entity);
    }

    public function getIcon()
    {
        return 'fa fa-plus';
    }

    public function getLevel(): int
    {
        return LogAction::LEVEL_INFO;
    }

    protected function load(ObjectManager $em)
    {
        $this->entity = $em->getRepository(User::class)->find($this->meta['id']);
    }

    private function getLink(RouteGeneratorInterface $router = null): array
    {
        if (! $router || ! $this->entity instanceof User) {
            return ['%entity%', ['%entity%' => $this->meta['name']]];
        }
        return ["<a href=\"{$router->generate($this->entity)}\">%entity%</a>", ['%entity%' => $this->entity]];
    }

    public function getMessage(RouteGeneratorInterface $router = null)
    {
        list($entity, $entities) = $this->getLink($router);
        return ["User $entity added.", $entities];
    }

    public function getUserMessage(RouteGeneratorInterface $router = null)
    {
        list($user, $users) = $this->getUserLink($router);
        list($entity, $entities) = $this->getLink($router);
        return ["$user added user $entity.", $users, $entities];
    }
}
```

## Event subscriber example

Log all action events with Psr\Log\LoggerInterface.

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
            ActionEvent::class => 'onLog',
        ];
    }
}
```