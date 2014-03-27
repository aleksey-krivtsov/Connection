<?php

namespace Imhonet\Connection\DataFormat\Hash\PDO;

use Imhonet\Connection\DataFormat\IHash;

class Get implements IHash
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
        return $this->data ? $this->data->fetch(\PDO::FETCH_ASSOC) : null;
    }

    public function formatValue()
    {
    }

}
