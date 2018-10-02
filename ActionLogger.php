<?php

namespace LoremIpsum\ActionLoggerBundle;

use App\Entity\User;
use LoremIpsum\ActionLoggerBundle\Entity\LogAction;
use LoremIpsum\ActionLoggerBundle\Entity\LogActionRelation;
use LoremIpsum\ActionLoggerBundle\Event\ActionEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActionLogger implements ActionLoggerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var User
     */
    private $user;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em,
        SessionInterface $session,
        RouterInterface $router,
        RequestStack $requestStack,
        EventDispatcherInterface $dispatcher,
        ActionFactory $actionFactory
    ) {
        $this->em            = $em;
        $this->tokenStorage  = $tokenStorage;
        $this->session       = $session;
        $this->router        = $router;
        $this->requestStack  = $requestStack;
        $this->dispatcher    = $dispatcher;
        $this->actionFactory = $actionFactory;
    }

    /**
     * Overwrite current user (default user is provided by TokenStorage)
     *
     * @param User $user
     */
    public function setCurrentUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User|null
     */
    public function getCurrentUser()
    {
        if (! $this->user) {
            $token      = $this->tokenStorage->getToken();
            $this->user = $token ? $token->getUser() : null;
        }
        return $this->user;
    }

    /**
     * @param Action $action
     *
     * @return $this
     */
    public function log(Action $action)
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

        $log     = new LogAction($this->actionFactory, $action, $extra);
        $persist = ! $action->skipPersisting();
        if ($persist) {
            $this->em->persist($log);
            foreach ($action->getRelations() as $keyEntity => $keyIds) {
                foreach ((array)$keyIds as $keyId) {
                    $relation = new LogActionRelation();
                    $relation->setLog($log);
                    $relation->setKeyId($keyId);
                    $relation->setKeyEntity($keyEntity);
                    $this->em->persist($relation);
                }
            }
        }

        $this->dispatcher->dispatch(ActionEvent::NAME, new ActionEvent($this->actionFactory, $action));

        if ($persist) {
            $this->em->flush();
        }

        return $this;
    }

    /**
     * @param Action[] $actions
     *
     * @return $this
     */
    public function bulkLog(array $actions)
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

            $log = new LogAction($this->actionFactory, $action, $extra);
            if (! $action->skipPersisting()) {
                $this->em->persist($log);

                foreach ($action->getRelations() as $keyEntity => $keyIds) {
                    foreach ((array)$keyIds as $keyId) {
                        $relation = new LogActionRelation();
                        $relation->setLog($log);
                        $relation->setKeyId($keyId);
                        $relation->setKeyEntity($keyEntity);
                        $this->em->persist($relation);
                    }
                }
            }

            $this->dispatcher->dispatch(ActionEvent::NAME, new ActionEvent($this->actionFactory, $action));
        }

        $this->em->flush();

        return $this;
    }

    /**
     * @param Action $action
     * @param string $flashType supported: success, info, warning, danger
     *
     * @return $this
     * @throws \Twig_Error_Runtime
     */
    public function flashLog(Action $action, $flashType = 'success')
    {
        $this->log($action);

        if ($this->session instanceof Session) {
            $this->session->getFlashBag()
                          ->add($flashType, strip_tags($this->prepareMessage($action->getMessage(), 'strip_tags')));
        }

        return $this;
    }

    /**
     * @param array|string $message Action::getMessage or Action::getUserMessage
     * @param callable $filterCallback
     *
     * @return string
     * @throws \Twig_Error_Runtime
     */
    public function prepareMessage($message, callable $filterCallback)
    {
        if (! is_array($message)) {
            return $filterCallback((string)$message);
        }

        $message = $this->flattenMessageArray($message);

        $str = array_shift($message);
        return \twig_replace_filter($str, array_map(function ($message) use ($filterCallback) {
            return $filterCallback($message);
        }, $message));
    }

    private function flattenMessageArray(array $array)
    {
        $return = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return = array_merge($return, $this->flattenMessageArray($value));
            } else {
                $return[$key] = $value;
            }
        }
        return $return;
    }
}
