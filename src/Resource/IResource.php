<?php

namespace Imhonet\Connection\Resource;

interface IResource extends IConnect
{
    /**
     * @return mixed
     */
    public function getHandle();

    /**
     * @return mixed
     */
    public function disconnect();
}