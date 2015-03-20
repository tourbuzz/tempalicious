<?php

/**
 * A robust tempfile implementation supporting:
 * * File Extensions
 * * Automatic cleanup
 *
 * 1) Uses a semaphore file to ensure no collissions (php seems to re-issue tmp files of same name if deleted)
 * 2) Automatically cleans up temp files when the request exits (since you won't know when the caller is done w/file)
 */
class Tempalicous
{
    protected $inited               = false;
    protected $tmpDir               = NULL;
    protected $extension            = NULL;
    protected $tmpFile              = NULL;
    protected $tmpFileWithExtension = NULL;

    public function __construct()
    {
        $this->setTmpDir(sys_get_temp_dir());
    }

    public function setExtension($extension)
    {
        $this->extension = ltrim(trim($extension), '.');
        return $this;
    }

    public function setTmpDir($dir)
    {
        $this->tmpDir = $dir;
        return $this;
    }

    private function ensureTempFile()
    {
        if ($inited) return;
        $inited = true;

        if (!$this->extension) throw new Exception("Extension is required in this version of Tempalicous");

        register_shutdown_function(array($this, 'cleanup'));

        // provision semaphore file
        $this->tmpFile = tempnam($this->tmpDir, "Tempalicous_");

        // create actual temp file
        $this->tmpFileWithExtension = "{$this->tmpFile}.{$this->extension}";
        $ok = touch($this->tmpFileWithExtension);
        if (!$ok) throw new Exception("Unable to touch {$this->tmpFileWithExtension}");
    }

    public function cleanup()
    {
        if ($this->tmpFileWithExtension && file_exists($this->tmpFileWithExtension))
        {
            unlink($this->tmpFileWithExtension);
        }

        // clean semaphore file last
        if ($this->tmpFile && file_exists($this->tmpFile))
        {
            unlink($this->tmpFile);
        }
    }

    public function getTempfilePath()
    {
        $this->ensureTempFile();

        return $this->tmpFileWithExtension;
    }

    /**
     * Static initializer to create a Tempalicous file.
     *
     * @param string The desired extension, example: "png"
     * @return string The path to a temp file location.
     */
    public static function create($extension)
    {
        $tmp = new Tempalicous();
        return $tmp->setExtension($extension)
                   ->getTempfilePath()
                   ;
    }
}

