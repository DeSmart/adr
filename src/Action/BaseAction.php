<?php

namespace DeSmart\ADR\Action;

use DeSmart\CommandBus\Contracts\CommandBus;
use DeSmart\ADR\Responder\Responder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Base action.
 */
class BaseAction extends Controller
{
    use ValidatesRequests;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var CommandBus
     */
    protected $commandBus;

    /**
     * @var Responder
     */
    protected $responder;

    /**
     * Calls an Action's method. If the result is an instance of a Responder,
     * it's method respond() is invoked and the result returned.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function callAction($method, $parameters)
    {
        $response = call_user_func_array([$this, $method], $parameters);

        if (true === $response instanceof Responder) {
            return $response->respond();
        }

        return $response;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param CommandBus $commandBus
     */
    public function setCommandBus(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @param Responder $responder
     */
    public function setResponder(Responder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * @param array $rules
     */
    protected function validateRequest(array $rules)
    {
        $this->validate($this->request, $rules);
    }

    /**
     * @param mixed $payload
     * @param array $includes
     * @return Responder
     */
    protected function respondWith($payload, array $includes = [])
    {
        return $this->responder->with($payload, $includes);
    }
}
