package main

import (
	"context"
	"flag"
	"grpc-demo/demo"
	"net"
	"net/http"

	"google.golang.org/grpc"

	grpcprometheus "github.com/grpc-ecosystem/go-grpc-prometheus"
	"github.com/prometheus/client_golang/prometheus/promhttp"
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
	grpcServerEndpoint = flag.String("grpc-server-endpoint", "localhost:1010", "gRPC server endpoint")
)

type demoServer struct {
}

func (s *demoServer) SayHello(context.Context, *demo.HelloRequest) (reply *demo.HelloReply, err error) {
	reply = &demo.HelloReply{
		Message: "hello test",
	}
	return
}

func main() {
	go runGrpc()
	runHttp()
}

func runGrpc() {
	listener, err := net.Listen("tcp", ":1010")
	if err != nil {
		panic(err)
	}
	s := grpc.NewServer(
		grpc.StreamInterceptor(grpcprometheus.StreamServerInterceptor),
		grpc.UnaryInterceptor(grpcprometheus.UnaryServerInterceptor),
	)
	demo.RegisterGreeterServer(s, &demoServer{})
	grpcprometheus.Register(s)
	err = s.Serve(listener)
	if err != nil {
		panic(err)
	}
}

func runHttp() {
	// ctx, cancel := context.WithCancel(context.Background())
	// defer cancel()

	// mux := runtime.NewServeMux()
	// opts := []grpc.DialOption{
	// 	grpc.WithInsecure(),
	// }
	// err := demo.RegisterGreeterHandlerFromEndpoint(ctx, mux, *grpcServerEndpoint, opts)
	// if err != nil {
	// 	panic(err)
	// }
	http.Handle("/metrics", promhttp.Handler())
	// http.ListenAndServe(":1011", mux)
	http.ListenAndServe(":1011", nil)
}
