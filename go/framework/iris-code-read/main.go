package main

import (
	"fmt"

	"github.com/kataras/iris"
)

func main() {
	app := iris.Default()
	app.Get("/ping", func(ctx iris.Context) {
		fmt.Println("ping")
		ctx.JSON(iris.Map{
			"message": "pong",
		})
		ctx.Next()
	}, func(ctx iris.Context) {
		fmt.Println("ping2")
	})
	app.Run(iris.Addr(":8888"))
}
