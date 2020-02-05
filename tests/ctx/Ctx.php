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
     * @var static
     */
    protected static $ctxInstance;

    /**
     * @var string
     */
    protected $ctxNamespace = 'Tests\Jetea\Ctx';
}
