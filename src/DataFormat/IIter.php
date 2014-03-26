<?php

namespace Imhonet\Connection\DataFormat;

interface IIter extends IDataFormat
{
    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data);

    /**
     * @return \Traversable
     */
    public function formatData();

    /**
     * @return null
     */
    public function formatValue();
}
