### 使用引用

**场景一：遍历一个数组获取新的数据结构**

也许你会这样写：

```
// 申明一个新的数组,组装成你想要的数据
$tmp = [];
foreach ($arr as $k => $v) {
    // 取出你想要的数据
    $tmp[$k]['youwant'] = $v['youwant'];
    ...
    // 一系列判断得到你想要的数据
    if (...) {
        $tmp[$k]['youwantbyjudge'] = 'TIGERB';
    }
    ...
}
// 最后得要你想要的数组$tmp

-------------------------------------------------------

// 也许你觉着上面的写法不是很好，那我们下面换种写法
foreach ($arr as $k => $v) {
    // 一系列判断得到你想要的数据
    if (...) {
        // 复写值为你想要的
        $arr[$k]['youwantbyjudge'] = 'TIGERB'
    }
    ...
    // 干掉你不想要的结构
    unset($arr[$k]['youwantdel']);
}
// 最后我们得到我们的目标数组$arr
```


接下来我们使用引用值：

```
foreach ($arr as ＆$v) {
    // 一系列判断得到你想要的数据
    if (...) {
        // 复写值为你想要的
        $v['youwantbyjudge'] = 'TIGERB'
    }
    ...
    // 干掉你不想要的结构
    unset($v['youwantdel']);
}
unset($v);
// 最后我们得到我们的目标数组$arr
```

使用引用是不是使我们的代码更加的简洁，除此之外相对于第一种写法，我们节省了内存空间，尤其是再操作一个大数组时效果是及其明显的。

**场景二：传递一个值到一个函数中获取新的值**

基本和数组遍历一致，我们只需要声明这个函数的这个参数为引用即可，如下：

```
function decorate(&$arr = []) {
    # code...
}

$arr = [
    ....
];
// 调用函数
decorate($arr);
// 如上即得到新的值$arr，好处还是节省内存空间

```

### 使用try...catch...

假如有下面一段逻辑：
```
class UserModel
{
    public function login($username = '', $password = '')
    {
        code...
        if (...) {
            // 用户不存在
            return -1;
        }
        code...
        if (...) {
            // 密码错误
            return -2;
        }
        code...
    }
}

class UserController
{
    public function login($username = '', $password = '')
    {
        $model = new UserModel();
        $res   = $model->login($username, $password);
        if ($res === -1) {
            return [
                'code' => '404',
                'message' => '用户不存在'
            ];
        }
        if ($res === -2) {
            return [
                'code' => '400',
                'message' => '密码错误'
            ];
        }
        code...
    }
}
```

我们用try...catch...改写后：
```
class UserModel
{
    public function login($username = '', $password = '')
    {
        code...
        if (...) {
            // 用户不存在
            throw new Exception('用户不存在', '404');
        }
        code...
        if (...) {
            // 密码错误
            throw new Exception('密码错误', '400');
        }
        code...
    }
}

class UserController
{
    public function login($username = '', $password = '')
    {
        try {
            $model = new UserModel();
            $res   = $model->login($username, $password);
            // 如果需要的话，我们可以在这里统一commit数据库事务
            // $db->commit();
        } catch (Exception $e) {
            // 如果需要的话，我们可以在这里统一rollback数据库事务
            // $db->rollback();
            return [
                'code'    => $e->getCode(),
                'message' => $e->getMessage()
            ]
        }
    }
}
```

通过使用try...catch...使我们的代码逻辑更加清晰，try...里只需要关注业务正常的情况，异常的处理统一在catch中。所以，我们在写上游代码时异常直接抛出即可。

### 使用匿名函数

** 构建函数或方法内部的代码块 **

假如我们有一段逻辑，在一个函数或者方法里我们需要格式化数据,但是这个格式化数据的代码片段出现了多次，如果我们直接写可能会想下面这样：

```
function doSomething(...) {
    ...
    // 格式化代码段
    ...
    ...
    // 格式化代码段[重复的代码]
    ...
}
```

我相信大多数的人应该不会像上面这么写，可能都会像下面这样：

```
function doSomething(...) {
    ...
    format(...);
    ...
    format(...);
    ...
}

// 再声明一个格式花代码的函数或方法
function format() {
    // 格式化代码段
    ...
}
```

上面这样的写法没有任何的问题，最小单元化我们的代码片段，但是如果这个format函数或者方法只是doSomething使用呢？我通常会像下面这么写，为什么？因为我认为在这种上下文的环境中format和doSomething的一个子集。

```
function doSomething() {
    ...
    $package = function (...) use (...) {　// 同样use后面的参数也可以传引用
        // 格式化代码段
        ...
    };
    ...
    package(...);
    ...
    package(...);
    ...
}
```

