<?php

namespace DivineOmega\FileSync\FileListing;

abstract class FileListingFactory
{
    public static function createFromFilesystems(array $filesystems)
    {
        return array_map(function($filesystem) {
            return new FileListing($filesystem);
        }, $filesystems);
    }
}