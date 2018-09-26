<p align="center"><img src="https://avatars3.githubusercontent.com/u/43405966?s=200&v=4" title="ctx"</p>

## 关于 Ctx

Ctx 是一个模块化服务上下文框架，帮助开发者模块化的组织各种服务，让服务间调用方式更加统一。

平时调用其他服务的方式一般是 `new XyzService()`或则采用`依赖注入`的方式实例化服务，前者会导致一个服务被实例化多次，服务提供方不能更好的进行控制，后者则会存在服务互相依赖的时候造成困扰，如果服务要单例的时候你还需要在服务提供者privider的地方声明服务为单例。

Ctx 提供了一种新的选择，每个模块都只会被实例化一次，每个模块的服务只会提供唯一的入口暴露给调用方，模块不支持多实例，这样可以让模块提供者可以更容易进行服务的控制和维护，所有的模块之间调用方式一致，如：

```
$this->ctx->模块名->方法()
```

## 安装

```
composer require phpctx/ctx=~1.0
```

## 编写ctx服务

在调用服务之前需要先编写服务，以下将描述一个服务的编写过程。源码参考参考：[https://github.com/phpctx/ctx/tree/master/tests/ctx](https://github.com/phpctx/ctx/tree/master/tests/ctx)

参考目录树结构如：

```
ctx 根文件夹
├── Ctx.php					Ctx入口类
├── Basic/					文件夹
│   └── Ctx.php				各个Ctx服务基类
└── Service/				Service文件夹，包括所有的服务模块
    └── Example/			服务模块1：Example模块
        └── Ctx.php			服务模块入口
        ├── Child/			模块子类文件夹
        │   └── Demo.php	模块子类	
    └── Example2/			服务模块2：Example2模块
        ├── Child/
        │   └── Demo.php
        └── Ctx.php		
```

1. 新建根文件夹，名称随意，如 `ctx`

2. 编写***Ctx入口类***：`Ctx.php`，为Ctx服务唯一入口，所有的模块调度都要通过此类实现，此类为***单例实现***。参考[https://github.com/phpctx/ctx/blob/master/tests/ctx/Ctx.php](https://github.com/phpctx/ctx/blob/master/tests/ctx/Ctx.php)

   ```
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
        * ctx 实例
        */
       protected static $ctxInstance;
   
       //ctx 命名空间
       protected $ctxNamespace = 'Ctx';
   }
   ```

   Note: 必须提供  `$ctxInstance` 静态属性和 `$ctxNamespace`属性，访问类型必须为`protected`，目录下所有的类遵循`PSR-4`标准，`$ctxNamespace`决定了文件夹下的命名空间，`$ctxInstance`和`$ctxNamespace`这两个属性是为了支持多个ctx服务，只要确保ctx命名空间`$ctxNamespace`值不同，如：

   ```
   ctx 	ctx根文件夹
   ├── Ctx.php					Ctx入口类
   ├── Basic/					文件夹
   └── Service/				Service文件夹，包括所有的服务模块
           
   ctx_1 	ctx_1根文件夹
   ├── Ctx.php					Ctx入口类
   ├── Basic/					文件夹
   └── Service/				Service文件夹，包括所有的服务模块	
   ```

   不过建议一个团队内尽量放到一个ctx服务中按照模块进行开发。

3. 新增`Basic文件夹`，包含 ***所有的服务模块类的基类*** `Ctx` ，也可以放入公共的服务模块异常`Exception`类等。

4. 编写***所有的服务模块类的基类***`Basic/Ctx.php`，方便所有的模块类继承实现公共逻辑处理和复用，如所有的服务的rpc实现等，参考[https://github.com/phpctx/ctx/blob/master/tests/ctx/Basic/Ctx.php](https://github.com/phpctx/ctx/blob/master/tests/ctx/Basic/Ctx.php)

   ```
   <?php
   
   namespace Ctx\Basic;
   
   use PHPCtx\Ctx\Basic\Ctx as BasicCtx;
   
   /**
    * Class Ctx
    * @package Ctx\Basic
    *
    * @property \Ctx\Ctx $ctx
    */
   abstract class Ctx extends BasicCtx
   {
       protected function invokeRpc($method, $args)
       {
           // $this->rpc['host'],
           // $this->getModName(),
           // $method,
           // var_export($args, true)
       }
   }
   ```

   所有的***Service模块入口类***都必须继承此类，模块子类可以选择性继承此类，如果需要采用rpc将个别服务独立部署和优化等，可以在此类中通过实现`invokeRpc`方法来实现具体的rpc调用逻辑，这样所有继承了此基类的方法均很容易实现rpc调用。

5. 新建`Service文件夹`，此文件夹用于包含所有的模块具体实现。

6. 新建模块文件夹，如`Example`，一般模块名跟业务有关，如`User`表示用户服务模块，`Payment`表示支付服务模块，此文件夹下将存放所有的此模块的具体实现。

7. 编写***Service模块入口类***`Service/Ctx.php`，此类为模块入口，此类为***单例实现***，所有调用该模块的方法都要走此类进行调度当前模块下的方法的子类的方法，参考[https://github.com/phpctx/ctx/blob/master/tests/ctx/Service/Example/Ctx.php](https://github.com/phpctx/ctx/blob/master/tests/ctx/Service/Example/Ctx.php)

   ```
   <?php
   
   namespace Ctx\Service\Example;
   
   use Ctx\Basic\Ctx as BasicCtx;
   
   /**
    * 模块接口声明文件
    * 备注：文件命名跟模块中的其他类不同，因为模块入口类只能被实例化一次
    * 也就是只能用ctx->模块 来实例化，不能用loadC来实例化更多
    */
   class Ctx extends BasicCtx
   {
   }
   ```

## 调用ctx服务

1. 实例化ctx服务

```
\Ctx\Ctx::getInstance();
```

2. 