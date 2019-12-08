<?php

namespace DivineOmega\FileSync\FileSyncStrategies;

use DivineOmega\FileSync\FileListing\FileListing;
use DivineOmega\FileSync\FileListing\FileListingFactory;
use DivineOmega\FileSync\FileListing\TransferAction;
use DivineOmega\FileSync\FileListing\TransferActionFactory;
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

                $newTransferActions = TransferActionFactory::createFromFiles(
                    $files,
                    $fileListing->filesystem,
                    $otherFileListing->filesystem
                );

                $transferActions = array_merge($transferActions, $newTransferActions);

            }
        }

        foreach($transferActions as $transferAction) {
            $transferAction->transfer();
        }
    }
}