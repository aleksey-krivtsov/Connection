<?php

namespace Imhonet\Connection\DataFormat\Scalar\PDO;


use Imhonet\Connection\DataFormat\IScalar;

class Get implements IScalar
{
    /**
     * @var \PDOStatement
     */
    private $data;

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function formatData()
    {
        return array();
    }

    public function formatValue()
    {
        return $this->data ? $this->data->fetchColumn() : null;
    }

}
