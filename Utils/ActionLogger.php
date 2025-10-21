<?php

namespace LoremIpsum\ActionLoggerBundle\Utils;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use LoremIpsum\ActionLoggerBundle\Action\ActionInterface;
use LoremIpsum\ActionLoggerBundle\Entity\LogAction;
use LoremIpsum\ActionLoggerBundle\Entity\LogActionRelation;
use LoremIpsum\ActionLoggerBundle\Event\ActionEvent;
use LoremIpsum\ActionLoggerBundle\Factory\ActionFactory;
use LoremIpsum\ActionLoggerBundle\Model\ActionLoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActionLogger implements ActionLoggerInterface
{
    private ?User $user;

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly EntityManagerInterface $em,
        private readonly RequestStack $requestStack,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ActionFactory $actionFactory
    ) {
    }

    /**
     * Overwrite current user (default user is provided by TokenStorage)
     * @param User $user
     */
    public function setCurrentUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCurrentUser(): ?User
    {
        if (! $this->user) {
            $token      = $this->tokenStorage->getToken();
            $this->user = $token?->getUser();
        }
        return $this->user;
    }

    public function log(ActionInterface $action): static
    {
        if (! $action->getUser()) {
            $action->setUser($this->getCurrentUser());
        }

        $extra   = [];
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $extra['request'] = [
                'clientIp' => $request->getClientIp(),
                'port'     => $request->getPort(),
                'uri'      => $request->getUri(),
                'method'   => $request->getMethod(),
            ];
        }

        $this->createLogAction($action, $extra);
        $this->em->flush();

        return $this;
    }

    /**
     * @param ActionInterface[] $actions
     * @return $this
     */
    public function bulkLog(array $actions): static
    {
        if (empty($actions)) {
            return $this;
        }

        $extra   = [];
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $extra['request'] = [
                'clientIp' => $request->getClientIp(),
                'port'     => $request->getPort(),
                'uri'      => $request->getUri(),
                'method'   => $request->getMethod(),
            ];
        }

        foreach ($actions as $action) {
            if (! $action->getUser()) {
                $action->setUser($this->getCurrentUser());
            }
            $this->createLogAction($action, $extra);
        }

        $this->em->flush();

        return $this;
    }

    protected function createLogAction(ActionInterface $action, array $extra): LogAction
    {
        $log = new LogAction($this->actionFactory, $action, $extra);
        if (! $action->skipPersisting()) {
            $this->em->persist($log);
            $this->persistLogRelations($log, $action);
        }

        $this->dispatcher->dispatch(new ActionEvent($this->actionFactory, $action));
        return $log;
    }

    private function persistLogRelations(LogAction $log, ActionInterface $action): void
    {
        foreach ($action->getRelations() as $entityClass => $keyIds) {
            $keyEntity = $this->actionFactory->getEntityKey($entityClass);
            foreach ((array)$keyIds as $keyId) {
                $relation = new LogActionRelation($log, $keyId, $keyEntity);
                $this->em->persist($relation);
            }
        }
    }

    /**
     * @param ActionInterface $action
     * @param string          $flashType supported: success, info, warning, danger
     * @return $this
     */
    public function flashLog(ActionInterface $action, $flashType = 'success'): static
    {
        $this->log($action);

        $this->requestStack->getSession()
                           ->getFlashBag()
                           ->add($flashType, $this->prepareMessage($action->getMessage(), function ($message) {
                               return htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE);
                           }));

        return $this;
    }

    /**
     * @param array|string $message Action::getMessage or Action::getUserMessage
     * @param callable     $filterCallback
     * @return string
     */
    public function prepareMessage($message, callable $filterCallback): string
    {
        if (! is_array($message)) {
            return $filterCallback((string)$message);
        }

        $message = $this->flattenMessageArray($message);

        $str = array_shift($message);
        return strtr($str, array_map(function ($message) use ($filterCallback) {
            return $filterCallback($message);
        }, $message));
    }

    private function flattenMessageArray(array $array)
    {
        $return = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return = array_merge($return, $this->flattenMessageArray($value));
                continue;
            }
            $return[$key] = $value;
        }
        return $return;
    }
}
