<?php

namespace DivineOmega\FileSync\FileListing;

use League\Flysystem\Filesystem;

abstract class FileListingFactory
{
    public static function createFromFilesystems(array $filesystems)
    {
        return array_map(function(FileSystem $filesystem) {
            return self::createFromFilesystem($filesystem);
        }, $filesystems);
    }

    public static function createFromFilesystem(Filesystem $filesystem)
    {
        return new FileListing($filesystem);
    }
}