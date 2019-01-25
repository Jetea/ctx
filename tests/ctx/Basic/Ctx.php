<?php

namespace Tests\Jetea\Ctx\Basic;

use Jetea\Ctx\Basic\Ctx as BasicCtx;

/**
 * Class Ctx
 * @package Ctx\Basic
 *
 * @property \Tests\Jetea\Ctx\Ctx $ctx 声明ctx实例，方便ide跳转
 */
abstract class Ctx extends BasicCtx
{
    protected function invokeRpc($method, $args)
    {
        return sprintf(
            'rpc host: %s, moduleName: %s, method: %s, args: %s',
            $this->rpc['host'],
            $this->getModName(),
            $method,
            var_export($args, true)
        );
    }
}
