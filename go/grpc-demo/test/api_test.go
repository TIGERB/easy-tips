package test

import (
	"context"
	"fmt"
	"grpc-demo/demo"
	"testing"
	"time"

	"google.golang.org/grpc"
)

func ClientTimeoutInterceptor(
	ctx context.Context,
	method string,
	req,
	reply interface{},
	cc *grpc.ClientConn,
	invoker grpc.UnaryInvoker,
	opts ...grpc.CallOption,
) error {
	ctxWithTimeout, cancel := context.WithTimeout(ctx, 1*time.Second)
	defer cancel()
	err := invoker(ctxWithTimeout, method, req, reply, cc, opts...)
	fmt.Println("ClientTimeoutInterceptor")
	return err
}

func TestHello(t *testing.T) {

	connection, err := grpc.Dial(":8888", grpc.WithInsecure(), grpc.WithBlock(), grpc.WithUnaryInterceptor(ClientTimeoutInterceptor))
	if err != nil {
		t.Error(err)
		return
	}
	defer connection.Close()
	client := demo.NewGreeterClient(connection)
	// ctx, cancel := context.WithTimeout(context.Background(), time.Second)
	// defer cancel()
	resp, err := client.SayHello(context.Background(), &demo.HelloRequest{
		Name: "aaa",
	})
	if err != nil {
		t.Error(err)
		return
	}
	t.Log(resp.GetMessage())
}
