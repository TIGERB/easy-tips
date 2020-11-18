package algorithm

// Stack Stack
type Stack struct {
	val []interface{}
}

// New New
func New() *Stack {
	return &Stack{val: []interface{}{}}
}

// Push Push
func (s *Stack) Push(item interface{}) {
	s.val = append(s.val, item)
}

// Pop Pop
func (s *Stack) Pop() interface{} {
	len := len(s.val)
	if len == 0 {
		return nil
	}
	item := s.val[len-1]
	s.val = append(s.val[:0], s.val[:len-1]...)
	return item
}

// Get Get
func (s *Stack) Get() []interface{} {
	return s.val
}

// Len Len
func (s *Stack) Len() int {
	return len(s.val)
}

// func main() {
// 	Stack := New()
// 	Stack.Push(1)
// 	Stack.Push(2)
// 	Stack.Push(3)
// 	fmt.Println(Stack.Get(), Stack.Len())
// 	Stack.Push(6)
// 	Stack.Push(9)
// 	fmt.Println(Stack.Get(), Stack.Len())
// 	fmt.Println(Stack.Pop(), Stack.Get(), Stack.Len())
// }
