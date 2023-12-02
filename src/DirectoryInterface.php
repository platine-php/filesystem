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
 *  @file DirectoryInterface.php
 *
 *  The directory interface
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

/**
 * Class DirectoryInterface
 * @package Platine\Filesystem
 */
interface DirectoryInterface extends FilesystemInterface
{
    /**
     * Filter list for directory read
     */
    public const ALL = 1;
    public const FILE = 2;
    public const DIR = 3;

    /**
     * Create new directory
     * @param string $name
     * @param int $mode
     * @param bool $recursive
     * @return self
     */
    public function create(string $name, int $mode = 0775, bool $recursive = false);

    /**
     * Create new file in this directory
     * @param string $name
     * @param string $content
     * @param int $mode
     * @return FileInterface
     */
    public function createFile(
        string $name,
        string $content = '',
        int $mode = 0775
    ): FileInterface;

    /**
     * Scan the directory and return the raw content
     * @return array<int, string>
     */
    public function scan(): array;

    /**
     * Return the directory content
     * @param int $filter
     * @return array<int, FileInterface|DirectoryInterface>
     */
    public function read(int $filter = self::ALL): array;
}
