<?php

namespace Imhonet\Connection\DataFormat;

interface IScalar extends IDataFormat
{
    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data);

    /**
     * @return array empty
     */
    public function formatData();

    /**
     * @return int|float|string|bool
     */
    public function formatValue();
}
