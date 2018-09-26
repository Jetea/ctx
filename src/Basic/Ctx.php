<?php

namespace PHPCtx\Ctx\Basic;

use PHPCtx\Ctx\Exceptions\Exception;

/**
 * 所有的业务模块基类
 *
 * @copyright sh7ning 2016.1
 * @author    sh7ning
 */
abstract class Ctx
{
    /**
     * @var \PHPCtx\Ctx\Ctx $ctx
     */
    public $ctx;

    /**
     * 命名空间
     * 辅助方法有setNamespace() 和 getNamespace()
     */
    private $namespace = '';

    final public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * 模块名
     * 辅助方法有setModName() 和 getModName()
     */
    private $modName = '';

    final public function getModName()
    {
        return $this->modName;
    }

    /**
     * 初始化服务命名空间和模块名，然后调用初始化方法
     *
     * 不采用反射实现，是因为已知可以直接传递，减少计算量
     *
     * @param $namespace
     * @param $modName
     * @throws Exception
     */
    final public function initCtxService($namespace, $modName)
    {
        if ($this->namespace) { //只能被框架调用一次，不允许用户调用
            throw new Exception('method deny, invoke: ' . __METHOD__ . '@' . get_class($this));
        }

        //初始化模块属性
        $this->namespace = $namespace;
        $this->modName = $modName;

        $this->init();
    }

    /**
     * 模块具体执行的初始化方法
     */
    protected function init()
    {
    }

    /**
     * 加载模块子类
     * 备注：这里不直接用 __get() 实例化模块内子类是因为方便加载多个实例化对象，方便子类不同对象复用(如多个profile)
     * 这里用 protected 关键字是为了防止外部模块调用：如 $ctx->模块->loadC()，这样外部模块只能调用模块的mod声明的方法
     * 所有的模块子类只能让mod模块文件去进行调用
     *
     * @param $class
     * @param $args
     * @return \PHPCtx\Ctx\Basic\Ctx
     * @throws Exception
     */
    final protected function loadC($class, ...$args)
    {
        if (! empty($this->modName)) {
            $className = '\\' . $this->namespace . '\Service\\' . $this->modName . '\\Child\\' . $class;

            $subObj = new $className(...$args); //since php 5.6
            if ($subObj instanceof self) {
                /** @var \PHPCtx\Ctx\Basic\Ctx $subObj */
                $subObj->ctx = $this->ctx;
                $subObj->initCtxService($this->namespace, $this->modName);
            }
            return $subObj;
        } else {    //还未完成初始化(在构造函数__construct中调用loadC)是不允许调用父类的loadC
            throw new Exception('can not loadC until construct obj, invoke:' . __METHOD__ . '@' . get_class($this));
        }
    }

    /**
     * 远程Rpc调用
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $args)
    {
        //减少无用的远程调用
        if (empty($this->rpc['method']) || ! in_array($method, $this->rpc['method']) || empty($this->rpc['host'])) {
            throw new Exception('非法调用:' .$method . '@' . get_class($this));
        }

        return $this->invokeRpc($method, $args);
    }

    /**
     * rpc配置
     */
    protected $rpc = [
        'host'      => '',  //网关地址
        'method'    => [], //方法名 减少无用的远程调用
    ];

    /**
     * 执行远程Rpc调用逻辑，方便子类进行更灵活的操作如:显式调用,异步调用等
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    protected function invokeRpc($method, $args)
    {
        //do rpc, like below:
        //$rpc = new JsonRpcClient($this->rpc['host']);
        //return $rpc->exec($this->getModName(), $method, $args);
    }
}
