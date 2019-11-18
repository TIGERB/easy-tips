#include<stdio.h>
#include<string.h>
#include<stdlib.h>

int main(int argc, char **argv){
    // char name;
    // const char *lala = "shizhan learn c";
    // printf("please enter your name \n");
    // puts("shizhan");
    // printf("%s, %p, %c\n", "we", "are", *("space" + 1));
    // // scanf("%c", &name);
    // // printf("name length is %lu \n", strlen(&name));
    // // printf("hello %c \n", name);
    // while (*(lala) != '\0')
    //     putchar(*(lala++));
    char *po;
    po = malloc(10 * sizeof(char));
    printf("molloc point address %p %p", po, po+1);
    printf("aa %1s", &po[0]);
    return 0;
}