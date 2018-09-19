<?php

namespace Tests\PHPCtx\Ctx;

use Ctx\Ctx;

/**
 * 单元测试
 */
class CtxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Ctx
     */
    protected $ctx;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        //初始化用于测试的ctx单例对象
        $this->ctx = Ctx::getInstance();
    }

    public function testExampleService()
    {
        $ret = $this->ctx->Example->setMessage('Ctx.');
        $this->assertEquals(true, $ret);

        $ret = $this->ctx->Example->getMessage();
        $this->assertEquals('hello Ctx.', $ret);

        //factory
        /** @var Ctx $ctx */
        $ctx = Ctx::getInstance();
        $ret = $ctx->Example->getMessage();
        $this->assertEquals('hello Ctx.', $ret);
    }
}
