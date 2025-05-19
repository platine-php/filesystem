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
 *  @file FilesystemInterface.php
 *
 *  The file system common interface
 *
 *  @package    Platine\Filesystem
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   https://www.platine-php.com
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\Filesystem;

use Platine\Filesystem\Adapter\AdapterInterface;

/**
 * @class FilesystemInterface
 * @package Platine\Filesystem
 */
interface FilesystemInterface
{
    /**
     * Whether the file system is a file
     * @return bool
     */
    public function isFile(): bool;

    /**
     * Whether the file system is a directory
     * @return bool
     */
    public function isDir(): bool;

    /**
     * Return the name of this file system
     * @return string
     */
    public function getName(): string;

    /**
     * Return the path of this file system
     * @return string
     */
    public function getPath(): string;

    /**
     * Copy the file system to given path
     * @param string|DirectoryInterface $directory
     * @param int $mode the mode
     * @return FileInterface|DirectoryInterface
     */
    public function copyTo(string|DirectoryInterface $directory, int $mode = 0775): FileInterface|DirectoryInterface;

    /**
     * Move this file system to new path
     * @param string|DirectoryInterface $directory
     * @return FileInterface|DirectoryInterface
     */
    public function moveTo(string|DirectoryInterface $directory): FileInterface|DirectoryInterface;

    /**
     * Return the original path of this file system
     * @return string
     */
    public function getOriginalPath(): string;

    /**
     * Return the size of the file system
     * @return int
     */
    public function getSize(): int;

    /**
     * Change the modification time of the file system
     * @param int $time
     * @return $this
     */
    public function touch(int $time): self;

    /**
     * Return the location of this file system
     * @return string
     */
    public function getLocation(): string;

    /**
     * Return the type of the file system
     * @return string
     */
    public function getType(): string;

    /**
     * Change the file system permission
     * @param int $mode
     * @return $this
     */
    public function chmod(int $mode): self;

    /**
     * Whether the file system exists
     * @return bool
     */
    public function exists(): bool;

    /**
     * Return the permission of the file system
     * @return string
     */
    public function getPermission(): string;

    /**
     * Return the modification time of
     * the file system
     * @return int
     */
    public function getMtime(): int;

    /**
     * Rename the file system
     * @param string $newPath
     * @return $this
     */
    public function rename(string $newPath): self;

    /**
     * Delete the file system
     * @return FileInterface|DirectoryInterface
     */
    public function delete(): FileInterface|DirectoryInterface;

    /**
     * Whether the file system is readable
     * @return bool
     */
    public function isReadable(): bool;

    /**
     * Whether the file system is writable
     * @return bool
     */
    public function isWritable(): bool;

    /**
     * Return the adapter instance
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface;
}
