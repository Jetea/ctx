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

    protected $ctxNamespace = 'Ctx';
}
