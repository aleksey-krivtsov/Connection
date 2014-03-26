<?php

namespace Imhonet\Connection\Resource;

use Imhonet\Connection\Resource\PDO;

class Factory implements IConnect
{
    const TYPE_MYSQL = 'mysql';
    const TYPE_MYSQL_PDO = 'mysql_pdo';
    const TYPE_HANDLERSOCKET = 'handlersocket';
    const TYPE_RCMD = 'rcmd';
    const TYPE_MONGO = 'mongo';
    const TYPE_COUCHBASE = 'couchbase';
    const TYPE_MEMCACHE = 'memcache';
    const TYPE_MEMCACHED = 'memcached';

    private static $instance;
    private $resources = array();
    private $params = array();

    /**
     * @return self
     */
    static public function getInstance()
    {
        return self::$instance ? : self::$instance = new static;
    }

    /**
     * @inheritdoc
     */
    public function setHost($host)
    {
        $this->params['host'] = $host;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPort($port)
    {
        $this->params['port'] = $port;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setUser($user)
    {
        $this->params['user'] = $user;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPassword($password)
    {
        $this->params['password'] = $password;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDatabase($database)
    {
        $this->params['database'] = $database;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTable($table)
    {
        $this->params['table'] = $table;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setIndexName($name)
    {
        $this->params['index_name'] = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setIndexFields(array $fields)
    {
        $this->params['index_fields'] = $fields;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setIds($ids)
    {
        $this->params['ids'] = $ids;

        return $this;
    }

    /**
     * @param string $type self::TYPE_MYSQL|self::TYPE_HANDLERSOCKET|...
     * @param array $params
     * @return string
     */
    private function getResourceKey($type, $params)
    {
        return md5($type . json_encode($params));
    }

    /**
     * @param string $type self::TYPE_MYSQL|self::TYPE_HANDLERSOCKET|...
     * @throws \InvalidArgumentException
     * @return IResource
     */
    public function getResource($type)
    {
        $key = $this->getResourceKey($type, $this->params);

        if (!isset($this->resources[$key])) {
            $this->resources[$key] = $this->createResource($type);
        }

        $this->resetParams();

        return $this->resources[$key];
    }

    protected function createResource($type)
    {
        switch ($type) {
            case self::TYPE_MYSQL_PDO:
                $resource = $this->getMySQLPDO();
                break;
            case self::TYPE_COUCHBASE:
                $resource = $this->getCouchbase();
                break;
            case self::TYPE_MEMCACHE:
                $resource = $this->getMemcache();
                break;
            case self::TYPE_MEMCACHED:
                $resource = $this->getMemcached();
                break;
            default:
                throw new \InvalidArgumentException();
        }

        return $resource;
    }

    private function getMySQLPDO()
    {
        $resource = new PDO\MySQL();
        $resource->setHost($this->params['host'])
            ->setPort($this->params['port'])
            ->setUser($this->params['user'])
            ->setPassword($this->params['password'])
            ->setDatabase($this->params['database'])
        ;

        return $resource;
    }

    private function getCouchbase()
    {
        $resource = new Couchbase();
        $resource->setHost($this->params['host'])
            ->setPort($this->params['port'])
            ->setUser($this->params['user'])
            ->setPassword($this->params['password'])
            ->setDatabase($this->params['database'])
        ;

        return $resource;
    }

    private function getMemcached()
    {
        $resource = new Memcached();
        $resource->setHost($this->params['host'])
            ->setPort($this->params['port'])
        ;

        return $resource;
    }

    private function getMemcache()
    {
        $resource = new Memcache();
        $resource->setHost($this->params['host'])
            ->setPort($this->params['port'])
        ;

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->params['host'];
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->params['port'];
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->params['user'];
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->params['password'];
    }

    /**
     * @inheritdoc
     */
    public function getDatabase()
    {
        return $this->params['database'];
    }

    /**
     * @inheritdoc
     */
    public function getTable()
    {
        return $this->params['table'];
    }

    /**
     * @inheritdoc
     */
    public function getIndexName()
    {
        return $this->params['index_name'];
    }

    /**
     * @inheritdoc
     */
    public function getIndexFields()
    {
        return $this->params['index_fields'];
    }

    /**
     * @inheritdoc
     */
    public function getIds()
    {
        return $this->params['ids'];
    }

    private function resetParams()
    {
        $this->params = array();
    }

}
