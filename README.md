<p align="center"><img src="https://avatars3.githubusercontent.com/u/43405966?s=200&v=4" title="ctx"</p>

## 关于 Ctx

Ctx 是一个模块化服务上下文框架，帮助模块化组织各种服务逻辑，让服务间调用方式更加统一。

平时调用其他服务的方式一般是 `new XyzService()`或则采用`依赖注入`的方式实例化服务，前者会导致一个服务被实例化多次，服务提供方不能更好的进行控制，后者则会存在服务互相依赖的时候造成困扰，如果服务要单例的时候你还需要在服务提供者privider的地方声明服务为单例。

Ctx 提供了一种新的选择，每个模块都只会被实例化一次，每个模块的服务只会提供唯一的入口暴露给调用方，模块不支持多实例，这样可以让模块提供者可以更容易进行服务的控制和维护，所有的模块之间调用方式一致，如：

```
$this->ctx->模块名->方法()
```

同时Ctx提供了很方便的方式将模块方法rpc化，方便单独部署某些接口，比如某个接口频率特别高需要单独优化部署等或则这个接口需要进行保密，如加密算法等。

## 题外话

* 模块化开发：不同的模块只能操作自己的数据（包括数据库和缓存等），需要其他模块数据，只能让对应模块的开发人员提供接口，这样每个模块的人只需要了解自己模块的实现，减少复杂度和新人入职加入模块开发维护的难度（只需要了解他负责的模块），同时减少错误的其他模块数据操作，因为有的模块采用异步或则定时任务或则缓存的方式，直接操作对应模块，可能会因为不了解业务而导致数据操作遗漏带来脏数据。

* Ctx单独成为项目进行部署：减少不同的项目共用逻辑的时候拷贝代码，如果是每个都采用微服务减少拷贝代码又会增加网络开销，所以只需要把ctx发布到需要的项目即可。所有的项目依赖共同的ctx服务，每个项目只负责参数的获取和ctx服务的调用组装实现业务逻辑，每个项目实现参数获取和响应输出，具体业务逻辑都要依赖 ctx 进行实现。为了方便ctx模块中的方法的共用，不建议直接把 `$request` 这样的请求对象作为参数传递给 ctx模块的方法，也就是不要在controller之外的地方进行输入参数的获取，而是用参数的方式传递给service。项目关系大概组织为：

  ```
  web项目文件夹
  api项目文件夹
  脚本文件夹
  admin项目文件夹
  
  ctx文件夹 (供上边所有项目共用)
  ```

## 安装

```
composer require jetea/ctx=~1.0
```

## 编写ctx服务

在调用服务之前需要先编写服务，以下将描述一个服务的编写过程。源码参考参考：[https://github.com/jetea/ctx/tree/master/tests/ctx](https://github.com/jetea/ctx/tree/master/tests/ctx)

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

2. 编写**Ctx入口类**：`Ctx.php`，为Ctx服务唯一入口，所有的模块调度都要通过此类实现，此类为**单例实现**。参考[https://github.com/jetea/ctx/blob/master/tests/ctx/Ctx.php](https://github.com/jetea/ctx/blob/master/tests/ctx/Ctx.php)

   ```
   <?php
   
   namespace Ctx;
   
   use Jetea\Ctx\Ctx as BasicCtx;
   
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

3. 新增`Basic文件夹`，包含 **所有的服务模块类的基类** `Ctx` ，也可以放入公共的服务模块异常`Exception`类等。

4. 编写**所有的服务模块类的基类**`Basic/Ctx.php`，方便所有的模块类继承实现公共逻辑处理和复用，如所有的服务的rpc实现等，参考[https://github.com/jetea/ctx/blob/master/tests/ctx/Basic/Ctx.php](https://github.com/jetea/ctx/blob/master/tests/ctx/Basic/Ctx.php)

   ```
   <?php
   
   namespace Ctx\Basic;
   
   use Jetea\Ctx\Basic\Ctx as BasicCtx;
   
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

   所有的**Service模块入口类**都必须继承此类，模块子类可以选择性继承此类，所有继承了此类的服务类都会拥有`$ctx`属性，从而能在服务内简单的通过`$this->ctx->模块->方法()`这样的方式来调用其他模块的方法。如果需要采用rpc将个别服务独立部署和优化等，可以在此类中通过实现`invokeRpc`方法来实现具体的rpc调用逻辑，这样所有继承了此基类的方法均很容易实现rpc调用。

5. 新建`Service文件夹`，此文件夹用于包含所有的模块具体实现。

6. 新建模块文件夹，如`Example`，一般模块名跟业务有关，如`User`表示用户服务模块，`Payment`表示支付服务模块，此文件夹下将存放所有的此模块的具体实现。

7. 编写**Service模块入口类**，如`Service/Example/Ctx.php`，此类为`Example`模块入口，此类为**单例实现**，所有调用该模块的方法都要走此类进行调度当前模块下的方法的子类的方法，参考[https://github.com/jetea/ctx/blob/master/tests/ctx/Service/Example/Ctx.php](https://github.com/jetea/ctx/blob/master/tests/ctx/Service/Example/Ctx.php)

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

调用ctx服务指调用Service模块服务下的方法，只能调用到模块的入口类方法，这样的调用方式是为了更好的限制了所有服务调用都要走调用的模块下的入口类，方便统一进行处理，调用ctx服务分服务外部调用和服务内部调用：

* Service模块服务内调用

   ```
   $this->ctx->模块->方法()
   ```

* Service模块服务外调用

   1. 实例化ctx服务，参考[https://github.com/jetea/ctx/blob/master/tests/CtxTest.php](https://github.com/jetea/ctx/blob/master/tests/CtxTest.php)

   ```
   \Ctx\Ctx::getInstance();
   ```

   2. 如果ctx服务的实例为 `$ctx`

   ```
   $ctx->模块->方法()
   ```


## 其它

* 如果服务类加载失败，多半是因为composer中没有声明Ctx服务模块需要的命名空间
* 所有的Service模块入口类都**不允许**实现`__construct`方法，如果需要初始化模块只能实现`init`方法，所有的模块入口初始化的时候都会执行init方法，如 `$this->ctx->Example`将会实例User模块的入口类`\Ctx\Service\Example\Ctx`，同时会调用其中的`init`方法。
* Ctx服务实例 和 模块入口类实例 都只会 有一个，即单例，方便模块进行更好的处理。
* 所有继承了 **所有的服务模块类的基类** 的类，都会拥有：
  1.  `ctx`属性，此属性为ctx服务实例，方便在服务内调用其他的模块方法
  2. 如果基类实现了`invokeRpc`方法，则所有继承的类都会拥有rpc实现。
  3. `loadC`方法，模块内加载其他类，模块类所有非入口类都需要放到 `Child`文件夹下通过模块内的`loadC`方法进行实例化
* 所有模块内的非入口类无论是否继承 **所有的服务模块类的基类**，都能实现 `__construct`方法，但是继承了基类的话一定会在实例化后，被调用 `init` 方法。 

* 模块入口类或模块子类rpc实现：

  1. **所有的服务模块类的基类** 实现`invokeRpc`方法

  2. 模块入口类或模块子类继承 **所有的服务模块类的基类**

  3. 模块入口类或模块子类重载属性`$rpc`，属性访问方式为`protected`，属性为数组，拥有两个字段`host`和`method`，其中`host`表示rpc方法的远程host，`method`表示允许执行的rpc方法数组。

     ```
     /**
      * rpc配置
      */
     protected $rpc = [
     	'host'      => '',  //网关地址
     	'method'    => [], //方法名 减少无用的远程调用
     ];
     ```
     
