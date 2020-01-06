<?php

namespace DivineOmega\FileSync\FileSyncStrategies;

use DivineOmega\CliProgressBar\ProgressBar;
use DivineOmega\FileSync\FileListing\FileListingFactory;
use DivineOmega\FileSync\FileListing\TransferActionFactory;
use DivineOmega\FileSync\Interfaces\FileSyncStrategyInterface;
use League\Flysystem\Filesystem;

class OneWay implements FileSyncStrategyInterface
{
    private $fromFilesystem;
    private $toFilesystem;
    private $showProgressBar = false;

    public function from(Filesystem $filesystem)
    {
        $this->fromFilesystem = $filesystem;
        return $this;
    }

    public function to(Filesystem $filesystem)
    {
        $this->toFilesystem = $filesystem;
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
            $maxProgress = 3;
            $progressBar = new ProgressBar();
            $progressBar->setMaxProgress($maxProgress);
            $progressBar->setMessage('Getting file listings...');
            $progressBar->display();
        }

        $fromFileListing = FileListingFactory::createFromFilesystem($this->fromFilesystem);

        if ($this->showProgressBar) {
            $progressBar->advance();
            $progressBar->display();
        }

        $toFileListing = FileListingFactory::createFromFilesystem($this->toFilesystem);

        if ($this->showProgressBar) {
            $progressBar->advance();
            $progressBar->setMessage('Determining differences...');
            $progressBar->display();
        }

        $files = $fromFileListing->getFilesToTransferTo($toFileListing);
        $transferActions = TransferActionFactory::createFromFiles($files, $this->fromFilesystem, $this->toFilesystem);

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