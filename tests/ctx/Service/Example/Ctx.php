<?php

namespace Ctx\Service\Example;

use Ctx\Basic\Ctx as BasicCtx;

/**
 * 模块接口声明文件
 * 备注：文件命名跟模块中的其他类不同，因为要防止模块声明类只能被实例化一次
 * 也就是只能用ctx->模块 来实例化，不能用loadC来实例化更多
 */
class Ctx extends BasicCtx
{
    /**
     * @var \Ctx\Service\Example\Child\Demo
     */
    private $demo;

    /**
     * @throws \PHPCtx\Ctx\Exceptions\Exception
     */
    public function init()
    {
        $this->demo = $this->loadC('Demo', 'hello');
    }

    /**
     * 测试代码
     */
    public function setMessage($var)
    {
        return $this->demo->setMessage($var);
    }

    public function getMessage()
    {
        return $this->demo->getMessage();
    }
}
