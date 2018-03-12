# ssh多ssh-key管理(场景：在同一台开发机器上开发着公司的项目，又维护者自己的github开源项目，这时候为了安全和区分我们就需要在一台机器上维护多个ssh-key)

- step 1: ssh-keygen -t rsa -C "tigerbcode@gmail.com" -f ~/.ssh/github
- step 2: 创建config文件到~/.ssh

```demo
# github

Host github.com
Hostname github.com
User git
Identityfile ~/.ssh/github
```

- step 3: less ~/.ssh/github.pub (添加公钥)
- step 4: ssh -T github.com


