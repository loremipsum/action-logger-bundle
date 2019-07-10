<?php

namespace LoremIpsum\ActionLoggerBundle\Action;

use Doctrine\Common\Persistence\ObjectManager;
use LoremIpsum\ActionLoggerBundle\Entity\ActionLoggable;

abstract class EntityDeleteAction extends EntityAction
{
    protected $entity;

    public function __construct(ActionLoggable $entity = null)
    {
        parent::__construct($entity);
        if ($entity) {
            $this->getLogMetaData();
        }
    }

    protected function load(ObjectManager $em)
    {
    }
}
