<?php

namespace DivineOmega\FileSync\FileListing;

use League\Flysystem\Filesystem;

class TransferAction
{
    private $path;
    private $sourceFilesystem;
    private $destinationFilesystem;

    public function __construct(string $path, Filesystem $sourceFilesystem, Filesystem $destinationFilesystem)
    {
        $this->path = $path;
        $this->sourceFilesystem = $sourceFilesystem;
        $this->destinationFilesystem = $destinationFilesystem;
    }

    public function transfer(): bool
    {
        $stream = $this->sourceFilesystem->readStream($this->path);
        return $this->destinationFilesystem->putStream($this->path, $stream);
    }
}