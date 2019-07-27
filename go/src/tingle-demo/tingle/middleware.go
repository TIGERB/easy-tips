package tingle

import "fmt"

// Handler 处理
type Handler interface {
	// 自身的业务
	Do(c *Context) error
	// 设置下一个对象
	SetNext(h Handler) Handler
	// 执行
	Run(c *Context)
}

// Next 抽象出来的 可被合成复用的结构体
type Next struct {
	// 下一个对象
	nextHandler Handler
}

// SetNext 实现好的 可被复用的SetNext方法
// 返回值是下一个对象 方便写成链式代码优雅
// 例如 nullHandler.SetNext(argumentsHandler).SetNext(signHandler).SetNext(frequentHandler)
func (n *Next) SetNext(h Handler) Handler {
	n.nextHandler = h
	return h
}

// Run 执行
func (n *Next) Run(c *Context) {
	// 由于go无继承的概念 这里无法执行当前handler的Do
	// n.Do(c)
	if n.nextHandler != nil {
		// 合成复用下的变种
		// 执行下一个handler的Do
		(n.nextHandler).Do(c)
		// 执行下一个handler的Run
		(n.nextHandler).Run(c)
	}
}

// NullHandler 空Handler
// 由于go无继承的概念 作为链式调用的第一个载体 设置实际的下一个对象
type NullHandler struct {
	// 合成复用Next的`nextHandler`成员属性、`SetNext`成员方法、`Run`成员方法
	Next
}

// Do 空Handler的Do
func (h *NullHandler) Do(c *Context) error {
	// 空Handler 这里什么也不做 只是载体 do nothing...
	return nil
}

type RecoverHandler struct {
	// 合成复用Next
	Next
}

// Do
func (h *RecoverHandler) Do(c *Context) error {
	fmt.Println("RecoverHandler...")
	return nil
}

// AccessLogHandler
type AccessLogHandler struct {
	// 合成复用Next
	Next
}

// Do
func (h *AccessLogHandler) Do(c *Context) error {
	fmt.Println("AccessLogHandler...")
	return nil
}
