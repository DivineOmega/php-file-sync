<?php

namespace DivineOmega\FileSync;

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