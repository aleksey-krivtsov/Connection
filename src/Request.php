<?php

namespace Imhonet\Connection;

use Imhonet\Connection\DataFormat\IDataFormat;
use Imhonet\Connection\Query\Query as Query;
use Imhonet\Connection\Resource\IResource;

class Request
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var IResource
     */
    protected $resource;

    /**
     * @var IDataFormat
     */
    private $format;

    private $response = NAN;

    /**
     * @param Query $query
     * @param IDataFormat $format
     */
    public function __construct(Query $query, IDataFormat $format)
    {
        $this->query = $query;
        $this->format = $format;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $this->getResponse();

        return $this;
    }

    private function getResponse()
    {
        return !$this->hasResponse()
            ? $this->response = $this->query->execute()
            : $this->response;
    }

    private function hasResponse()
    {
        return !is_float($this->response) || !is_nan($this->response);
    }

    /**
     * @return array|\Traversable
     */
    public function getData()
    {
        return $this->getFormater()->formatData();
    }

    /**
     * @return float|int|null|string
     */
    public function getValue()
    {
        return $this->getFormater()->formatValue();
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->query->getErrorCode();
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->query->getCount();
    }

    /**
     * @return int|null
     */
    public function getLastId()
    {
        return $this->query->getLastId();
    }

    private function getFormater()
    {
        return $this->format->setData($this->getResponse());
    }

}
