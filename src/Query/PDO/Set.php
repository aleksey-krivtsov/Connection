<?php

namespace Imhonet\Connection\Query\PDO;


class Set extends PDO
{
    private $last_id;

    protected function getResponse()
    {
        $response = parent::getResponse();
        $this->getResourceLastInsertId();

        return $response;
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
        return $this->getResponse()->rowCount();
    }

    private function getResourceLastInsertId()
    {
        return $this->last_id === null ? $this->last_id = (int) $this->getResource()->lastInsertId() : $this->last_id;
    }

    /**
     * @inheritdoc
     */
    public function getLastId()
    {
        $this->execute();

        return $this->getResourceLastInsertId();
    }

}
