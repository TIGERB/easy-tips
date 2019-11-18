### 面向对象的设计过程

---

# 前言

我一直认为分享的目的不是炫技。

- 一是，自我学习的总结。
- 二是，降低他人的学习成本。
- 三是，别人对自己学习结果的审核。

同时，我也不希望我一直在给大家讲我知道什么。二是有下面四个要素：

观点|本次分享的观点是一个软件工程中的思维方法，不限于编程语言
-|-
**探讨**|**我可能理解错的，或者大家没理解的，大家可以随时打断我，尽可能多互动，目的增加理解**
**理解**|**希望讲完了，大家可以理解**
**运用**|**最重要的，如果对大家有帮助，希望大家一定要去在实际业务中实战**

# 背景

工作中，几乎大家经常抱怨别人写的代码：

- 没法改
- 耦合高 
- 无法扩展

> 今天就来探讨如何**克服**上面的问题～

# 场景

首先问个问题：

> 平常工作中来了一个业务需求，我们是如何开始写代码的？

我推测大多数人可能：

- 1、梳理业务
- 2、设计数据库、接口、缓存
- 3、评审
- 4、于是就开始了 `怎么怎么样...如果怎么怎么样...怎么怎么样...`愉快的码代码的过程

> 此处有人觉着有啥问题么？

```
备注：说出来问题的，本次分享就可以略过了，哈哈哈。
```

### 随便看一个简单的业务场景

```
比如刘超凡一天的生活，简单来看看他一天干些啥：

1.0 饿了吃饭
1.1 到点吃饭

2.0 渴了喝水
2.1 到点喝水

3.0 困了睡觉
3.1 到点睡觉
3.2 有可能一个睡觉，也有可能... 是吧？复杂
```


```php
// 一个业务逻辑从头写到尾
function doSomething()
{
    // 该吃饭了...
    
    // 该喝水了...

    // 该睡觉了...
}
```

```php
// 一个业务逻辑(拆成多个函数)从头写到尾

function eat()
{
    // 吃饭...
}

function drink()
{
    // 喝水...
}

function goBed()
{
    // 睡觉...
}

function doSomething()
{
    // 该吃饭了...
    eat();

    // 该喝水了...
    drink();
    
    // 该睡觉了...
    goBed();
}
```

```php
// 一个业务逻辑(引入类)从头写到尾

class DemoPeople
{
    public function doSomething()
    {
        // 该吃饭了...
        
        // 该喝水了...

        // 该睡觉了...
    }
}
```

```php
// 一个业务逻辑(拆成多个类方法)从头写到尾
// 也许、可能、貌似、猜测大多数人停留到了这个阶段
// 问题：某一天多了社交的能力，咋办？

class DemoPeople
{
    public function doSomething()
    {
        // 该吃饭了...
        $this->eat();

        // 该喝水了...
        $this->drink();
    
        // 该睡觉了...
        $this->goBed();        
    }

    private function eat()
    {
        // 吃饭...
    }

    private function drink()
    {
        // 喝水...
    }

    private function goBed()
    {
        // 睡觉...
    }
}
```

```php
// 一个业务逻辑(拆成多类)从头写到尾
// 进化
// 引入逻辑层

class DemoPeople
{
    public function doSomething()
    {
        // 该吃饭了...
        (new DemoEat)->eat();
        
        // 该喝水了...
        (new DemoDrink)->drink();
        
        // 该睡觉了...
        (new DemoSleep)->goBed();
        
    }
}

class DemoEat
{
    public function eat()
    {
        // 吃饭...
    }
}

class DemoDrink
{
    public function drink()
    {
        // 喝水...
    }
}

class DemoSleep
{
    private function goBed()
    {
        // 睡觉...
    }
}
```

```php
// 一个业务逻辑(拆成类、抽象类、接口)从头写到尾
// 出现了抽象类、接口

interface BehaviorInterface
{
    public function do();
}

class DemoEat implements BehaviorInterface 
{
    public function do()
    {
        // 吃饭...
    }
}

class DemoDrink implements BehaviorInterface 
{
    public function do()
    {
        // 喝水...
    }
}

class DemoSleep implements BehaviorInterface 
{
    public function do()
    {
        // 睡觉...
    }
}

class DemoPeople
{
    // 状态
    private $state = '';

    public function doSomething()
    {
        $this->makeDecision()->do();
    }

    // 决策
    private function makeDecision()
    {
        switch ($this->state) {
            case 'hunger':
                return new DemoEat;
                break;
            case 'thirsty':
                return new DemoDrink;
                break;
            case 'tired':
                return new DemoSleep;
                break;
            
            default:
                throw new Exception('not support', 500);
                break;
        }
    }
}
```

> 思考🤔：上面的代码就没啥问题了吗？

