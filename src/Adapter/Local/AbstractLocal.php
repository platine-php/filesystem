<?php

/**
 * Platine Filesystem
 *
 * Platine Filesystem is file system abstraction layer extendable by adapters
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2020 Platine Filesystem
 * Copyright (c) 2019 Alex Sivka
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 *  @file AbstractLocal.php
 *
 *  The file system local base class
 *
 *  @package    Platine\Filesystem\Adapter\Local
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   http://www.iacademy.cf
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\Filesystem\Adapter\Local;

use Platine\Filesystem\Adapter\AdapterInterface;
use Platine\Filesystem\FileSystemInterface;
use Platine\Stdlib\Helper\Path;

/**
 * Class AbstractLocal
 * @package Platine\Filesystem\Adapter\Local
 */
abstract class AbstractLocal implements FileSystemInterface
{

    /**
     * The adapter instance
     * @var AdapterInterface
     */
    protected AdapterInterface $adapter;

    /**
     * The path of the file system
     * @var string
     */
    protected string $path;

    /**
     * The original path of the file system
     * @var string
     */
    protected string $originalPath;

    /**
     * Create new instance
     * @param string $path
     * @param AdapterInterface $adapter
     */
    public function __construct(string $path, AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->setPath($path);
    }

    /**
     * Return the adapter instance
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * Return the path of the file system
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the path of the file system
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->originalPath = $path && $path !== DIRECTORY_SEPARATOR
                                ? trim($path, DIRECTORY_SEPARATOR)
                                : DIRECTORY_SEPARATOR;
        $this->path = $this->adapter->getAbsolutePath($path);

        return $this;
    }

    /**
     * Return the original path
     * @return string
     */
    public function getOriginalPath(): string
    {
        return $this->originalPath;
    }

    /**
    * {@inheritdoc}
    */
    public function moveTo($directory)
    {
        $dest = $this->copyTo($directory);
        $dest->touch($this->getMtime());
        $this->delete();

        return $dest;
    }

    /**
    * {@inheritdoc}
    */
    public function chmod(int $mode)
    {
        chmod($this->path, $mode);

        return $this;
    }

    /**
    * {@inheritdoc}
    */
    public function exists(): bool
    {
        return $this->isExists($this->path);
    }

    /**
    * {@inheritdoc}
    */
    public function getLocation(): string
    {
        return dirname($this->path);
    }

    /**
    * {@inheritdoc}
    */
    public function getMtime(): int
    {
        $time  = filemtime($this->path);
        return $time !== false ? $time : -1;
    }

    /**
    * {@inheritdoc}
    */
    public function getName(): string
    {
        return basename($this->path);
    }

    /**
    * {@inheritdoc}
    */
    public function getPermission(): string
    {
        $permission = fileperms($this->path);
        if ($permission === false) {
            return '';
        }
        return substr(base_convert((string) $permission, 10, 8), -4);
    }

    /**
    * {@inheritdoc}
    */
    public function getSize(): int
    {
        $size = filesize($this->path);
        return $size !== false ? $size : -1;
    }

    /**
    * {@inheritdoc}
    */
    public function isDir(): bool
    {
        return $this->getType() === 'dir';
    }

    /**
    * {@inheritdoc}
    */
    public function isFile(): bool
    {
        return $this->getType() === 'file';
    }

    /**
    * {@inheritdoc}
    */
    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    /**
    * {@inheritdoc}
    */
    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    /**
    * {@inheritdoc}
    */
    public function rename(string $newPath)
    {
        $normalizedNewPath = rtrim(Path::normalizePathDS($newPath), '\\/');
        if (strpos($normalizedNewPath, DIRECTORY_SEPARATOR) === false) {
            $normalizedNewPath = dirname($this->originalPath)
                            . DIRECTORY_SEPARATOR . $normalizedNewPath;
        }

        $newAbsolutePath = $this->adapter->getAbsolutePath($normalizedNewPath);
        if ($newAbsolutePath === $this->path) {
            return $this;
        }
        rename($this->path, $newAbsolutePath);

        return $this->setPath($normalizedNewPath);
    }

    /**
    * {@inheritdoc}
    */
    public function touch(int $time)
    {
        touch($this->path, $time);

        return $this;
    }

    /**
     * Whether the given path exists
     * @param string $path
     * @return bool
     */
    protected function isExists(string $path): bool
    {
        return file_exists($path);
    }
}
