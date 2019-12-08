<?php

namespace DivineOmega\FileSync\FileListing;

use Carbon\Carbon;
use Exception;

class File
{
    public $path;
    public $timestamp;

    /**
     * File constructor.
     *
     * @param string $path
     * @param string $timestamp
     * @throws Exception
     */
    public function __construct(string $path, string $timestamp)
    {
        $this->path = $path;
        $this->timestamp = Carbon::createFromTimestamp($timestamp);
    }

    public function isNewerThan(File $file)
    {
        return $this->timestamp > $file->timestamp;
    }
}