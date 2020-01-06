<?php

use DivineOmega\FileSync\FileSync;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

final class OneWayFileSystemTest extends TestCase
{
    public function setupDirectory($name, $fillWithFiles = true)
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

        if ($fillWithFiles)
        {
            $numFiles = rand(1, 100);

            $faker = Faker\Factory::create();

            $monthInSeconds = 2592000;

            for ($i=0; $i < $numFiles; $i++) {
                $filename = $faker->word().'.txt';
                $content  = $faker->text(rand(5, 1000));
                file_put_contents($path.$filename, $content);
                touch($path.$filename, time() - rand(0, $monthInSeconds));
            }
        }

        $adapter = new Local($path);
        return new Filesystem($adapter);
    }

    public function testMultiDirectionalFileSync()
    {
        $directoryA = $this->setupDirectory('oneway-a');
        $directoryB = $this->setupDirectory('oneway-b', false);

        (new FileSync())
            ->oneWay()
            ->from($directoryA)
            ->to($directoryB)
            ->begin();

        $filesA = glob(__DIR__.'/Data/oneway-a/*.txt');
        $filesB = glob(__DIR__.'/Data/oneway-b/*.txt');

        $this->assertSameSize($filesA, $filesB);

        foreach($filesA as $fileA) {
            $fileB = __DIR__.'/Data/oneway-b/'.basename($fileA);

            $this->assertFileExists($fileB);

            $this->assertFileEquals($fileA, $fileB);
        }
    }
}
