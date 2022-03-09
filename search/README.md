# 搜索概念

- Elasticsearch: 权威指南 https://www.elastic.co/guide/cn/elasticsearch/guide/current/index.html
- 倒排索引 https://www.elastic.co/guide/cn/elasticsearch/guide/current/inverted-index.html#inverted-index
- 正排索引与倒排索引 
- 倒排与列存 https://juejin.cn/post/6844903502704017415
- 分面搜索（Faceted Search）https://cdc.tencent.com/2009/07/30/%E5%88%86%E9%9D%A2%E6%90%9C%E7%B4%A2%EF%BC%88faceted-search%EF%BC%89/
- 一文带你了解搜索功能设计 https://zhuanlan.zhihu.com/p/268050957
- 电商搜索如何让你买得又快又好
    + 「概述」(一) https://zhuanlan.zhihu.com/p/50634256
    + 「搜索前」(二) https://zhuanlan.zhihu.com/p/50792323
    + 「搜索中」(三) https://zhuanlan.zhihu.com/p/50919931
- 运营经理应该懂的搜索知识（5）-搜索人工干预 http://www.woshipm.com/operate/64946.html
- 电商搜索排序：粗排 https://zhuanlan.zhihu.com/p/422164662
- 电商搜索排序：精排 https://zhuanlan.zhihu.com/p/431806763
- 电商搜索排序：召回 https://zhuanlan.zhihu.com/p/395626828

## 搜索搭建
- Install Elasticsearch with Dockeredit https://www.elastic.co/guide/en/elasticsearch/reference/current/docker.html
- 从零搭建 ES 搜索服务
    + 基础搜索 https://symonlin.github.io/2018/12/27/elasticsearch-2/

## 相关性的判断

tf-idf
Term Frequence - Inverse Document Frequence
单文本词频 - 逆文档频率

## 《Elasticsearch: 权威指南》记录

- 索引
    + 动词：存储数据到ES的过程称之为「索引」
    + 名词：存储数据的地方
- Luence索引
- 类型
- 文档
- 属性

```

- 分析器(1个)
    + 字符过滤器(1个)
    + 分词器(1个)
    + 分词过滤器(N个)

分析器类型：
1. 标准分析器：单词边界拆分(unicode)
2. 简单分析器：按不是字母的地方拆分
3. 空格分析器：按空格拆分
4. 语言分析器：按语言语法规则拆分
5. 自定义分析器

全文域？
精确值域？

什么时候使用「分析器」？
1. 索引文档的时候(建立倒排索引的时候)，使用分析器处理被索引的文档
2. 查询的时候，使用分析器处理「查询字符串」

                                                                                                属性1
                                                                                        文档1 --->
                                                                                                属性2
                                                                                                ...
                                                                                                属性N
                                            类型1(type) ---> 映射(属性类型、属性分析器类型) --->
                                                                                        文档2 --->
                                                                                        ...
                                                                                        文档N --->
                            ---> 索引1(index) ---> 
                                            类型2 ---> 映射 ---> ...
                                            ...
                                            类型N ---> 映射 ---> ...

                            ---> 索引2 ---> ...
ES集群 ---> 节点(node) ---> 分片(shard)
                            ---> 索引3 ---> ...

                                  ...

                            ---> 索引N ---> ...
```

```
在分布式系统中深度分页

理解为什么深度分页是有问题的，我们可以假设在一个有 5 个主分片的索引中搜索。 当我们请求结果的第一页（结果从 1 到 10 ），每一个分片产生前 10 的结果，并且返回给 协调节点 ，协调节点对 50 个结果排序得到全部结果的前 10 个。

现在假设我们请求第 1000 页—​结果从 10001 到 10010 。所有都以相同的方式工作除了每个分片不得不产生前10010个结果以外。 然后协调节点对全部 50050 个结果排序最后丢弃掉这些结果中的 50040 个结果。

可以看到，在分布式系统中，对结果排序的成本随分页的深度成指数上升。这就是 web 搜索引擎对任何查询都不要返回超过 1000 个结果的原因。
```

### 分析器