** 实现类的【懒加载】和实现设计模式的【最少知道原则】 **

假如有下面这段代码：

```
class One
{
    private $instance;

    // 类One内部依赖了类Two
    // 不符合设计模式的最少知道原则
    public function __construct()
    {  
        $this->intance = new Two();
    }

    public function doSomething()
    {
        if (...) {
            // 如果某种情况调用类Two的实例方法
            $this->instance->do(...);
        }
        ...
    }
}
...

$instance = new One();
$instance->doSomething();
...
```

上面的写法有什么问题？

- 不符合设计模式的最少知道原则,类One内部直接依赖了类Two
- 类Two的实例不是所有的上下文都会用到，所以浪费了资源，有人说搞个单例，但是解决不了实例化了不用的尴尬

所以我们使用匿名函数解决上面的问题，下面我们这么改写：

```
class One
{
    private $closure;

    public function __construct(Closure $closure)
    {  
        $this->closure = $closure;
    }

    public function doSomething()
    {
        if (...) {
            // 用的时候再实例化
            // 实现懒加载
            $instance = $this->closure();
            $instance->do(...)
        }
        ...
    }
}
...

$instance = new One(function () {
    // 类One外部依赖了类Two
    return new Two();
});
$instance->doSomething();
...
```

### 减少对if...else...的使用

如果你碰见下面这种类型的代码，那一定是个黑洞。

```
function doSomething() {
    if (...) {
        if (...) {
            ...
        } esle {
            ...
        }
    } else {
        if (...) {
            ...
        } esle {
            ...
        }
    }
}

```

** 提前return异常　**

细心的你可能会发现上面这种情况，可能绝大多数else代码里都是在处理异常情况，更有可能这个异常代码特别简单，通常我会这么去做：

```
//　如果是在一个函数里面我会先处理异常的情况，然后提前return代码，最后再执行正常的逻辑
function doSomething() {
    if (...) {
        // 异常情况
        return ...;
    }
    if (...) {
        // 异常情况
        return ...;
    }
    //　正常逻辑
    ...
}

//　同样，如果是在一个类里面我会先处理异常的情况，然后先抛出异常
class One
{
    public function doSomething()
    {
        if (...) {
            // 异常情况
            throw new Exception(...);
        }
        if (...) {
            // 异常情况
            throw new Exception(...);
        }
        //　正常逻辑
        ...
    }
}

```

** 关联数组做map　**

如果我们在客户端做决策，通常我们会判断不同的上下文在选择不同策略，通常会像下面一样使用if或者switch判断:

```
class One
{
    public function doSomething()
    {
        if (...) {
            $instance = new A();
        }　elseif (...) {
            $instance = new A();
        } else {
            $instance = new C();
        }
        $instance->doSomething(...);
        ...
    }
}
```

上面的写法通常会出现大量的if语句或者switch语句，通常我会使用一个map来映射不同的策略，像下面这样：

```
class One
{
    private $map = [
        'a' => 'namespace\A', // 带上命名空间，因为变量是动态的
        'b' => 'namespace\B',
        'c' => 'namespace\C'
    ];
    public function doSomething()
    {
        ...
        $instance = new $this->map[$strategy];// $strategy是'a'或'b'或'c'
        $instance->doSomething(...);
        ...
    }
}
```

### 使用接口

为什么要使用接口？极大的便于后期的扩展和代码的可读性，例如设计一个优惠系统，不同的商品只是在不同的优惠策略下具备不同的优惠行为，我们定义一个优惠行为的接口，最后对这个接口编程即可，伪代码如下

```
Interface Promotion
{
    public function promote(...);
}

class OnePromotion implement Promotion
{
    public function doSomething(...)
    {
        ...
    }
}

class TwoPromotion implement Promotion
{
    public function doSomething(...)
    {
        ...
    }
}
```

### 控制器拒绝直接的DB操作

最后我想说的是永远拒绝在你的Controller里直接操作DB，为什么？我们的程序绝大多数的操作基本都是增删改查，可能是查询的where条件和字段不同，所以有时候我们可以抽象的把对数据库增删改查的方法写到model中，通过参数暴露我们的where，fields条件。通常这样可以很大程度的提高效率和代码复用。比如像下面这样：

```
class DemoModel implement Model
{
    public function getMultiDate($where = [], $fields = ['id'], $orderby = 'id asc')
    {
        $this->where($where)
             ->field($fields)
             ->orderby($orderby)
             ->get();
    }
}
```

### 最后

如果有写的不对的地方，欢迎大家指正,THX～

> [Easy PHP：一个极速轻量级的PHP全栈框架](http://php.tigerb.cn/)
