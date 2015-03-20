<?php

class TempaliciousTest extends PHPUnit_Framework_TestCase
{
    function testTempFilesWithNoExtension()
    {
        $t = new Tempalicious();
        $tempFile = $t->getTempfilePath();

        $this->assertFileExists($tempFile, "/tmp/Tempfile file doesn't exist");

        $t->cleanup();
        $this->assertFileNotExists($tempFile, "/tmp/Tempfile wasn't cleaned up.");
    }

    function testTempFilesWithExtension()
    {
        $t = new Tempalicious();
        $t->setExtension('ext');
        $tempFileWithExtension = $t->getTempfilePath();
        $semFile = dirname($tempFileWithExtension) . '/' . basename($tempFileWithExtension);

        $this->assertFileExists($semFile, "/tmp/Tempfile semaphore file doesn't exist");
        $this->assertFileExists($tempFileWithExtension, "/tmp/Tempfile.ext doesn't exist");

        $t->cleanup();
        $this->assertFileNotExists($semFile, "/tmp/Tempfile semaphore wasn't cleaned up.");
        $this->assertFileNotExists($tempFileWithExtension, "/tmp/Tempfile.ext wasn't cleaned up.");
    }
}
