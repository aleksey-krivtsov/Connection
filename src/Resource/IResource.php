<?php

namespace Imhonet\Connection\Resource;

interface IResource extends IConnect
{
    /**
     * @return mixed
     * @throws \Exception
     */
    public function getHandle();

    /**
     * @return mixed
     */
    public function disconnect();
}
