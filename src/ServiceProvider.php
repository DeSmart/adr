<?php

namespace DeSmart\ADR;

use DeSmart\CommandBus\CommandBus;
use DeSmart\ADR\Responder\Responder;
use DeSmart\ADR\Action\BaseAction;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->resolving(BaseAction::class, function (BaseAction $action) {
            $action->setCommandBus($this->app->make(CommandBus::class));
            $action->setRequest($this->app->make('request'));
            $action->setResponder($this->app->make(Responder::class));

            return $action;
        });
    }
}
