package main

import (
	"fmt"
)

// Context Context
type Context struct {
}

// Handler Handler
type Handler interface {
	Do(c *Context) error
	SetNext(h Handler) Handler
	Run(c *Context)
}

// Next Next
type Next struct {
	nextHandler Handler
}

// SetNext SetNext
func (n *Next) SetNext(h Handler) Handler {
	n.nextHandler = h
	return h
}

// Run Run
func (n *Next) Run(c *Context) {
	if n.nextHandler != nil {
		(n.nextHandler).Do(c)
		(n.nextHandler).Run(c)
	}
}

// NullHandler NullHandler·
type NullHandler struct {
	Next
}

// Do Do
func (h *NullHandler) Do(c *Context) error {
	// do nothing...
	return nil
}

// SignHandler SignHandler
type SignHandler struct {
	Next
}

// Do Do
func (h *SignHandler) Do(c *Context) error {
	fmt.Println("SignHandler")
	return nil
}

// ArgumentsHandler ArgumentsHandler
type ArgumentsHandler struct {
	Next
}

// Do Do
func (h *ArgumentsHandler) Do(c *Context) error {
	fmt.Println("ArgumentsHandler")
	return nil
}

// FrequentHandler FrequentHandler
type FrequentHandler struct {
	Next
}

// Do Do
func (h *FrequentHandler) Do(c *Context) error {
	fmt.Println("FrequentHandler")
	return nil
}

// LogHandler LogHandler
type LogHandler struct {
	Next
}

// Do Do
func (h *LogHandler) Do(c *Context) error {
	fmt.Println("LogHandler")
	return nil
}

func main() {
	nullHandler := &NullHandler{}
	argumentsHandler := &ArgumentsHandler{}
	signHandler := &SignHandler{}
	frequentHandler := &FrequentHandler{}

	nullHandler.SetNext(argumentsHandler).SetNext(signHandler).SetNext(frequentHandler)
	nullHandler.Run(&Context{})

	fmt.Println("----------------------")

	middlewares := make([]Handler, 0)
	middlewares = append(middlewares, nullHandler)
	middlewares = append(middlewares, argumentsHandler)
	middlewares = append(middlewares, signHandler)
	middlewares = append(middlewares, frequentHandler)

	for k, handler := range middlewares {
		if k == 0 {
			continue
		}
		middlewares[k-1].SetNext(handler)
	}
	nullHandler.Run(&Context{})
}
