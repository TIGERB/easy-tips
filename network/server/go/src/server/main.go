package main

import (
	"fmt"
	"net/http"
)

func demo(w http.ResponseWriter, r *http.Request) {
	fmt.Println("Hello World!")
	fmt.Fprintf(w, "Hello World!")
}

func main() {
	http.HandleFunc("/", demo)
	http.ListenAndServe("127.0.0.1:8879", nil)
}
