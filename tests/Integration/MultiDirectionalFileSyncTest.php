<?php

use DivineOmega\FileSync\FileSync;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

final class MultiDirectionalFileSyncTest extends TestCase
{
    public function setupDirectory($name)
    {
        $path = __DIR__.'/Data/'.$name.'/';

        if (is_dir($path)) {

            $files = glob($path.'*.txt');

            foreach($files as $file) {
                unlink($file);
            }
        } else {
            mkdir($path, 0777, true);
        }

        $adapter = new Local($path);
        return new Filesystem($adapter);
    }

    public function testMultiDirectionalFileSync()
    {
        $directoryA = $this->setupDirectory('a');
        $directoryB = $this->setupDirectory('b');
        $directoryC = $this->setupDirectory('c');

        (new FileSync())
            ->multiDirectional()
            ->with($directoryA)
            ->with($directoryB)
            ->with($directoryC)
            ->withProgressBar()
            ->begin();
    }
}
