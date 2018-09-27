<?php

namespace Tests\PHPCtx\Ctx\Basic;

use PHPCtx\Ctx\Basic\Ctx as BasicCtx;

/**
 * Class Ctx
 * @package Ctx\Basic
 *
 * @property \Tests\PHPCtx\Ctx\Ctx $ctx
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
