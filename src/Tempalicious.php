<?php

/**
 * A robust tempfile implementation supporting:
 * * File Extensions
 * * Automatic cleanup
 *
 * 1) Uses a semaphore file to ensure no collissions (php seems to re-issue tmp files of same name if deleted)
 * 2) Automatically cleans up temp files when the request exits (since you won't know when the caller is done w/file)
 */
class Tempalicious
{
    protected $inited               = false;
    protected $tmpDir               = NULL;
    protected $extension            = NULL;
    protected $tmpFile              = NULL;
    protected $tmpFileSemaphore     = NULL;

    public function __construct()
    {
        $this->setTmpDir(sys_get_temp_dir());
    }

    public function setExtension($extension)
    {
        $cleanedExtension = ltrim(trim($extension), '.');
        if ($cleanedExtension === '')
        {
            $this->extension = NULL;
        }
        else
        {
            $this->extension = $cleanedExtension;
        }

        return $this;
    }

    public function setTmpDir($dir)
    {
        $this->tmpDir = $dir;

        return $this;
    }

    /**
     * idempotent
     */
    private function ensureTempFile()
    {
        if ($this->inited) return;
        $this->inited = true;

        register_shutdown_function(array($this, 'cleanup'));

        if ($this->extension)
        {
            // provision semaphore file
            $this->tmpFileSemaphore = tempnam($this->tmpDir, "Tempalicous_");
            if ($this->tmpFileSemaphore === false) throw new Exception("Couldn't create temp file semaphore {$this->tmpFileSemaphore}");

            // create actual temp file
            $this->tmpFile = "{$this->tmpFileSemaphore}.{$this->extension}";
            $ok = touch($this->tmpFile);
            if (!$ok) throw new Exception("Unable to touch {$this->tmpFile}");
        }
        else
        {
            // create actual temp file
            $this->tmpFile = tempnam($this->tmpDir, "Tempalicous_");
            if ($this->tmpFile === false) throw new Exception("Couldn't create temp file {$this->tmpFile}");
        }
    }

    public function cleanup()
    {
        if ($this->tmpFile && file_exists($this->tmpFile))
        {
            unlink($this->tmpFile);
        }

        // clean semaphore file last to avoid weird race condition where PHP will re-issue a recently used tmpfile
        if ($this->tmpFileSemaphore && file_exists($this->tmpFileSemaphore))
        {
            unlink($this->tmpFileSemaphore);
        }
    }

    public function getTempfilePath()
    {
        $this->ensureTempFile();

        return $this->tmpFile;
    }

    /**
     * Static initializer for fluent use.
     *
     * Hopefully future-proof if __invokeStatic() should appear in language.
     *
     * @return object Tempalicious
     */
    public static function __invokeStatic()
    {
        return new Tempalicious();
    }

    /**
     * Static initializer to create a Tempalicious file.
     *
     * @param string The desired extension, example: "png", default NULL
     * @return string The path to a temp file location.
     */
    public static function create($extension = NULL)
    {
        return Tempalicious::__invokeStatic()
                   ->setExtension($extension)
                   ->getTempfilePath()
                   ;
    }
}
