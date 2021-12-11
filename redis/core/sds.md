## sds结构(C实现)

```
struct sdshdr {
    // 字符串长度 不包含末尾空字符\0
    int len;
    // buffer
    int free;
    // 字符串
    char buf[];
};
```