分词和标准化的过程称为 分析
分析器 analyser https://www.elastic.co/guide/cn/elasticsearch/guide/current/analysis-intro.html

1. 一块文本拆分成独立词条(term)，text -> terms
2. 标准化这些独立词条

- 字符过滤器：格式化为标准文本(text -> standard text)，例如去掉文本中的html标签
- 分词器：文本块拆为独立词条(text -> terms)
- 分词过滤器：
    + 统一转小写
    + 同义词、近义词转换
    + 停用词
    + 提取词干
    + 纠错
    + 自动补全
    + 等等...

```
字符过滤器：格式化为标准文本(text -> standard text)，例如去掉文本中的html标签
 
分词器：文本块拆为独立词条(text -> terms)
 
分词过滤器：统一转小写、同义词、近义词转换、停用词、提取词干、纠错、自动补全、等等...
```

### 倒排索引 https://www.elastic.co/guide/cn/elasticsearch/guide/current/inverted-index.html#inverted-index

```
小写化
词干提取
同义词

全文
精确值

过滤器filters

相关性 Tf-idf https://www.elastic.co/guide/cn/elasticsearch/guide/current/relevance-intro.html
词频

```

```
Tf-idf

Tf-idf中的tf全称为Term Frequence,指的是词频，是指该词在某文本的占比。Tf越高，说明该词在文本中越重要。

Idf全称为Inverse Document Frequence，指的是逆文档频率。在说idf前先介绍下df，df是文档频率，是将包含该Term的文档数除以总文档数。比如一个Term在10个文档出现，总共有50个文档，那么df值为10/50（1/5）。

讲完df后，我们再聊回idf，还是上面的例子，那么idf值为log（50/10）。由公式可以看出，idf越高，说明有该Term的文本越少，那么该文本越就能代表该Term。

同时用log来表示，还能处理掉一些高频词对文本相关性的干扰。比如“的”“了”，这种高频词的Tf可能很高，但Idf会很小，接近于0，两数值相乘后也会很小，能很好的排除这些高频词的噪音。

对于较为简单的文本相关性排序，相关性的分值可以用Tf*idf来表示，分值越高，说明文本相关性越高。
```

### ES数据同步方式

- 阿里巴巴 MySQL binlog 增量订阅&消费组件 
    + https://github.com/alibaba/canal
    + MySQL如何实时同步数据到ES？试试这款阿里开源的神器！ https://juejin.cn/post/6891435372824395784
- go-mysql-elasticsearch 
    + https://github.com/go-mysql-org/go-mysql-elasticsearch
    + 使用 go-elasticsearch 实时同步 MySQL 数据到 ES https://cloud.tencent.com/document/product/845/35562

# 推荐

- ctr(Click Through Rate) 点击通过率
- 协同过滤：CF（Collaborative Filteing


- 舍弃 Python，为什么知乎选用 Go 重构推荐系统？https://www.infoq.cn/article/ur32plxfwkeydg*zle0f
- 推荐系统--召回 https://zhuanlan.zhihu.com/p/107715284


# 大数据

```
Hadoop是包含计算框架MapReduce分布式文件系统HDFS。 Spark是MapReduce的替代方案，而且兼容HDFS、Hive等分布式存储系统，可融入Hadoop生态。 MapReduce的计算引擎将中间结果存储在磁盘上，进行存储和容错。
```

- 一种通用的数据仓库分层方法 https://cloud.tencent.com/developer/article/1396891
- 漫谈数据仓库之维度建模 https://zhuanlan.zhihu.com/p/27426819
- hadoop和大数据的关系？和spark的关系？ https://www.zhihu.com/question/23036370
- 对比Pig、Hive和SQL，浅看大数据工具之间的差异 https://cloud.tencent.com/developer/article/1042007

- ETL，是英文Extract-Transform-Load的缩写 抽取-传输-载入

数据分层：
```
DM层(Data Market数据集市): 更细致具体到某个业务的数据
^
|
|
DW层(Data Warehose数据仓库): 汇总数据
    DW细分层：
        ---> DWS层(Service): 汇总多个表数据的宽表
        ---> DWM层(Middle): 中间表
        ---> DWD层(Detail): 细颗粒度数据
^
|
|
ODS层(Operation Data Source): 源数据或者经过简单清洗的数据
```

