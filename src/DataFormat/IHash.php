<?php

namespace Imhonet\Connection\DataFormat;

interface IHash
{
    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data);

    /**
     * @return array assoc
     */
    public function formatData();

    /**
     * @return null
     */
    public function formatValue();
}
