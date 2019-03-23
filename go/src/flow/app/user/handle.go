package user

import (
	"encoding/json"
	"net/http"
)

type Response struct {
	Code   int         `json:"code"`
	Msg    string      `json:"msg"`
	Result interface{} `json:"msg"`
}

type DemoHandle struct {
}

func (DemoHandle) ServeHTTP(rw http.ResponseWriter, r *http.Request) {
	rw.Header().Add("Content-Type", "application/json")
	resp, _ := json.Marshal(Response{
		Code: 200,
		Msg:  "OK",
	})
	rw.Write(resp)
}
