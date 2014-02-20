<?php

namespace Imhonet\Connection\Query\Memcached;

use Imhonet\Connection\Query\Base;
use Respect\Validation\Validator;

class Set extends Base
{
    private $data = array();
    private $expire = 0;

    /**
     * @var bool
     */
    private $response;

    /**
     * @param array $data [key => value, ...]
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param int $value
     */
    public function setExpire($value)
    {
        assert(Validator::create()->digit()->noWhitespace()->validate($value));

        $this->expire = $value;
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
            $this->response = $this->getResource()->setMulti($this->data, $this->expire);
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
        return $this->isError() ? 0 : sizeof($this->data);
    }

    /**
     * @inheritdoc
     */
    public function getLastId()
    {
    }

}
