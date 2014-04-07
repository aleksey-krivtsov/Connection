<?php

namespace Imhonet\Connection\Query\Sphinx;


use Imhonet\Connection\Query\Query;

class Get extends Query
{
    private $index;
    private $search;
    private $limit;
    private $offset = 0;
    private $sort_field;
    private $sort_order = \SORT_ASC;

    /**
     * @var array|bool
     */
    private $response;
    /**
     * @var bool|null
     */
    private $success;

    /**
     * @param string $name
     * @return self
     */
    public function addIndex($name)
    {
        $this->index = $name;

        return $this;
    }

    /**
     * @param string $str
     * @return self
     */
    public function setSearchString($str)
    {
        $this->search = $str;

        return $this;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return self
     */
    public function setLimit($limit, $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param string $field
     * @param int $order
     * @return self
     */
    public function setOrder($field, $order = \SORT_ASC)
    {
        $this->sort_field = $field;
        $this->sort_order = $order;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        return $this->getResponse();
    }

    protected function getResponse()
    {
        if (!$this->hasResponse()) {
            try {
                $sph = $this->getResource();
            } catch (\Exception $e) {
                $this->success = false;
                $this->response = false;
            }

            if (isset($sph)) {
                if ($this->sort_field) {
                    $sph->SetSortMode($this->getSortOrder(), $this->sort_field);
                }

                if ($this->limit) {
                    $sph->SetLimits($this->offset, $this->limit);
                }

                $sph->addQuery($sph->EscapeString($this->search), $this->index);

                $this->response = $sph->runQueries();
                $this->success = $this->response !== false;
            }
        }

        return $this->response;
    }

    private function hasResponse()
    {
        return $this->success !== null;
    }

    /**
     * @inheritdoc
     * @return \SphinxClient
     */
    protected function getResource()
    {
        return parent::getResource();
    }

    private function getSortOrder()
    {
        return $this->sort_order == \SORT_DESC ? \SPH_SORT_ATTR_DESC : \SPH_SORT_ATTR_ASC;
    }

    /**
     * @return bool
     */
    private function isError()
    {
        return $this->getResponse() === false || $this->getResponse()[0]['status'] != 0;
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
        return $this->isError() ? null : $this->getResponse()[0]['total'];
    }

    /**
     * @inheritdoc
     */
    public function getCount()
    {
        return $this->isError() ? null : sizeof($this->getResponse()[0]['matches']);
    }

    /**
     * @inheritdoc
     */
    public function getLastId()
    {
    }
}
