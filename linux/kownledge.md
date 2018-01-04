### 存储

- 寄存器(仅仅讨论cpu的寄存器)
    + 存在于cpu, 占用面积大，容量小，速度快
    + 计算机运算时必须将数据读入寄存器才能运算
    + 临时存储，断电后内容不存在
- 存储器
    + RAM(Random Access Memory): 随机存取存储器，易发挥性，即掉电失忆。
    + ROM(Read Only Memory): 只读存储器，一次写入多次读取，断电信息不丢失，如计算机启动的bios芯片。

## i++和++i

++i的性能优于i++, 原因如下:

```
i++相当于：
function i(&$arg = 0)
{
    $tmp = $arg; // 需要临时变量， 产生额外的消耗
    $arg += 1;
    return $tmp;
}

++i相当于：
function i($arg = 0)
{
    $arg += 1;
    return $arg;
}
```
