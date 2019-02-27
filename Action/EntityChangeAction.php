<?php

namespace LoremIpsum\ActionLoggerBundle\Action;

use LoremIpsum\ActionLoggerBundle\Entity\ActionLoggable;

abstract class EntityChangeAction extends EntityAction
{
    use ChangeSet;

    protected $preChangeData = [];

    public function __construct(ActionLoggable $entity = null)
    {
        parent::__construct($entity);
        if ($entity) {
            $this->preChangeData = $entity->toActionLogArray();
        }
    }

    protected function getMetaData()
    {
        return array_merge(parent::getMetaData(), [
            'changes' => $this->getChangeSet($this->preChangeData, $this->entity->toActionLogArray()),
        ]);
    }

    public function skipPersisting()
    {
        return empty($this->meta['changes']);
    }
}