```php
// 其他：我喜欢的写法

class DemoPeople
{
    // 状态
    private $state = '';

    // 策略map
    private $stateStrategyMap = [
        'hunger'  => 'DemoEat',
        'thirsty' => 'DemoDrink',
        'tired'   => 'DemoSleep',
    ];

    public function doSomething()
    {
        (new $stateStrategyMap[$this->state])->do();
    }
}
```

上面就是面向对象设计的代码结果。

> 所以，如何设计出完全面向对象的代码？

# 代码建模

> 什么是代码建模？

把业务抽象成事物(类class、抽象类abstact class)和行为(接口interface)的过程。

### 实栗🌰分析

又来看一个实际的业务场景：
```
最近刘超凡开始创业了，刚创立了一家电商公司，B2C，自营书籍《3分钟学会交际》。最近开始写提交订单的代码。

⚠️注意场景 1.刚创业 2.简单的单体应用 3.此处不探讨架构
```

一般来说，我们根据业务需求一顿分析，开始定义接口API、设计数据库、缓存、技术评审扽等就开始码代码了。

```
接口参数：
uid
address_id
coupon_id
.etc

业务逻辑：
参数校验->
地址校验->
其他校验->
写订单表->
写订单商品信息表->
写日志->
扣减商品库存->
清理购物车->
扣减各种促销优惠活动的库存->
使用优惠券->
其他营销逻辑等等->
发送消息->
等等...
```

就开始写代码了`怎么怎么样...如果怎么怎么样...怎么怎么样...`一蹴而就、思路清晰、逻辑清楚、很快搞定完代码，很优秀是不是，值得鼓励。

但是，上面的结果就是大概所有人都见过的连续上千行的代码等等。上面的流程没啥问题啊，那正确的做法是什么了？就是接着要说的**代码建模**。

我们根据上面的场景，开始建模。

### 业务分析少不了

同样，首先，我们看看`提交订单`这个业务场景要做的事情:

>换个角度看业务其实很简单：根据用户相关信息生成一个订单。

1. 梳理得到业务逻辑
```
参数校验->
地址校验->
其他校验->
写订单表->
写订单商品信息表->
写日志->
扣减商品库存->
清理购物车->
扣减各种促销优惠活动的库存->
使用优惠券->
其他营销逻辑等等->
发送消息->
等等...
```

2. 梳理业务逻辑依赖信息
```
用户信息
商品信息
地址信息
优惠券信息
等等...
```

再次回归概念

> 什么是代码建模？把业务抽象成事物(类class、抽象类abstact class)和行为(接口interface)的过程。

### 获取事物

我们把订单生成的过程可以想象成`机器人`，一个生成订单的`订单生成机器人`，或者订单生成机器啥的，这样我们就得到了`代码建模`过程中的一个事物。

从而我们就可以把这个事物转化成一个类(或结构体)，或者抽象类。

<img src="http://cdn.tigerb.cn/20191020223812.jpg" width="100%">

### 获取行为

这些操作就是上面机器人要做的事情。

事物有了：`订单生成机器人`
行为呢？毫无义务就是上面各种业务逻辑。把具体的行为抽象成一个订单创建行为接口：

<img src="http://cdn.tigerb.cn/20191020224230.jpg" width="100%">

### UML

<img src="http://cdn.tigerb.cn/20191020233121.png" width="100%">

### 设计代码

1. 定义一个类
```php
// 一个具体的 创建订单的机器人🤖️
class RobotOrderCreate
{
    // 用户信息
    private $infoUser = [];

    // 商品信息
    private $infoProduct = [];

    // 地址信息
    private $infoAddress = [];

    // 优惠券信息
    private $infoCoupon = [];

    // 等等属性

    /**
     * 构造函数
     * 根据请求参数、购物车数据等，初始化机器人的属性
     * 
     * @param $request Request 请求数据对象
     */
    public function __construct(Request $request)
    {
        // 根据请求参数、购物车数据 初始化机器人的属性
    }

    /**
     * 创建订单
     */
    public function create()
    {
        // 创建订单的逻辑...
    }
}
```

2. 定义一个订单创建行为的接口
```php
interface BehaviorOrderCreateInterface
{
    public function do(RobotOrderCreate $robot);
}
```

3. 定义具体的不同订单创建行为类
```
参数校验->
地址校验->
其他校验->
写订单表->
写订单商品信息表->
写日志->
扣减商品库存->
清理购物车->
扣减各种促销优惠活动的库存->
使用优惠券->
其他营销逻辑等等->
发送消息->
等等...
```

```php
class Param implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 参数校验...
    }
}

class Address implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 地址校验...
    }
}

class Check implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 其他校验...
    }
}

class Order implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 写订单表...
    }
}

class OrderItem implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 写订单商品信息表...
    }
}

class Log implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 写订单商品信息表...
    }
}

// 等等... 不再赘述
```

