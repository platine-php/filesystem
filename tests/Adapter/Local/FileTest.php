<?php

declare(strict_types=1);

namespace Platine\Test\Filesystem\Adapter\Local;

use org\bovigo\vfs\vfsStream;
use Platine\Filesystem\Adapter\AdapterInterface;
use Platine\Filesystem\Adapter\Local\File;
use Platine\Filesystem\Adapter\Local\LocalAdapter;
use Platine\Filesystem\FileInterface;
use Platine\Dev\PlatineTestCase;

/**
 * File class tests
 *
 * @group core
 * @group filesystem
 */
class FileTest extends PlatineTestCase
{
    protected $vfsRoot;
    protected $vfsPath;

    protected function setUp(): void
    {
        parent::setUp();
        //need setup for each test
        $this->vfsRoot = vfsStream::setup();
        $this->vfsPath = vfsStream::newDirectory('my_tests')->at($this->vfsRoot);
    }

    public function testConstructor(): void
    {
        $adapter = $this->getMockInstance(LocalAdapter::class);
        $t = new File('my_path', $adapter);
        $this->assertInstanceOf(FileInterface::class, $t);
    }

    public function testAppend(): void
    {
        $file = $this->createVfsFile('my_file', $this->vfsPath, 'foo');

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $file->url()]
        );

        $t = new File($file->url(), $adapter);
        $t->append('bar');
        $this->assertEquals('foobar', $t->read());
    }

    public function testWrite(): void
    {
        $file = $this->createVfsFile('my_file', $this->vfsPath, 'foo');

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $file->url()]
        );

        $t = new File($file->url(), $adapter);
        $t->write('bar');
        $this->assertEquals('bar', $t->read());
    }

    public function testCreate(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $t = new File($this->vfsPath->url(), new LocalAdapter($this->vfsPath->url()));
        $file = $t->create('my_file', 'bar');
        $this->assertEquals('bar', $file->read());
        $this->assertEquals(3, $file->getSize());
        $this->assertTrue($file->isFile());
    }

    public function testGetType(): void
    {
        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $this->vfsPath->url()]
        );

        $t = new File($this->vfsPath->url(), $adapter);
        $this->assertEquals('file', $t->getType());
    }

    public function testGetAdapter(): void
    {
        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $this->vfsPath->url()]
        );

        $t = new File($this->vfsPath->url(), $adapter);
        $this->assertInstanceOf(AdapterInterface::class, $t->getAdapter());
        $this->assertEquals($adapter, $t->getAdapter());
        $this->assertEquals($this->vfsPath->url(), $t->getOriginalPath());
    }

    public function testGetExtension(): void
    {
        $file = $this->createVfsFile('my_file.foo', $this->vfsPath, 'foo');

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $file->url()]
        );

        $t = new File($file->url(), $adapter);
        $this->assertEquals('foo', $t->getExtension());
    }

    public function testGetMime(): void
    {
        $file = $this->createVfsFile('my_file.foo', $this->vfsPath, 'foo');

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $file->url()]
        );

        $t = new File($file->url(), $adapter);
        $this->assertEquals('text/plain', $t->getMime());
    }

    public function testLocation(): void
    {
        $file = $this->createVfsFile('my_file.foo', $this->vfsPath, 'foo');

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $file->url()]
        );

        $t = new File($file->url(), $adapter);
        $this->assertEquals($this->vfsPath->url(), $t->getLocation());
    }

    public function testReadWrite(): void
    {
        $file = $this->createVfsFile('my_file.foo', $this->vfsPath, 'foo');

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $file->url()]
        );

        $t = new File($file->url(), $adapter);
        $this->assertTrue($t->isReadable());
        $this->assertTrue($t->isWritable());
        $t->chmod(0100);
        $this->assertFalse($t->isReadable());
        $this->assertFalse($t->isWritable());
    }

    public function testGetPermissionFunctionfilePermsReturnFalse(): void
    {
        global $mock_fileperms_to_false;

        $mock_fileperms_to_false = true;
        $file = $this->createVfsFile('my_file.pdf', $this->vfsPath, 'foo');

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $file->url()]
        );

        $t = new File($file->url(), $adapter);
        $this->assertEmpty($t->getPermission());
    }

    public function testGetMimeUsingHelperToGetMimetype(): void
    {
        global $mock_mime_content_type_to_false;

        $mock_mime_content_type_to_false = true;
        $file = $this->createVfsFile('my_file.pdf', $this->vfsPath, 'foo');

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $file->url()]
        );

        $t = new File($file->url(), $adapter);
        $this->assertEquals('application/pdf', $t->getMime());
    }

    public function testDelete(): void
    {
        $file = $this->createVfsFile('my_file.foo', $this->vfsPath, 'foo');

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $file->url()]
        );

        $t = new File($file->url(), $adapter);
        $this->assertTrue($t->exists());
        $t->delete();
        $this->assertFalse($t->exists());
    }

    public function testCopyTo(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $file = $this->createVfsFile('my_file.foo', $this->vfsPath, 'foo');
        $dir = $this->createVfsDirectory('copies', $this->vfsPath);

        $t = new File($file->url(), new LocalAdapter($this->vfsPath->url()));
        $myFile = $t->copyTo($dir->url());
        $this->assertTrue($myFile->exists());
    }

    public function testRename(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $file = $this->createVfsFile('my_file.foo', $this->vfsPath, 'foo');

        $t = new File($file->url(), new LocalAdapter($this->vfsPath->url()));
        $myFile = $t->rename('my_file');
        $this->assertEquals('my_file', $myFile->getName());
    }

    public function testRenameSamePath(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $file = $this->createVfsFile('my_file.foo', $this->vfsPath, 'foo');

        $t = new File($file->url(), new LocalAdapter($this->vfsPath->url()));
        $myFile = $t->rename($file->url());
        $this->assertEquals('my_file.foo', $myFile->getName());
    }
}
