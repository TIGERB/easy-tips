<?php
/**
 * redis 连接数据库类
 */

class RedisConnect
{
    /**
     * Redis的ip
     *
     * @var string
     */
    const REDISHOSTNAME = "127.0.0.1";

    /**
     * Redis的port
     *
     * @var int
     */
    const REDISPORT = 6379;

    /**
     * Redis的超时时间
     *
     * @var int
     */
    const REDISTIMEOUT = 0;

    /**
     * Redis的password
     *
     * @var unknown_type
     */
    const REDISPASSWORD = "auth";

    /**
     * Redis的DBname
     *
     * @var int
     */
    const REDISDBNAME = 12;

    /**
     * 类单例
     *
     * @var object
     */
    private static $instance;

    /**
     * Redis的连接句柄
     *
     * @var object
     */
    private $redis;

    /**
     * RedisConnect constructor.
     */
    private function __construct ()
    {
        // 链接数据库
        $this->redis = new Redis();
        $this->redis->connect(self::REDISHOSTNAME, self::REDISPORT, self::REDISTIMEOUT);
        $this->redis->auth(self::REDISPASSWORD);
        $this->redis->select(self::REDISDBNAME);
    }

    /**
     * 私有化克隆函数，防止类外克隆对象
     */
    private function __clone ()
    {}

    /**
     * 类的唯一公开静态方法，获取类单例的唯一入口
     *
     * @return object
     */
    public static function getRedisInstance ()
    {
        if (! (self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取redis的连接实例
     *
     * @return Redis
     */
    public function getRedisConn ()
    {
        return $this->redis;
    }

    /**
     * 需要在单例切换的时候做清理工作
     */
    public function __destruct ()
    {
        self::$instance->redis->close();
        self::$instance = NULL;
    }
}
