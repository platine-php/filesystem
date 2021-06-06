<?php

declare(strict_types=1);

namespace Platine\Test\Filesystem\Adapter\Local;

use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use Platine\Filesystem\Adapter\Local\Directory;
use Platine\Filesystem\Adapter\Local\Exception\NotFoundException;
use Platine\Filesystem\Adapter\Local\LocalAdapter;
use Platine\Filesystem\DirectoryInterface;
use Platine\Dev\PlatineTestCase;

/**
 * Directory class tests
 *
 * @group core
 * @group filesystem
 */
class DirectoryTest extends PlatineTestCase
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
        $t = new Directory('my_path', $adapter);
        $this->assertInstanceOf(DirectoryInterface::class, $t);
    }

    public function testCreate(): void
    {
        $dir = $this->createVfsDirectory('directories', $this->vfsPath);

        $t = new Directory($dir->url(), new LocalAdapter($this->vfsPath->url()));
        $myDir = $t->create('my_dir');
        $this->assertInstanceOf(DirectoryInterface::class, $myDir);
        $this->assertEquals('my_dir', $myDir->getName());
        $this->assertEquals('0775', $myDir->getPermission());
    }

    public function testCreateAlreadyExist(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $dir = $this->createVfsDirectory('directories', $this->vfsPath);

        $t = new Directory($dir->url(), new LocalAdapter($this->vfsPath->url()));

        $myDir = $t->create('mydir');
        $myDirSame = $t->create('mydir', 0700);
        $this->assertInstanceOf(DirectoryInterface::class, $myDir);
        $this->assertEquals('mydir', $myDir->getName());
        $this->assertEquals('mydir', $myDirSame->getName());
        $this->assertEquals('0700', $myDir->getPermission());
        $this->assertEquals('0700', $myDirSame->getPermission());
    }

    public function testGetType(): void
    {
        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $this->vfsPath->url()]
        );

        $t = new Directory($this->vfsPath->url(), $adapter);
        $this->assertEquals('dir', $t->getType());
    }

    public function testReadAndScanFalse(): void
    {
        global $mock_scandir_to_false;

        $mock_scandir_to_false = true;

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $this->vfsPath->url()]
        );

        $t = new Directory($this->vfsPath->url(), $adapter);
        $this->assertEmpty($t->read());
        $this->assertEmpty($t->scan());
    }

    public function testReadAndScanItems(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $src = $this->createVfsDirectory('directories', $this->vfsPath);

        $t = new Directory($src->url(), new LocalAdapter($this->vfsPath->url()));
        $t->createFile('file1', 'foo');
        $t->createFile('file2', 'bar');
        $t->create('dir');

        $this->assertCount(3, $t->read());
        $this->assertCount(3, $t->scan());
    }

    public function testReadOnlyFile(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $src = $this->createVfsDirectory('directories', $this->vfsPath);

        $t = new Directory($src->url(), new LocalAdapter($this->vfsPath->url()));
        $t->createFile('file1', 'foo');
        $t->createFile('file2', 'bar');
        $t->create('dir');

        $this->assertCount(2, $t->read(DirectoryInterface::FILE));
    }

    public function testReadOnlyDir(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $src = $this->createVfsDirectory('directories', $this->vfsPath);

        $t = new Directory($src->url(), new LocalAdapter($this->vfsPath->url()));
        $t->createFile('file1', 'foo');
        $t->createFile('file2', 'bar');
        $t->create('dir');

        $this->assertCount(1, $t->read(DirectoryInterface::DIR));
    }

    public function testReadInvalidFilte(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $src = $this->createVfsDirectory('directories', $this->vfsPath);

        $t = new Directory($src->url(), new LocalAdapter($this->vfsPath->url()));

        $this->expectException(InvalidArgumentException::class);
        $t->read(47558);
    }


    public function testDeleteItemsMultiple(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $src = $this->createVfsDirectory('directories', $this->vfsPath);

        $t = new Directory($src->url(), new LocalAdapter($this->vfsPath->url()));
        $t->createFile('file1', 'foo');
        $t->createFile('file2', 'bar');
        $t->create('dir');

        $this->assertCount(3, $t->read());
        $this->assertTrue($t->exists());
        $t->delete();
        $this->assertFalse($t->exists());
    }

    public function testDeleteNotExist(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $t = new Directory('my_not_found_dir', new LocalAdapter($this->vfsPath->url()));

        $this->assertFalse($t->exists());
        $t->delete();
        $this->assertFalse($t->exists());
    }

    public function testDelete(): void
    {
        $dir = $this->createVfsDirectory('directories', $this->vfsPath);

        $adapter = $this->getMockInstance(
            LocalAdapter::class,
            ['getAbsolutePath' => $dir->url()]
        );

        $t = new Directory($dir->url(), $adapter);
        $this->assertTrue($t->exists());
        $t->delete();
        $this->assertFalse($t->exists());
    }

    public function testCopyTo(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $src = $this->createVfsDirectory('directories', $this->vfsPath);
        $dir = $this->createVfsDirectory('copies', $this->vfsPath);

        $t = new Directory($src->url(), new LocalAdapter($this->vfsPath->url()));
        $myDir = $t->copyTo($dir->url());
        $this->assertTrue($myDir->exists());
        $this->assertTrue($myDir->isDir());
    }

    public function testMoveTo(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $src = $this->createVfsDirectory('directories', $this->vfsPath);
        $dir = $this->createVfsDirectory('copies', $this->vfsPath);

        $t = new Directory($src->url(), new LocalAdapter($this->vfsPath->url()));
        $this->assertTrue($t->exists());
        $myDir = $t->moveTo($dir->url());
        $this->assertFalse($t->exists());
        $this->assertTrue($myDir->exists());
        $this->assertTrue($myDir->isDir());
    }

    public function testCopyToItemsMultiple(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $src = $this->createVfsDirectory('directories', $this->vfsPath);
        $dir = $this->createVfsDirectory('copies', $this->vfsPath);

        $t = new Directory($src->url(), new LocalAdapter($this->vfsPath->url()));
        $t->createFile('file1', 'foo');
        $t->createFile('file2', 'bar');
        $t->create('dir');

        $myDir = $t->copyTo($dir->url());
        $this->assertTrue($myDir->exists());
        $this->assertTrue($myDir->isDir());
        $this->assertCount(3, $myDir->read());
    }

    public function testCopyToItemsMultipleSelfChildDirectory(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $src = $this->createVfsDirectory('directories', $this->vfsPath);
        $dir = $this->createVfsDirectory('copies', $this->vfsPath);

        $t = new Directory($src->url(), new LocalAdapter($this->vfsPath->url()));
        $dir1 = $t->create('dir1');

        $myDir = $t->copyTo($dir1);
        $this->assertTrue($myDir->exists());
        $this->assertTrue($myDir->isDir());
        $this->assertEquals('directories', $myDir->getName());
    }

    public function testCopyToNotExist(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $this->expectException(NotFoundException::class);
        $dir = $this->createVfsDirectory('copies', $this->vfsPath);

        $t = new Directory('my_dir_that_not_exist', new LocalAdapter($this->vfsPath->url()));
        $t->copyTo($dir->url());
    }

    public function testCopyToPathIsSame(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $this->expectException(InvalidArgumentException::class);
        $dir = $this->createVfsDirectory('copies', $this->vfsPath);

        $t = new Directory($dir->url(), new LocalAdapter($this->vfsPath->url()));
        $t->copyTo($t);
    }
}
