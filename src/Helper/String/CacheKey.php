<?php
namespace MykeOn\Helper\String;

use Slim\Http\Request;
use MykeOn\Helper\String\StringObject;

/**
 * Represents a cache key string
 */
class CacheKey extends StringObject
{
    /**
     * @var string
     */
    private $keyDelimiter = '_';

    /**
     * @var string
     */
    private $string;

    /* -------------------- */

    /**
     * @param Request $request
     * @param string  $keyDelimiter
     */
    public function useRequest(Request $request)
    {
        // Set the key using the URI's path as base
        $this->string = str_replace(
            '/',
            $this->keyDelimiter,
            substr($request->getUri()->getPath(), 1)
        );
        $this->string = preg_replace('/__[a-z]+/', '', $this->string);

        return $this;
    }

    public function __toString()
    {
        return $this->string;
    }
}
