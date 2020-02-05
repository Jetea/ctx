<?php

namespace Tests\Jetea;

use Tests\Jetea\Ctx\Ctx;

/**
 * 单元测试
 */
class CtxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Ctx
     */
    protected $ctx;

    public function __construct($name = null, $data = [], $dataName = '')
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

    public function testExampleServiceRpc()
    {
        $modName = 'Example';
        $args = [1, 2, 3];
        $method = 'rpc';

        $rpcResult = $this->ctx->$modName->$method(...$args);
        $this->assertEquals($rpcResult, sprintf(
            'rpc host: %s, moduleName: %s, method: %s, args: %s',
            \Tests\Jetea\Ctx\Service\Example\Ctx::EXAMPLE_CTX_RPC_HOST,
            $modName,
            $method,
            var_export($args, true)
        ));
    }

    public function testCallUndefinedRpcMethod()
    {
        try {
            $this->ctx->Example->rpc1();
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }


    public function testLoadC1()
    {
        $this->ctx->Example->loadCTest1();
        $this->assertTrue(true);
    }

    public function testLoadC2()
    {
        $this->ctx->Example->loadCTest2();
        $this->assertTrue(true);
    }

    public function testLoadC3()
    {
        $this->expectException(\Error::class);
        $this->ctx->Example->loadCTest3();
    }
}
