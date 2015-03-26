<?php

class TempaliciousTest extends PHPUnit_Framework_TestCase
{
    /**
     * @testdox Tempalicious::create() creates a tempfile with no extension
     */
    function testConvenience()
    {
        $t = Tempalicious::create();
        $this->assertFileExists($t);
        $this->assertNotContains('.', $t);

    }

    /**
     * @testdox Tempalicious::create('ext') creates a tempfile with extension '.ext'
     */
    function testConvenienceWithExtension()
    {
        $t = Tempalicious::create('ext');
        $this->assertFileExists($t);
        $this->assertRegexp('/\.ext$/', $t);
    }

    /**
     * @testdox Tempalicious puts temp files into sys_get_temp_dir()
     */
    function testTempFileLocation()
    {
        $t = Tempalicious::create('ext');
        $tempaliciousTmpFileDirPath = dirname($t);
        $this->assertEquals(sys_get_temp_dir(), $tempaliciousTmpFileDirPath);

        $t = Tempalicious::create();
        $tempaliciousTmpFileDirPath = dirname($t);
        $this->assertEquals(sys_get_temp_dir(), $tempaliciousTmpFileDirPath);
    }

    /**
     * @testdox Tempalicious::create('.ext') creates a tempfile with extension '.ext'
     */
    function testConvenienceWithExtensionAndExtraDot()
    {
        $t = Tempalicious::create('.ext');
        $this->assertFileExists($t);
        $this->assertRegexp('/\.ext$/', $t);

    }

    function testAutoCleanupWithNoExtension()
    {
        $t = new Tempalicious();
        $tempFile = $t->getTempfilePath();

        $this->assertFileExists($tempFile, "/tmp/Tempfile file doesn't exist");

        $t->cleanup();
        $this->assertFileNotExists($tempFile, "/tmp/Tempfile wasn't cleaned up.");
    }

    function testAutoCleanupWithExtension()
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
