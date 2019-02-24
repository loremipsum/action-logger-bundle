<?php

namespace LoremIpsum\ActionLoggerBundle\Entity;

interface ActionLoggable
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return array
     */
    public function toActionLogArray();

    public function __toString();
}
