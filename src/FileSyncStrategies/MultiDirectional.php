<?php

namespace DivineOmega\FileSync\FileSyncStrategies;

use DivineOmega\CliProgressBar\ProgressBar;
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
        if ($this->showProgressBar) {
            $maxProgress = 1;
            $progressBar = new ProgressBar();
            $progressBar->setMaxProgress(1);
            $progressBar->setMessage('Getting file listings...');
            $progressBar->display();
        }

        $fileListings = FileListingFactory::createFromFilesystems($this->filesystems);

        if ($this->showProgressBar) {
            $progressBar->advance();
            $maxProgress += count($fileListings);
            $progressBar->setMaxProgress($maxProgress);
            $progressBar->setMessage('Determining differences...');
            $progressBar->display();
        }

        $transferActions = [];

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

            if ($this->showProgressBar) {
                $progressBar->advance();
                $progressBar->display();
            }
        }

        if ($this->showProgressBar) {
            $maxProgress += count($transferActions);
            $progressBar->setMaxProgress($maxProgress);
            $progressBar->setMessage('Transferring files...');
            $progressBar->display();
        }

        foreach($transferActions as $transferAction) {
            $transferAction->transfer();

            if ($this->showProgressBar) {
                $progressBar->advance();
                $progressBar->display();
            }
        }

        if ($this->showProgressBar) {
            $progressBar->complete();
        }
    }
}