<?php

namespace Imhonet\Connection\Query\Couchbase;

use Imhonet\Connection\Query\Base;

class Get extends Base
{
    private $ids;

    /**
     * @var \CouchbaseException|null
     */
    private $error;

    /**
     * @var array|float
     */
    private $response = NAN;

    public function setIds(array $ids)
    {
        $this->ids = $ids;
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
        if (!$this->hasResponse()) {
            /** @type $handle \Couchbase */
            $handle = $this->resource->getHandle();

            try {
                $this->response = $handle->getMulti($this->ids);
            } catch (\CouchbaseException $e) {
                $this->response = null;
                $this->error = $e;
            }
        }

        return $this->response;
    }

    private function hasResponse()
    {
        return !is_float($this->response) || !is_nan($this->response);
    }

    private function isError()
    {
        return $this->getResponse() === null;
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
        return null;
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
