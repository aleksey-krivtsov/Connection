<?php

namespace Imhonet\Connection\Query;

use Imhonet\Connection\Resource\IResource;

abstract class Base
{
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