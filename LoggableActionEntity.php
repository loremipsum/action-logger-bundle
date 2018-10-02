<?php

namespace LoremIpsum\ActionLoggerBundle;

interface LoggableActionEntity
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return array
     */
    public function toLogArray();

    public function __toString();
}
