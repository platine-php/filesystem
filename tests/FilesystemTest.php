<?php

declare(strict_types=1);

namespace Platine\Test\Filesystem;

use Platine\Filesystem\Adapter\Local\Directory;
use Platine\Filesystem\Adapter\Local\File;
use Platine\Filesystem\Adapter\Local\LocalAdapter;
use Platine\Filesystem\Filesystem;
use Platine\Dev\PlatineTestCase;

/**
 * Filesystem class tests
 *
 * @group core
 * @group filesystem
 */
class FilesystemTest extends PlatineTestCase
{
    public function testConstructor(): void
    {
        $t = new Filesystem();
        $this->assertInstanceOf(Filesystem::class, $t);
    }

    public function testGetAdapter(): void
    {
        $adapter = $this->getMockInstance(LocalAdapter::class);
        $t = new Filesystem($adapter);
        $this->assertInstanceOf(LocalAdapter::class, $t->getAdapter());
        $this->assertEquals($adapter, $t->getAdapter());
    }

    public function testGetReturnFileInstance(): void
    {
        $file = $this->getMockInstance(File::class);
        $adapter = $this->getMockInstance(LocalAdapter::class, ['get' => $file]);
        $t = new Filesystem($adapter);
        $this->assertInstanceOf(File::class, $t->get('my_path'));
    }

    public function testGetReturnDirectoryInstance(): void
    {
        $dir = $this->getMockInstance(Directory::class);
        $adapter = $this->getMockInstance(LocalAdapter::class, ['get' => $dir]);
        $t = new Filesystem($adapter);
        $this->assertInstanceOf(Directory::class, $t->get('my_path'));
    }

    public function testGetReturnNull(): void
    {
        $adapter = $this->getMockInstance(LocalAdapter::class, ['get' => null]);
        $t = new Filesystem($adapter);
        $this->assertNull($t->get('my_path'));
    }

    public function testFileInstance(): void
    {
        $file = $this->getMockInstance(File::class);
        $adapter = $this->getMockInstance(LocalAdapter::class, ['file' => $file]);
        $t = new Filesystem($adapter);
        $this->assertInstanceOf(File::class, $t->file('my_path'));
    }

    public function testDirectoryInstance(): void
    {
        $dir = $this->getMockInstance(Directory::class);
        $adapter = $this->getMockInstance(LocalAdapter::class, ['directory' => $dir]);
        $t = new Filesystem($adapter);
        $this->assertInstanceOf(Directory::class, $t->directory('my_path'));
    }
}
