<?php

namespace Jetea\Ctx;

use Jetea\Ctx\Exceptions\Exception;

/**
 * 通用context对象
 *
 * @copyright sh7ning 2016.1
 * @author    sh7ning
 * @version   0.0.1
 * @example
 */
abstract class Ctx
{
    /**
     * 私有克隆函数，防止外办克隆对象
     */
    private function __clone()
    {
    }

    /**
     * 框架单例，静态变量保存全局实例
     * @description 这里设置为private，是为了让该静态属性必须被继承，且必须为 protected
     * @var static
     */
    private static $ctxInstance;

    /**
     * 请求单例
     * @return static
     */
    public static function getInstance()
    {
        if (empty(static::$ctxInstance)) {
            static::$ctxInstance = new static();
        }

        return static::$ctxInstance;
    }

    /**
     * ctx命名空间
     *
     * 采用继承，而不采用反射提高性能
     * $thisReflection = new ReflectionClass($this);
     * $this->ctxNamespace = $thisReflection->getNamespaceName();
     * @var string
     */
    protected $ctxNamespace;

    /**
     * 私有构造函数，防止外界实例化对象
     * Ctx constructor.
     */
    protected function __construct()
    {
    }

    /**
     * 自动单例获取ctx服务框架的模块
     * 模块接口文件必须是单例，防止错误的调用模块接口
     *
     * @param string $m 模块名 模块名首字母必须大写
     * @return mixed
     */
    public function __get($m)
    {
        //不想增加对首字母大小写的判断
        //强制调用的时候模块名大写
        $m = ucfirst($m);
        if (property_exists($this, $m)) {
            throw new Exception("Module name {$m} should begin with a capital letter.");
        }

        $className = '\\' . $this->ctxNamespace . '\Service\\' . $m . '\\Ctx';
        $this->$m = new $className();
        $this->$m->ctx = $this;
        $this->$m->initCtxService($this->ctxNamespace, $m);
        return $this->$m;
    }
}
