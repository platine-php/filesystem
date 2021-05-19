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
 *  @file LocalAdpater.php
 *
 *  The file system local adapter class
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

use InvalidArgumentException;
use Platine\Filesystem\Adapter\AdapterInterface;
use Platine\Filesystem\DirectoryInterface;
use Platine\Filesystem\FileInterface;
use Platine\Filesystem\Util\Helper;

/**
 * Class LocalAdpater
 * @package Platine\Filesystem\Adapter\Local
 */
class LocalAdapter implements AdapterInterface
{

    /**
     * The root directory
     * @var string
     */
    protected string $root;

    /**
     * Create new instance
     * @param string|null $root
     */
    public function __construct(?string $root = null)
    {
        if ($root === null) {
            $path = realpath(__DIR__ . '../../../../../');
            if ($path !== false) {
                $root = dirname($path);
            } else {
                $root = '.';
            }
        }

        $normalizedRoot = Helper::normalizePath($root);
        $rootAbsolute = realpath($normalizedRoot);
        if ($rootAbsolute !== false) {
            $this->root = $rootAbsolute . DIRECTORY_SEPARATOR;
        } else {
            throw new InvalidArgumentException(sprintf(
                'Invalid root path [%s]',
                $root
            ));
        }
    }


    /**
    * {@inheritdoc}
    */
    public function directory(string $path = ''): DirectoryInterface
    {
        return new Directory($path, $this);
    }

    /**
    * {@inheritdoc}
    */
    public function file(string $path = ''): FileInterface
    {
        return new File($path, $this);
    }

    /**
    * {@inheritdoc}
    */
    public function get(string $path)
    {
        $absolutePath = $this->getAbsolutePath($path);

        if (is_file($absolutePath)) {
            return $this->file($path);
        }

        if (is_dir($absolutePath)) {
            return $this->directory($path);
        }

        return null;
    }

    /**
    * {@inheritdoc}
    */
    public function getAbsolutePath(string $path): string
    {
        $normalizedPath = Helper::normalizePath($path);
        if (strpos($normalizedPath, $this->root) !== 0) {
            $normalizedPath = $this->root . ltrim($normalizedPath, DIRECTORY_SEPARATOR);
        }

        return $normalizedPath;
    }
}
