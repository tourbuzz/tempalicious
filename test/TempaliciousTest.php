<?php

class TempaliciousTest extends PHPUnit_Framework_TestCase
{
    function testTempFilesCreated()
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
