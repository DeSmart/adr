<?php

namespace DeSmart\ADR;

use DeSmart\CommandBus\CommandBus;
use DeSmart\ADR\Responders\Responder;
use DeSmart\ADR\Actions\BaseAction;

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
