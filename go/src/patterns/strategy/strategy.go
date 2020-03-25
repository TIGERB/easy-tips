package main

//------------------------------------------------------------
//我的代码没有`else`系列
//策略模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

import "fmt"

// SmsServiceInterface SmsServiceInterface
type SmsServiceInterface interface {
	Send() error
}

// ServiceProviderOne ServiceProviderOne
type ServiceProviderOne struct {
}

// Send Send
func (s *ServiceProviderOne) Send() error {
	fmt.Println("one")
	return nil
}

// ServiceProviderTwo ServiceProviderTwo
type ServiceProviderTwo struct {
}

// Send Send
func (s *ServiceProviderTwo) Send() error {
	fmt.Println("two")
	return nil
}

// Factory Factory
type Factory struct {
	Type string
}

// GetServiceProvider GetServiceProvider
func (f *Factory) GetServiceProvider() SmsServiceInterface {
	switch f.Type {
	case "one":
		return &ServiceProviderOne{}
	case "two":
		return &ServiceProviderTwo{}
	}
	return nil
}

// Context Context
type Context struct {
	strategy SmsServiceInterface
}

// SendSms SendSms
func (c *Context) SendSms() error {
	return c.strategy.Send()
}

func main() {
	(&Context{
		strategy: (&Factory{
			Type: "one",
		}).GetServiceProvider(),
	}).SendSms()
}