```php
/**
* 创建订单
* 
* @param $request Request 请求数据对象
*/
public function create(Request $request)
{
    // 这里的代码该怎么写？
    // 这样？
    try {
        (new Param())->do($this);
        (new Address())->do($this);
        (new Check())->do($this);
        (new Order())->do($this);
        (new OrderItem())->do($this);
        (new Log())->do($this);
        // 等等...
    } catch (Exception $e) {

    }
}
```

```php
/**
* 创建订单
* 
* @param $request Request 请求数据对象
* @param $behaviorList array 行为对象列表
*/
public function create(Request $request, $behaviorList = array())
{
    // 还可以继续优化吗？
    try {
        foreach ($behaviorList as $behavior){
            $behavior->do($this);
        }
        // 等等...
    } catch (Exception $e) {

    }
}
```

```php
/**
* 创建订单
* 
* @param $request Request 请求数据对象
* @param $behaviorList array 行为闭包列表
*/
public function create(Request $request, $behaviorList = array())
{
    // 这样 使用闭包
    try {
        foreach ($behaviorList as $behavior){
            // 闭包
            $behavior()->do($this);
        }
        // 等等...
    } catch (Exception $e) {

    }
}
```

### 完整代码

```php
/**
 * 《面向对象的设计过程》
 * -------------------
 * PHP版本完整代码
 * @authtor shizhan<tigerbcode@gmail.com>
 */

interface BehaviorOrderCreateInterface
{
    public function do(RobotOrderCreate $robot);
}

class Param implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 参数校验...
    }
}

class Address implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 地址校验等...
    }
}

class Check implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 其他校验...
    }
}

class Order implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 写订单表等...
    }
}

class OrderItem implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 写订单商品信息表等...
    }
}

class Log implements BehaviorOrderCreateInterface 
{
    public function do(RobotOrderCreate $robot)
    {
        // 日志...
    }
}

// 等等行为... 不再赘述

/* ------分界线------ */

// 一个具体的 创建订单的机器人🤖️
class RobotOrderCreate
{
    // 用户信息
    private $infoUser = [];

    // 商品信息
    private $infoProduct = [];

    // 地址信息
    private $infoAddress = [];

    // 优惠券信息
    private $infoCoupon = [];

    // 行为闭包列表
    private $behaviorList = [];

    // 等等属性

    /**
     * 构造函数
     * 
     * @param $request Request 请求数据对象
     */
    public function __construct(Request $request)
    {
        // 根据请求参数、购物车数据 初始化机器人的属性
        # code ...
    }

    /**
    * 注册行为
    * 
    * @param $behavior Closure 行为闭包列表
    */
    public function registerBehavior(Closure ...$behavior)
    {
        array_merge($this->behaviorList, $behavior);
    }

    /**
    * 创建订单
    */
    public function create()
    {
        // 这样 使用闭包
        try {
            foreach ($this->behaviorList as $behavior){
                // 闭包
                // 对象类型校验略
                $behavior()->do($this);
            }
            // 等等...
        } catch (Exception $e) {

        }
    }
}

/* ------分界线------ */

// 具体使用
(new RobotOrderCreate($request))->registerBehavior(
    function () {
        return new Param();
    },
    function () {
        return new Address();
    },
    function () {
        return new Check();
    },
    function () {
        return new Order();
    },
    function () {
        return new OrderItem();
    },
    function () {
        return new Log();
    },
    // 等等...
)->create();
```

