<?php
/**
 * 符合psr-1,2的编程实例
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

namespace Standard; // 顶部命名空间
// 空一行
use Test\TestClass;//use引入类

/**
 * 类描述
 *
 * 类名必须大写开头驼峰.
 */
abstract class StandardExample // {}必须换行
{
  /**
   *  常量描述.
   *
   * @var string
   */
  const THIS_IS_A_CONST = ''; // 常量全部大写下划线分割

  /**
   * 属性描述.
   *
   * @var string
   */
  public $nameTest = ''; // 属性名称建议开头小写驼峰
                       // 成员属性必须添加public（不能省略）， private, protected修饰符

  /**
   * 属性描述.
   *
   * @var string
   */
  private $_privateNameTest = ''; // 类私有成员属性，【个人建议】下划线小写开头驼峰

  /**
   * 构造函数.
   *
   * 构造函数描述
   *
   * @param  string $value 形参名称/描述
   */
  public function __construct($value = '')// 成员方法必须添加public（不能省略）， private, protected修饰符
  {// {}必须换行

    $this->nameTest = new TestClass();

    // 链式操作
    $this->nameTest->functionOne()
                   ->functionTwo()
                   ->functionThree();

    // 一段代码逻辑执行完毕 换行
    // code...
  }

  /**
   * 成员方法名称.
   *
   * 成员方法描述
   *
   * @param  string $value 形参名称/描述
   *
   * @return 返回值类型        返回值描述
   * 返回值类型：string，array，object，mixed（多种，不确定的），void（无返回值）
   */
  public function testFunction($value = '')// 成员方法必须小写开头驼峰
  {
      // code...
  }

  /**
   * 成员方法名称.
   *
   * 成员方法描述
   *
   * @param  string $value 形参名称/描述
   *
   * @return 返回值类型        返回值描述
   */
  private function _privateTestFunction($value = '')// 私有成员方法【个人建议】下划线小写开头驼峰
  {
      // code...
  }

  /**
   * 成员方法名称.
   *
   * 成员方法描述
   *
   * @param  string $value 形参名称/描述
   *
   * @return 返回值类型        返回值描述
   */
  public static function staticFunction($value = '')// static位于修饰符之后
  {
    // code...
  }

  /**
   * 成员方法名称.
   *
   * 成员方法描述
   *
   * @param  string $value 形参名称/描述
   *
   * @return 返回值类型        返回值描述
   */
  abstract public function abstractFunction($value = ''); // abstract位于修饰符之前

  /**
   * 成员方法名称.
   *
   * 成员方法描述
   *
   * @param  string $value 形参名称/描述
   *
   * @return 返回值类型        返回值描述
   */
  final public function finalFunction($value = '')// final位于修饰符之前
  {
    // code...
  }

  /**
   * 成员方法名称.
   *
   * 成员方法描述
   *
   * @param  string $valueOne 形参名称/描述
   * @param  string $valueTwo 形参名称/描述
   * @param  string $valueThree 形参名称/描述
   * @param  string $valueFour 形参名称/描述
   * @param  string $valueFive 形参名称/描述
   * @param  string $valueSix 形参名称/描述
   *
   * @return 返回值类型        返回值描述
   */
  public function tooLangFunction(
    $valueOne   = '', // 变量命名可小写开头驼峰或者下划线命名,个人那习惯，据说下划线可读性好
    $valueTwo   = '',
    $valueThree = '',
    $valueFour  = '',
    $valueFive  = '',
    $valueSix   = '')// 参数过多换行
  {
    if ($valueOne === $valueTwo) {// 控制结构=>后加空格,同{一行，（右边和)左边不加空格
      // code...
    }

    switch ($valueThree) {
      case 'value':
        // code...
        break;

      default:
        // code...
        break;
    }

    do {
      // code...
    } while ($valueFour <= 10);

    while ($valueFive <= 10) {
      // code...
    }

    for ($i=0; $i < $valueSix; $i++) {
      // code...
    }
  }
}
