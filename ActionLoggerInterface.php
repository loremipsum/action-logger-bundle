<?php

namespace LoremIpsum\ActionLoggerBundle;

use App\Entity\User;

interface ActionLoggerInterface
{
    public function log(Action $action);

    public function setCurrentUser(User $user);

    public function getCurrentUser();

    public function bulkLog(array $actions);

    public function flashLog(Action $action, $flashType = 'success');

    public function prepareMessage($message, callable $filterCallback);
}
