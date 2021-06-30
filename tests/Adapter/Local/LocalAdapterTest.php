<?php

declare(strict_types=1);

namespace Platine\Test\Filesystem\Adapter\Local;

use InvalidArgumentException;
use Platine\Dev\PlatineTestCase;
use Platine\Filesystem\Adapter\Local\LocalAdapter;
use Platine\Filesystem\DirectoryInterface;
use Platine\Filesystem\FileInterface;

/**
 * LocalAdapter class tests
 *
 * @group core
 * @group filesystem
 */
class LocalAdapterTest extends PlatineTestCase
{
    public function testConstructorDefaultValue(): void
    {
        global $mock_realpath_to_foodir;

        $mock_realpath_to_foodir = true;

        $t = new LocalAdapter();
        $this->assertEmpty($this->getPropertyValue(LocalAdapter::class, $t, 'root'));
    }

    public function testConstructorRootDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $t = new LocalAdapter('path/not/found');
    }

    public function testDirectoryInstance(): void
    {
        $t = new LocalAdapter();
        $dir = $t->directory();
        $this->assertInstanceOf(DirectoryInterface::class, $dir);
    }

    public function testFileInstance(): void
    {
        $t = new LocalAdapter();
        $file = $t->file();
        $this->assertInstanceOf(FileInterface::class, $file);
    }

    public function testGetIsFile(): void
    {
        global $mock_is_file_to_true;

        $mock_is_file_to_true = true;
        $t = new LocalAdapter();
        $o = $t->get('my_path');
        $this->assertInstanceOf(FileInterface::class, $o);
    }

    public function testGetIsDirectory(): void
    {
        global $mock_is_dir_to_true;

        $mock_is_dir_to_true = true;
        $t = new LocalAdapter();
        $o = $t->get('my_path');
        $this->assertInstanceOf(DirectoryInterface::class, $o);
    }

    public function testGetReturnNull(): void
    {
        $t = new LocalAdapter();
        $o = $t->get('my_foo_path_that_does_not_exists');
        $this->assertNull($o);
    }

    public function testGetAbsolutePath(): void
    {
        global $mock_realpath_to_same;

        $mock_realpath_to_same = true;

        $myPath = 'my_foo_path_that_does_not_exists';
        $t = new LocalAdapter('.');
        $o = $t->getAbsolutePath('my_foo_path_that_does_not_exists');
        $this->assertEquals('.' . DIRECTORY_SEPARATOR . $myPath, $o);
    }
}
