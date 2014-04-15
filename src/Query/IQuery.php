<?php

namespace Imhonet\Connection\Query;

use Imhonet\Connection\Resource\IResource;

interface IQuery
{
    /**
     * @param IResource $resource
     * @return self
     */
    public function setResource(IResource $resource);

    /**
     * @return mixed
     */
    public function execute();

    /**
     * @return int
     */
    public function getErrorCode();

    /**
     * @return int
     */
    public function getCountTotal();

    /**
     * @return int
     */
    public function getCount();

    /**
     * @return int|null
     */
    public function getLastId();
}