# 搜索概念
- 倒排索引 https://www.elastic.co/guide/cn/elasticsearch/guide/current/inverted-index.html#inverted-index
- 正排索引与倒排索引 
- 倒排与列存 https://juejin.cn/post/6844903502704017415
- 分面搜索（Faceted Search）https://cdc.tencent.com/2009/07/30/%E5%88%86%E9%9D%A2%E6%90%9C%E7%B4%A2%EF%BC%88faceted-search%EF%BC%89/
- 一文带你了解搜索功能设计 https://zhuanlan.zhihu.com/p/268050957
- 电商搜索如何让你买得又快又好
    + 「概述」(一) https://zhuanlan.zhihu.com/p/50634256
    + 「搜索前」(二) https://zhuanlan.zhihu.com/p/50792323
    + 「搜索中」(三) https://zhuanlan.zhihu.com/p/50919931

# 搜索搭建
- Install Elasticsearch with Dockeredit https://www.elastic.co/guide/en/elasticsearch/reference/current/docker.html
- 从零搭建 ES 搜索服务
    + 基础搜索 https://symonlin.github.io/2018/12/27/elasticsearch-2/

## 相关性的判断

tf-idf
Term Frequence - Inverse Document Frequence
单文本词频 - 逆文档频率

# 《Elasticsearch: 权威指南》记录

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
                                        类型1 ---> 映射(属性类型、属性分析器类型) --->
                                                                                文档2 --->
                                                                                ...
                                                                                文档N --->
                        ---> 索引1 ---> 
                                        类型2 ---> 映射 ---> ...
                                        ...
                                        类型N ---> 映射 ---> ...

                        ---> 索引2 ---> ...
ES集群 ---> node ---> 分片
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

### 倒排索引 https://www.elastic.co/guide/cn/elasticsearch/guide/current/inverted-index.html#inverted-index

```
小写化
词干提取
同义词

分词和标准化的过程称为 分析
分析器 analyser
https://www.elastic.co/guide/cn/elasticsearch/guide/current/analysis-intro.html


词条
词干
terms tokens

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



# 大数据

```
Hadoop是包含计算框架MapReduce分布式文件系统HDFS。 Spark是MapReduce的替代方案，而且兼容HDFS、Hive等分布式存储系统，可融入Hadoop生态。 MapReduce的计算引擎将中间结果存储在磁盘上，进行存储和容错。
```

- 一种通用的数据仓库分层方法 https://cloud.tencent.com/developer/article/1396891
- 漫谈数据仓库之维度建模 https://zhuanlan.zhihu.com/p/27426819
- hadoop和大数据的关系？和spark的关系？ https://www.zhihu.com/question/23036370
- 对比Pig、Hive和SQL，浅看大数据工具之间的差异 https://cloud.tencent.com/developer/article/1042007

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

# 云市场调研

- 电商云应用框架 https://mp.weixin.qq.com/s?__biz=MzAxOTY5MDMxNA==&mid=2455759241&idx=1&sn=ad486d10a00e0cf063e20a028e441a28&chksm=8c686facbb1fe6bab4a951c0a9adc44626d7e78b61ceb66d1c4f19838c992e4c98616fbea0a4&scene=21#wechat_redirect
- 有赞扩展点文档 https://doc.youzanyun.com/detail/EXT/4
- 微盟后端扩展点说明 https://cloud.weimob.com/saas/word/detail.html?tag=1090&menuId=2
- 扩展点的设计 https://juejin.cn/post/6844904146248663047
- 扩展点设计 https://www.jianshu.com/p/a20e1793f6d9
- https://github.com/asim/go-micro
- MOSN 扩展机制解析 https://mosn.io/docs/concept/extensions/
- 有赞云如何支持多语言 https://segmentfault.com/a/1190000021890970