<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;

trait ActionTrait
{
    /**
     * @var array
     */
    protected $actions = [
        '_search',
    ];

    /**
     *
     * @param  string   $level
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $arguments
     *
     * @return Response
     */
    protected function handleActionRequest(string $level, Request $request, Response $response, array $arguments): Response
    {
        // The action doesn't exist
        if (false === array_search($arguments['action'], $this->actions)) {
            return $response
                ->withStatus(400, "Unknown action")
                ->withJson([
                    "error" => "Unknown action",
                    'action' => $arguments['action'],
                ]);

        // The action exists
        } else {
            $level = ucfirst(mb_strtolower($level, 'UTF-8'));
            $action = ucfirst(mb_strtolower(str_replace('_', '', $arguments['action']), 'UTF-8'));
            $method = "handle${level}${action}Request";

            // The action is not handled
            if (!method_exists($this, $method)) {
                return $response
                    ->withStatus(501, "Known action Not handled")
                    ->withJson([
                        "error" => "The action exists but is not handled",
                        'action' => $arguments['action'],
                    ]);
            }

            return $this->$method($request, $response, $arguments);
        }
    }
}
