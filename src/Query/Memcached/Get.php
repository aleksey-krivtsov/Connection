<?php

namespace Imhonet\Connection\Query\Memcached;

use Imhonet\Connection\Query\Base;

class Get extends Base
{
    private $keys;

    /**
     * @var array
     */
    private $response;

    public function setKeys(array $keys)
    {
        $this->keys = $keys;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        return $this->getResponse();
    }

    private function getResponse()
    {
        if ($this->response === null) {
            $this->response = $this->getResource()->getMulti($this->keys);
        }

        return $this->response;
    }

    /**
     * @return \Memcached
     */
    private function getResource()
    {
        return $this->resource->getHandle();
    }

    private function isError()
    {
        return $this->getResponse() === false && $this->getResource()->getResultCode();
    }

    /**
     * @inheritdoc
     */
    public function getErrorCode()
    {
        return (int) $this->isError();
    }

    /**
     * @inheritdoc
     */
    public function getCountTotal()
    {
        return $this->getCount();
    }

    /**
     * @inheritdoc
     */
    public function getCount()
    {
        return sizeof($this->getResponse());
    }

    /**
     * @inheritdoc
     */
    public function getLastId()
    {
    }

}
