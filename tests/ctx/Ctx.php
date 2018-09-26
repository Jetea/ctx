<?php

namespace Ctx;

use PHPCtx\Ctx\Ctx as BasicCtx;

/**
 * Class Ctx
 * @property \Ctx\Service\Example\Ctx $Example
 */
class Ctx extends BasicCtx
{
    /**
     * ctx instance
     */
    protected static $ctxInstance;

    //ctx namespace
    protected $ctxNamespace = 'Ctx';
}
