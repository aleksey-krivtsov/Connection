<?php

namespace Imhonet\Connection\Query\Sphinx;


use Imhonet\Connection\Query\Query;

class Get extends Query
{
    const FILTER_VALUES = 'values';
    const FILTER_EXCLUDE = 'exclude';

    private $index;
    private $search;
    private $limit;
    private $offset = 0;
    private $sort_field;
    private $sort_order = \SORT_ASC;
    private $field_weights;
    private $filter;
    private $match_mode;
    private $is_escape = true;

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
     * @param array $field_weights
     * @return $this
     */
    public function setFieldWeights(array $field_weights)
    {
        $this->field_weights = $field_weights;

        return $this;
    }

    /**
     * @param $attribute
     * @param $values
     * @param bool $exclude
     * @return $this
     */
    public function addFilter($attribute, $values, $exclude = false)
    {
        $this->filter[$attribute] = [
            self::FILTER_VALUES => $values,
            self::FILTER_EXCLUDE => $exclude
        ];

        return $this;
    }

    /**
     * @param $match_mode
     * @return $this
     */
    public function setMatchMode($match_mode)
    {
        $this->match_mode = $match_mode;

        return $this;
    }

    /**
     * @return int
     */
    public function getMatchMode()
    {
        return $this->match_mode == \SORT_REGULAR ? \SPH_MATCH_EXTENDED : \SPH_MATCH_ALL;
    }

    /**
     * @param bool $is_escape
     * @return $this
     */
    public function setEscape($is_escape = true)
    {
        $this->is_escape = (bool) $is_escape;

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

                if ($this->field_weights) {
                    $sph->SetFieldWeights($this->field_weights);
                }

                if ($this->filter) {
                    foreach($this->filter as $attr => $data) {
                        $sph->SetFilter($attr, $data[self::FILTER_VALUES], $data[self::FILTER_EXCLUDE]);
                    }
                }

                if ($this->match_mode) {
                    $sph->SetMatchMode($this->getMatchMode());
                }

                if ($this->is_escape) {
                    $this->search = $this->prepareQuery($this->search);
                }

                $sph->addQuery($this->search, $this->index);


                $this->response = $sph->runQueries();
                $this->success = $this->response !== false;
            }
        }

        return $this->response;
    }

    /**
     * @param string $query
     * @return string
     */
    protected function prepareQuery($query)
    {
        $sph = $this->getResource();

        if (isset($sph)) {
            $query = htmlspecialchars_decode(stripslashes($query));
            $query = strtolower(trim(str_replace('"', '', $query)));
            //@TODO проверить: в sphinxapi похоже бага с эскейпингом слеша
            $query = str_replace('/', ' ', $query);
        }

        return $sph->EscapeString($query);
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

    /**
     * @return int
     */
    private function getSortOrder()
    {
        if ($this->sort_order != \SORT_REGULAR) {
            return $this->sort_order == \SORT_DESC ? \SPH_SORT_ATTR_DESC : \SPH_SORT_ATTR_ASC;
        } else {
            return \SPH_SORT_EXPR;
        }
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
