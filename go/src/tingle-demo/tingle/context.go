package tingle

import "net/http"

// Context 上下文
type Context struct {
	Request  *http.Request
	Response http.ResponseWriter
	Method   string
	Path     string
}

// JSON json输出
func (context *Context) JSON(content string) {
	context.Response.Header().Set("Content-Type", "Application/json")
	context.Response.Header().Set("Charset", "utf-8")
	context.Response.WriteHeader(200)
	context.Response.Write([]byte(content))
}