# 推荐

- CF 协同过滤
    + UserCF 基于用户相似度的过滤
    + ItemCF 基于物品相似度的过滤

# 云市场调研

SaaS：
- IaaS，PaaS，SaaS 的区别 https://www.ruanyifeng.com/blog/2017/07/iaas-paas-saas.html
- PaaS（平台即服务）https://www.ibm.com/cn-zh/cloud/learn/paas
- 多租户技术是什么？怎么实现多租户？https://juejin.cn/post/7002609579087364103
- 一文带您了解软件多租户技术架构 https://mp.weixin.qq.com/s/HaXlKWVQ9uijnAx5ej61kw#at
- 数据库中的Schema是什么? https://blog.csdn.net/u010429286/article/details/79022484
- 多租户架构系统架构：SaaS管理与PaaS平台的不同关键点 https://cloud.tencent.com/developer/article/1889603
- 一文搞懂SaaS困境、API经济与Serverless WebAssembly https://mp.weixin.qq.com/s?__biz=MzAxOTY5MDMxNA==&mid=2455759241&idx=1&sn=ad486d10a00e0cf063e20a028e441a28&

扩展点：
- 有赞
    + 电商云应用框架 https://mp.weixin.qq.com/s?__biz=MzAxOTY5MDMxNA==&mid=2455759241&idx=1&sn=ad486d10a00e0cf063e20a028e441a28&chksm=8c686facbb1fe6bab4a951c0a9adc44626d7e78b61ceb66d1c4f19838c992e4c98616fbea0a4&scene=21#wechat_redirect
    + 扩展点文档 https://doc.youzanyun.com/detail/EXT/4
    + 有赞云如何支持多语言 https://segmentfault.com/a/1190000021890970
    + 有赞权限系统(SAM) https://tech.youzan.com/sam/
    + PHP 应用代码说明 https://doc.youzanyun.com/resource/develop-guide/41356/49286/66170
- 微盟
    + 后端扩展点说明 https://cloud.weimob.com/saas/word/detail.html?tag=1090&menuId=2
- 扩展点的设计 https://juejin.cn/post/6844904146248663047
- 扩展点设计 https://www.jianshu.com/p/a20e1793f6d9
- https://github.com/asim/go-micro
- MOSN 扩展机制解析 https://mosn.io/docs/concept/extensions/
- shopify
    + https://code006.myshopify.com/admin/apps
    + https://apps.shopify.com/collections/works-with-shopify-marketing?locale=zh-CN

> 可以看到独立数据库模式资源利用率低，但是数据隔离性最好；而完全共享模式下资源利用率高，但是数据隔离性最弱。因此具体采用哪种模式仍然需要根据实际租户的需求来进行灵活创建和配置，一个灵活的SaaS应用实际需要同时灵活支撑上面三种模式。
> 比如我们开发一个SaaS云服务的CRM系统。这个系统部署在公有云端可以开放给多个企业客户使用。那么我们就遇到了一个关键问题。即是否当新入驻一个新的企业客户的时候，我们都需要重新在部署一套应用给这个客户使用？


1. 独立DB
2. 共享DB，独立Schema
3. 共享DB，共享Schema

# 业务配置中心

- 从 0 到 1, 构建达达快送自动化业务配置中心 https://www.modb.pro/db/126069

# 触达系统

- 闲鱼触达系统背后——我想更懂你 https://developer.aliyun.com/article/752445
- 触达系统进阶篇（一）：自动化消息 http://www.woshipm.com/pd/4633109.html
- 用户运营：触达系统应该如何搭建 http://www.woshipm.com/user-research/4239618.html
- 智能运营新功能，多波次营销全触达 https://juejin.cn/post/7028855072683638792
- 从 0 到 1 搭建技术中台之推送平台实践 ：https://xie.infoq.cn/article/cbec479489a00c23d6575635f 
- 揭秘一点资讯推送系统：https://juejin.cn/post/6981994002912378910