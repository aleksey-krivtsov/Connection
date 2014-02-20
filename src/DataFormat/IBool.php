<?php

namespace Imhonet\Connection\DataFormat;

interface IBool extends IDataFormat
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
     * @return bool
     */
    public function formatValue();
}
