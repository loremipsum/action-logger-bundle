<?php

namespace LoremIpsum\ActionLoggerBundle;

abstract class EntityChangeAction extends EntityAction
{
    use ChangeSet;

    protected $preChangeData = [];

    public function __construct(LoggableActionEntity $entity = null)
    {
        parent::__construct($entity);
        if ($entity) {
            $this->preChangeData = $entity->toLogArray();
        }
    }

    protected function getMetaData()
    {
        return array_merge(parent::getMetaData(), [
            'changes' => $this->getChangeSet($this->preChangeData, $this->entity->toLogArray()),
        ]);
    }

    public function skipPersisting()
    {
        return empty($this->meta['changes']);
    }
}