```go
/**
 * 《面向对象的设计过程》
 * -------------------
 * Go版本完整代码
 * @authtor shizhan<tigerbcode@gmail.com>
 */

// BehaviorOrderCreateInterface 定义一个订单创建行为的接口
type BehaviorOrderCreateInterface interface {
	// 自身的业务
	Do(robot *RobotOrderCreate) error
}

// Param 参数校验
type Param struct {
}

// Do 参数校验
func (behavior *Param) Do(robot *RobotOrderCreate) error {
	// code...
	return nil
}

// Address 地址校验等
type Address struct {
}

// Do 地址校验等
func (behavior *Address) Do(robot *RobotOrderCreate) error {
	// code...
	return nil
}

// Check 其他校验
type Check struct {
}

// Do 其他校验
func (behavior *Check) Do(robot *RobotOrderCreate) error {
	// code...
	return nil
}

// Order 写订单表等
type Order struct {
}

// Do 写订单表等
func (behavior *Order) Do(robot *RobotOrderCreate) error {
	// code...
	return nil
}

// OrderItem 写订单商品信息表等
type OrderItem struct {
}

// Do 写订单商品信息表等
func (behavior *OrderItem) Do(robot *RobotOrderCreate) error {
	// code...
	return nil
}

// Log 日志
type Log struct {
}

// Do 日志
func (behavior *Log) Do(robot *RobotOrderCreate) error {
	// code...
	return nil
}

// 等等行为... 不再赘述

// Request 请求数据对象
type Request struct {
}

// RobotOrderCreate 一个具体的 创建订单的机器人
type RobotOrderCreate struct {
	InfoUser     interface{}
	InfoProduct  interface{}
	InfoAddress  interface{}
	InfoCoupon   interface{}
	behaviorList []BehaviorOrderCreateInterface
}

// Init 根据请求参数、购物车数据 初始化机器人的属性
func (robot *RobotOrderCreate) Init(r *Request) *RobotOrderCreate {
	// code ...
	return robot
}

// RegisterBehavior 注册行为
// @param $behavior Closure 行为闭包列表
func (robot *RobotOrderCreate) RegisterBehavior(behavior ...BehaviorOrderCreateInterface) *RobotOrderCreate {
	robot.behaviorList = append(robot.behaviorList, behavior...)
	return robot
}

// Create 创建订单
func (robot *RobotOrderCreate) Create() error {
	// code ...
	for _, behavior := range robot.behaviorList {
		behavior.Do(robot)
	}
	return nil
}

func main() {
	// 具体使用
	(&RobotOrderCreate{}).Init(&Request{}).
		RegisterBehavior(
			&Param{},
			&Address{},
			&Check{},
			&Order{},
			&OrderItem{},
			&Address{},
			&Log{},
		).
		Create()
}

```

> 上面的代码有什么好处？

假如刘超凡又要新开发一个新的应用，新的应用创建订单的时候又有新的逻辑，比如没有优惠逻辑、新增了增加用户积分的逻辑等等，复用上面的代码，是不是就很简单了。

```php
(new RobotOrderCreate())->registerBehavior(
    // 等等...
    // 不注册优惠相关行为
    // ...
    // 新增用户积分行为
    function () {
        return new UserPoint();
    },
    // 等等...
)->create($request);
```

> 所以现在，什么是面向对象？

### 面向对象的设计原则

- 对接口编程，不要对实现编程
- 使用对象之间的组合，减少对继承的使用
- 抽象用于不同的事物，而接口用于事物的行为

针对上面的概念，我们再回头开我们上面的代码

> 对接口编程，不要对实现编程

```
结果：RobotOrderCreate依赖了BehaviorOrderCreateInterface抽象接口
```

> 使用对象之间的组合，减少对继承的使用

```
结果：完全没有使用继承，多个行为不同场景组合使用
```

> 抽象用于不同的事物，而接口用于事物的行为

```
结果：
1. 抽象了一个创建订单的机器人 RobotOrderCreate
2. 机器人又有不同的创建行为
3. 机器人的创建行为最终依赖于BehaviorOrderCreateInterface接口
```

是不是完美契合，所以这就是“面向对象的设计过程”。

### 结论

`代码建模过程就是“面向对象的设计过程”的具体实现方式.`

### 设计模式

> 最后，设计模式又是什么？

同样，我们下结合上面的场景和概念预习下设计模式。

##### 设计模式的设计原则

> 开闭原则：对扩展开放，对修改封闭

看看上面的最终的代码是不是完美契合。
```php
// 订单创建类RobotOrderCreate不可修改
// 订单创建类支持外部扩展新业务
(new RobotOrderCreate())->registerBehavior(
    // 等等...
    function () {
        return new class() implements BehaviorOrderCreateInterface {
            // 新增的业务
            public function do(RobotOrderCreate $robot)
            {
                // 新增的业务逻辑...
            }
        };
    },
    // 等等...
)->create($request);
```

> 依赖倒转：对接口编程，依赖于抽象而不依赖于具体
  
```
结果：创建订单的逻辑从依赖具体的业务转变为依赖于抽象接口BehaviorOrderCreateInterface
```

- 接口隔离：使用多个接口，而不是对一个接口编程，去依赖降低耦合
```
结果：上面的场景，我们只简单定义了订单创建的接口BehaviorOrderCreateInterface。由于订单创建过程可能出现异常回滚，我们就需要再定义一个订单创建回滚的接口
BehaviorOrderCreateRollBackInterface。
```  

> 最少知道：减少内部依赖，尽可能的独立
```
结果：还是上面那段代码，我们把RobotOrderCreate机器人依赖的行为通过外部注入的方式使用。
```
  
> 合成复用：多个独立的实体合成聚合，而不是使用继承
```
结果：RobotOrderCreate依赖了多个实际的订单创建行为类。
```
  
> 里氏代换：超类（父类）出现的地方，派生类（子类）都可以出现
```
结果：不好意思，我们完全没用继承。（备注：继承容易造成父类膨胀。）
```

# 最后

上面预习了设计模式的概念，下次我们进行《设计模式业务实战》。
  
