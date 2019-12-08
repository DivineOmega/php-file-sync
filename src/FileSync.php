<?php

namespace DivineOmega\FileSync;

use DivineOmega\FileSync\FileSyncStrategies\MultiDirectional;
use DivineOmega\FileSync\FileSyncStrategies\OneWay;

class FileSync
{
    public function oneWay()
    {
        return new OneWay();
    }

    public function multiDirectional()
    {
        return new MultiDirectional();
    }
}