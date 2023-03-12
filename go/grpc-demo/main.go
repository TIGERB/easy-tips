package main

import (
	"context"
	"flag"
	"fmt"
	"grpc-demo/demo"
	"net"
	"net/http"
	"time"

	"github.com/grpc-ecosystem/grpc-gateway/runtime"

	"google.golang.org/grpc"
)

// https://github.com/grpc/grpc-go/tree/master/examples/helloworld

// protoc \
//     --go_out=plugins=grpc:./ \
//     ./demo/demo.proto

// https://github.com/grpc-ecosystem/grpc-gateway

// http proto demo https://github.com/googleapis/googleapis/blob/master/google/api/http.proto#L46

// protoc \
//     -I ./demo \
//     -I /Users/tigerb/Documents/code/work/code/golang-mod/pkg/mod/github.com/grpc-ecosystem/grpc-gateway@v1.16.0/third_party/googleapis \
//     --go_out=plugins=grpc:./ \
//     --grpc-gateway_out=logtostderr=true:. \
//     ./demo/demo.proto

// protoc \
//     -I ./demo \
//     -I /Users/tigerb/Documents/code/work/code/golang-mod/pkg/mod/github.com/grpc-ecosystem/grpc-gateway@v1.16.0/third_party/googleapis \
//     --go_out=plugins=grpc:./ \
// 	   --php_out=plugins=grpc:./ \
//     --grpc-gateway_out=logtostderr=true:. \
//	   --plugin=protoc-gen-grpc=bins/opt/grpc_php_plugin \
//     ./demo/demo.proto

var (
	grpcServerEndpoint = flag.String("grpc-server-endpoint", "localhost:8888", "gRPC server endpoint")
)

type demoServer struct {
}

func (s *demoServer) SayHello(context.Context, *demo.HelloRequest) (reply *demo.HelloReply, err error) {
	fmt.Println("SayHello")
	time.Sleep(5 * time.Second)
	reply = &demo.HelloReply{
		Message: "hello",
	}
	return
}

func main() {
	go runGrpc()
	runHttp()
}

func runGrpc() {
	listener, err := net.Listen("tcp", ":8888")
	if err != nil {
		panic(err)
	}
	s := grpc.NewServer()
	demo.RegisterGreeterServer(s, &demoServer{})
	err = s.Serve(listener)
	if err != nil {
		panic(err)
	}
}

func runHttp() {
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()

	mux := runtime.NewServeMux()
	opts := []grpc.DialOption{
		grpc.WithInsecure(),
	}
	err := demo.RegisterGreeterHandlerFromEndpoint(ctx, mux, *grpcServerEndpoint, opts)
	if err != nil {
		panic(err)
	}
	http.ListenAndServe(":8889", mux)
}
