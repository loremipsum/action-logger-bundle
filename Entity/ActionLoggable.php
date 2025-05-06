<?php

namespace LoremIpsum\ActionLoggerBundle\Entity;

interface ActionLoggable
{
    public function getId();

    public function toActionLogArray(): array;

    public function __toString();
}
