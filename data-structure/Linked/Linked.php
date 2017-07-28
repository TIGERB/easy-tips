<?php
	
	/**
	 * 链表 
	 * 	优点:可以不需要规定长度,任意添加或删除节点
	 * 	缺点:想要获取链表中的某个值,就需要遍历链表,直到找出这个值
	 * 	这个只是在演示,如果在并发环境中使用可能要加锁
	 */
	
	class Node
	{
		private $value = null;

		private $next = null;

		public function __construct($value, $next = null)
		{
			$this->value = $value;
			$this->next = $next;
		}

		public function setNext($node)
		{
			$this->next = $node;
		}

		public function getNext()
		{
			return $this->next;
		}

		public function getValue()
		{
			return $this->value;
		}
	}

	class Linked implements Countable, Iterator
	{
		private $head = null;

		private $tail = null;

		private $current = null;

		private $length = 0;

		/**
		 * 添加节点
		 * @AuthorHTL neetdai
		 * @DateTime  2017-07-28T16:34:20+0800
		 * @param     mixed     
		 * 时间复杂度: O(1)              
		 */
		public function add($value)
		{
			if ($this->head === null) {
				$this->head = new Node($value);
				$this->tail = $this->head;
				$this->current = $this->head;
			}else{
				$node = new Node($value);
				$this->tail->setNext($node);
				$this->tail = $node;
			}
			$this->length++;
		}

		/**
		 * 删除指定位置的节点
		 * @AuthorHTL neetdai
		 * @DateTime  2017-07-28T16:38:21+0800
		 * @param     int                   $position 位置
		 * @return    bool   
		 * 时间复杂度: O(n)                          
		 */
		public function deletePosition($position)
		{
			if ($position > $this->length || $position <= 0) {
				return false;
			}

			$current = $this->head;

			$count = 1;

			//-------------------------------------
			//这一部分其实应该能写得更简单一些,可是我不懂得怎样写

			if ($position === 1) {
				$deleted = &$this->head;
				$this->head = $this->head->getNext();
				$this->length--;
				unset($deleted);
				return true;
			}

			$current = $this->head;
			for ($i=1; $i < $position - 1; $i++) { 
				$current = $current->getNext();
			}

			if ($current->getNext() === $this->tail) {
				$deleted = &$this->tail;
				$this->tail = $current;
				$this->tail->setNext(null);
				unset($deleted);
				$this->length--;
				return true;
			}

			$deleted = $current->getNext();
			$deleted = &$deleted;
			$current->setNext($deleted->getNext());
			unset($deleted);
			$this->length--;

			//-------------------------------------

			return true;
		}

		/**
		 * 返回链表的长度
		 * @AuthorHTL neetdai
		 * @DateTime  2017-07-27T18:38:32+0800
		 * @return    int
		 * 时间复杂度: O(1)         
		 */
		public function count()
		{
			return $this->length;
		}

		public function current()
		{
			return $this->current->getValue();
		}

		public function next()
		{
			$this->current = $this->current->getNext();
		}

		public function rewind()
		{
			$this->current = $this->head;
		}

		public function key()
		{
			return null;
		}

		public function valid()
		{
			return $this->current !== null;
		}
	}

	$l = new Linked();
	var_dump(count($l));
	$l->add(1);
	$l->add(2);
	$l->add(3);
	$l->add(4);
	var_dump(count($l));
	
	foreach ($l as $value) {
		var_dump($value);
	}
	
	$l->deletePosition(2);

	foreach ($l as $value) {
		var_dump($value);
	}

	$l->deletePosition(2);

	foreach ($l as $value) {
		var_dump($value);
	}

	$l->deletePosition(2);
	
	foreach ($l as $value) {
		var_dump($value);
	}

	$l->deletePosition(1);
