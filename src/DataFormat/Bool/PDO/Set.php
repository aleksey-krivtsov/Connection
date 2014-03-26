<?php

namespace Imhonet\Connection\DataFormat\Bool\PDO;

use Imhonet\Connection\DataFormat\IBool;

class Set implements IBool
{
    /**
     * @var \PDOStatement
     */
    protected $data;

    /**
     * @inheritdoc
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function formatData()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function formatValue()
    {
        return (bool) $this->data->rowCount();
    }

}
