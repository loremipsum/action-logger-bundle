<?php

namespace LoremIpsum\ActionLoggerBundle;

use LoremIpsum\ActionLoggerBundle\Action\ActionInterface;
use LoremIpsum\ActionLoggerBundle\Action\UnknownAction;
use LoremIpsum\ActionLoggerBundle\Entity\LogAction;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ActionFactory
{
    /**
     * @var ObjectManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $actionMapping;

    /**
     * @var array
     */
    protected $entityMapping;

    public function __construct(ObjectManager $entityManager, array $mapping, array $entityMapping)
    {
        $this->entityManager = $entityManager;
        $this->actionMapping = $mapping;
        $this->entityMapping = $entityMapping;
    }

    /**
     * @return array List of action_name => Action class
     */
    public function getActionMappings()
    {
        $mapping = [];
        foreach ($this->actionMapping as $action => $definition) {
            if (isset($mapping[$action])) {
                throw new \RuntimeException("Multiple definitions for action '{$action}' not allowed.");
            }
            $mapping[$action] = $definition['class'];
        }
        return $mapping;
    }

    /**
     * @return array List of action_name => [List of aliases (will be replaced in database)]
     */
    public function getAliasMappings()
    {
        $aliases = [];
        foreach ($this->actionMapping as $action => $definition) {
            if (empty($definition['alias'])) {
                continue;
            }
            if (isset($aliases[$action])) {
                throw new \RuntimeException("Multiple definitions for action '{$action}' not allowed.");
            }
            $aliases[$action] = (array)$definition['alias'];
        }
        return $aliases;
    }

    public function getAction($class)
    {
        $mapping = array_flip($this->getActionMappings());
        if (! isset($mapping[$class])) {
            throw new \RuntimeException("Missing action mapping for class $class");
        }
        return $mapping[$class];
    }

    /**
     * @param LogAction[]|Paginator $logs
     *
     * @return ActionInterface[]
     * @throws \Exception
     */
    public function getActionsFromLogs($logs)
    {
        $actions = [];
        foreach ($logs as $log) {
            $actions[] = $this->getActionFromLog($log);
        }
        return $actions;
    }

    public function getActionFromLog(LogAction $log)
    {
        $action = $this->getActionInstance($log);
        $action->loadFromLogAction($this->entityManager, $log);
        return $action;
    }

    /**
     * @param LogAction $log
     * @return ActionInterface
     */
    protected function getActionInstance(LogAction $log)
    {
        $action = $log->getAction();

        $mappings = self::getActionMappings();
        if (isset($mappings[$action])) {
            return new $mappings[$action]();
        }

        foreach ($this->getAliasMappings() as $mappingAction => $aliases) {
            if (! in_array($action, $aliases)) {
                continue;
            }
            if (! isset($mappings[$mappingAction])) {
                throw new \RuntimeException("$action found as alias for $mappingAction, but $mappingAction has no action mapping");
            }
            $log->setAction($mappingAction);
            $this->entityManager->flush();
            return new $mappings[$mappingAction]();
        }

        return new UnknownAction();
    }

    /**
     * @return array List of key => Entity class
     */
    public function getEntityMappings()
    {
        return $this->entityMapping;
    }

    /**
     * @param string $class
     * @return string
     */
    public function getEntityKey($class)
    {
        $mapping = array_flip($this->entityMapping);
        if (! isset($mapping[$class])) {
            throw new \RuntimeException("Missing entity mapping for class $class");
        }
        return $mapping[$class];
    }
}
