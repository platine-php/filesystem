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
 *  @file Filesystem.php
 *
 *  The file system main class
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
use Platine\Filesystem\Adapter\Local\LocalAdapter;

/**
 * @class Filesystem
 * @package Platine\Filesystem
 */
class Filesystem
{
    /**
     * The adapter instance
     * @var AdapterInterface
     */
    protected AdapterInterface $adapter;

    /**
     * Create new instance
     * @param AdapterInterface|null $adapter
     */
    public function __construct(?AdapterInterface $adapter = null)
    {
        $this->adapter = $adapter ?? new LocalAdapter();
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
     * Return the instance of file or directory
     * @param string $path
     * @return FileInterface|DirectoryInterface|null
     */
    public function get(string $path): FileInterface|DirectoryInterface|null
    {
        return $this->adapter->get($path);
    }

    /**
     * Return the file instance
     * @param string $path
     * @return FileInterface
     */
    public function file(string $path = ''): FileInterface
    {
        return $this->adapter->file($path);
    }

    /**
     * Return the directory instance
     * @param string $path
     * @return DirectoryInterface
     */
    public function directory(string $path = ''): DirectoryInterface
    {
        return $this->adapter->directory($path);
    }
}
