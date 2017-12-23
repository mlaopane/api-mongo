<?php

namespace MykeOn\Service\Database;

interface DatabaseInterface
{
    /**
     * Return the URI used to connect to a database
     * @return string
     */
    public function getUri();
}