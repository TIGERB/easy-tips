package test

import (
	"context"
	"grpc-demo/demo"
	"testing"
	"time"

	"google.golang.org/grpc"
)

func TestHello(t *testing.T) {

	connection, err := grpc.Dial(":8888", grpc.WithInsecure(), grpc.WithBlock())
	if err != nil {
		t.Error(err)
		return
	}
	defer connection.Close()
	client := demo.NewGreeterClient(connection)
	ctx, cancel := context.WithTimeout(context.Background(), time.Second)
	defer cancel()
	resp, err := client.SayHello(ctx, &demo.HelloRequest{
		Name: "aaa",
	})
	if err != nil {
		t.Error(err)
		return
	}
	t.Log(resp.GetMessage())
}
