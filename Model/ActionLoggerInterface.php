<?php

namespace LoremIpsum\ActionLoggerBundle\Model;

use App\Entity\User;
use LoremIpsum\ActionLoggerBundle\Action\ActionInterface;

interface ActionLoggerInterface
{
    public function log(ActionInterface $action);

    public function setCurrentUser(User $user);

    public function getCurrentUser();

    public function bulkLog(array $actions);

    public function flashLog(ActionInterface $action, $flashType = 'success');

    public function prepareMessage($message, callable $filterCallback);
}
