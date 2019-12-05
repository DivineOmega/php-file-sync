<?php

namespace DivineOmega\FileSync\FileListing;

use League\Flysystem\Filesystem;

abstract class TransferActionFactory
{
    public static function createFromFiles(array $files, Filesystem $sourceFilesystem, Filesystem $destinationFilesystem)
    {
        return array_map(function ($file) use ($sourceFilesystem, $destinationFilesystem) {
            return new TransferAction(
                $file->path,
                $sourceFilesystem,
                $destinationFilesystem
            );
        }, $files);
    }
}