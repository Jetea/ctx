<?php

namespace Tests\Jetea\Ctx;

use Jetea\Ctx\Ctx as BasicCtx;

/**
 * Class Ctx
 * @property \Tests\Jetea\Ctx\Service\Example\Ctx $Example
 */
class Ctx extends BasicCtx
{
    /**
     * ctx instance
     */
    protected static $ctxInstance;

    //ctx namespace
    protected $ctxNamespace = 'Tests\Jetea\Ctx';
}
