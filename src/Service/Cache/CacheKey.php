<?php
namespace MykeOn\Service\Cache;

use Slim\Http\{Request, Response};

class CacheKey
{
    private $keyDelimiter = '_';
    private $key;

    public function __construct(Request $request)
    {
        $this->key = str_replace('/', $this->keyDelimiter, substr($request->getUri()->getPath(), 1));
    }

    public function __toString()
    {
        return $this->key;
    }

}
