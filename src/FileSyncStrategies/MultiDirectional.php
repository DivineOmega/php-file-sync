<?php

namespace DivineOmega\FileSync;

use DivineOmega\FileSync\FileListing\FileListing;
use DivineOmega\FileSync\FileListing\FileListingFactory;
use DivineOmega\FileSync\FileListing\TransferAction;
use DivineOmega\FileSync\Interfaces\FileSyncStrategyInterface;
use League\Flysystem\Filesystem;

class MultiDirectional implements FileSyncStrategyInterface
{
    private $filesystems = [];
    private $showProgressBar = false;

    public function with(Filesystem $filesystem): self
    {
        $this->filesystems[] = $filesystem;
        return $this;
    }

    public function withProgressBar(): self
    {
        $this->showProgressBar = true;
        return $this;
    }

    public function begin(): void
    {
        $transferActions = [];

        $fileListings = FileListingFactory::createFromFilesystems($this->filesystems);

        foreach($fileListings as $key => $fileListing) {

            $otherFileListings = $fileListings;
            unset($otherFileListings[$key]);

            foreach($otherFileListings as $otherFileListing) {

                $files = $fileListing->getFilesToTransferTo($otherFileListing);

                foreach ($files as $file) {

                    $transferActions[] = new TransferAction(
                        $file->path,
                        $fileListing->filesystem,
                        $otherFileListing->filesystem
                    );
                }

            }
        }

        foreach($transferActions as $transferAction) {
            $transferAction->transfer();
        }
    }
}