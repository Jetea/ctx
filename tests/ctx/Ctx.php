<?php

namespace Tests\PHPCtx\Ctx;

use PHPCtx\Ctx\Ctx as BasicCtx;

/**
 * Class Ctx
 * @property \Tests\PHPCtx\Ctx\Service\Example\Ctx $Example
 */
class Ctx extends BasicCtx
{
    /**
     * ctx instance
     */
    protected static $ctxInstance;

    //ctx namespace
    protected $ctxNamespace = 'Tests\PHPCtx\Ctx';
}
