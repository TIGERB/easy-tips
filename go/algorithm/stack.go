package main

type stack struct {
	val []interface{}
}

func New() *stack {
	return &stack{val: []interface{}{}}
}

func (s *stack) Push(item interface{}) {
	s.val = append(s.val, item)
}

func (s *stack) Pop() interface{} {
	len := len(s.val)
	if len == 0 {
		return nil
	}
	item := s.val[len-1]
	s.val = append(s.val[:0], s.val[:len-1]...)
	return item
}

func (s *stack) Get() []interface{} {
	return s.val
}

func (s *stack) Len() int {
	return len(s.val)
}

// func main() {
// 	stack := New()
// 	stack.Push(1)
// 	stack.Push(2)
// 	stack.Push(3)
// 	fmt.Println(stack.Get(), stack.Len())
// 	stack.Push(6)
// 	stack.Push(9)
// 	fmt.Println(stack.Get(), stack.Len())
// 	fmt.Println(stack.Pop(), stack.Get(), stack.Len())
// }
