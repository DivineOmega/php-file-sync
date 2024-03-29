<?php

namespace DivineOmega\FileSync\FileListing;

use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListWith;

class FileListing
{
    public $filesystem;
    public $files = [];

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        $filesystem->addPlugin(new ListWith());
        $listing = $filesystem->listWith(['timestamp'], null, true);

        $this->files = [];

        foreach ($listing as $object) {
            if ($object['type'] !== 'file') {
                continue;
            }

            $this->files[] = new File($object['path'], $object['timestamp']);
        }
    }

    public function getFileByPath(string $path)
    {
        foreach($this->files as $file) {
            if ($file->path === $path) {
                return $file;
            }
        }

        return null;
    }

    public function getFilesToTransferTo(FileListing $otherFileListing): array
    {
        $filesToTransfer = [];

        foreach($this->files as $file) {

            $otherFile = $otherFileListing->getFileByPath($file->path);

            if (!$otherFile || $file->isNewerThan($otherFile)) {
                $filesToTransfer[] = $file;
            }

        }

        return $filesToTransfer;
    }

}