package main

//---------------
//叼叼的设计模式(Go享版)
//模板模式
//@auhtor TIGERB<https://github.com/TIGERB>
//---------------

type Context struct{

}

type BehaviorInterface interface{
	Check(ctx *Context) error
	GetNodeByRule(ctx *Context) error
	CheckJoinLimit(ctx *Context) error
	ConsumePoints(ctx *Context) error
}

type OrderPriceBehavior struct{}

func (lottery *Lottery) Check(ctx *Context) error {
	
}

func (lottery *Lottery) GetNodeByRule(ctx *Context) error {
	
}

func (lottery *Lottery) CheckJoinLimit(ctx *Context) error {
	
}

func (lottery *Lottery) GetRule(ctx *Context) error {
	
}


type Lottery struct{
	ConcreteBehavior BehaviorInterface
}

func (lottery *Lottery) Run(ctx *Context) error {
	lottery.Check();
	lottery.GetRule();
	lottery.GetNodeByRule();
	lottery.CheckTimes();
	lottery.CheckJoinLimit();
	lottery.ConsumePoints();
	lottery.GetPrize();
	lottery.Draw();
	lottery.PackagePrizeInfo();
}

func (lottery *Lottery) GetRule(ctx *Context) error {
	
}

func (lottery *Lottery) CheckTimes(ctx *Context) error {
	
}

func (lottery *Lottery) GetPrize(ctx *Context) error {
	
}

func (lottery *Lottery) Draw(ctx *Context) error {
	
}

func (lottery *Lottery) PackagePrizeInfo(ctx *Context) error {
	
}

func main()
	(&Lottery{
		ConcreteBehavior: OrderPriceBehavior{}
	}).Run(&Context{})
}