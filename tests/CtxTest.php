<?php

namespace Tests\PHPCtx\Ctx;

use Ctx\Ctx;

/**
 * @todo 增加rpc的单测
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

    }
}
