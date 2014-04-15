<?php

namespace Imhonet\Connection\Query;

use Imhonet\Connection\Resource\IResource;

abstract class Query implements IQuery
{
    /**
     * @var \Exception|null
     */
    protected $error;

    /**
     * @var IResource
     */
    protected $resource;

    /**
     * @param IResource $resource
     * @return $this
     */
    public function setResource(IResource $resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getResource()
    {
        try {
            $resource = $this->resource->getHandle();
        } catch (\Exception $e) {
            $this->error = $e;
            throw $e;
        }

        return $resource;
    }

    /**
     * @return mixed
     */
    abstract public function execute();

    /**
     * @return int
     */
    abstract public function getErrorCode();

    /**
     * @return int
     */
    abstract public function getCountTotal();

    /**
     * @return int
     */
    abstract public function getCount();

    /**
     * @return int|null
     */
    abstract public function getLastId();

}
