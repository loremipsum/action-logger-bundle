<?php

namespace LoremIpsum\ActionLoggerBundle;

abstract class EntityAction extends Action
{
    protected $entity;

    public function __construct(LoggableActionEntity $entity = null)
    {
        $this->entity = $entity;
    }

    protected function getMetaData()
    {
        if (! $this->entity) {
            throw new \RuntimeException("Missing entity.");
        }
        if (! $this->entity->getId()) {
            throw new \RuntimeException("Entity {$this->entity} has no id yet. Call flush() before logging entity.");
        }
        return [
            'id'   => $this->entity->getId(),
            'name' => (string)$this->entity,
        ];
    }

    public function getRelations()
    {
        $relations = [];
        if ($this->entity) {
            $relations[get_class($this->entity)] = $this->entity->getId();
        }
        return $relations;
    }
}
