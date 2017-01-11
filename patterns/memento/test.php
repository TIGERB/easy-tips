<?php
/**
 * 行为型模式
 *
 * php备忘录模式
 * 理解：就是外部存储对象的状态，以提供后退/恢复/复原
 * 使用场景：编辑器后退操作/数据库事物/存档
 *
 * 下面我们来实现编辑器的undo(撤销)/redo（重置）功能
 *
 * @author  TIGERB <https://github.com/TIGERB>
 * @example 运行 php test.php
 */


// 注册自加载
spl_autoload_register('autoload');

function autoload($class)
{
  require dirname($_SERVER['SCRIPT_FILENAME']) . '//..//' . str_replace('\\', '/', $class) . '.php';
}

/************************************* test *************************************/

use memento\Editor;

try {
  // 初始化一个编辑器并新建一个空文件
  $editor = new Editor('');

  // 写入一段文本
  $editor->write('hello php !');
  // 保存
  $editor->save();
  // 修改刚才的文本
  $editor->write(' no code no life !');
  // 撤销
  $editor->undo();
  $editor->read();
  // 再次修改并保存文本
  $editor->write(' life is a struggle !');
  $editor->save();
  // 重置
  $editor->redo();
  $editor->read();

} catch (\Exception $e) {
  echo 'error:' . $e->getMessage();
}
