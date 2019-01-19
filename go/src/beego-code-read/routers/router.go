package routers

import (
	"beego-code-read/controllers"
	"fmt"

	"github.com/astaxie/beego"
)

func init() {
	fmt.Println("aaaaaa 4")
	beego.Router("/", &controllers.MainController{})
}
