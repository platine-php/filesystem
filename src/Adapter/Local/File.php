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
 *  @file File.php
 *
 *  The local file class
 *
 *  @package    Platine\Filesystem\Adapter\Local
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   https://www.platine-php.com
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\Filesystem\Adapter\Local;

use Platine\Filesystem\DirectoryInterface;
use Platine\Filesystem\FileInterface;
use Platine\Stdlib\Helper\Path;

/**
 * @class File
 * @package Platine\Filesystem\Adapter\Local
 */
class File extends AbstractLocal implements FileInterface
{
    /**
    * {@inheritdoc}
    */
    public function append(string $content): self
    {
        file_put_contents($this->path, $content, FILE_APPEND);

        return $this;
    }

    /**
    * {@inheritdoc}
    */
    public function copyTo(
        string|DirectoryInterface $directory,
        int $mode = 0775
    ): FileInterface {
        if (is_string($directory)) {
            $directory = $this->adapter->directory($directory);
        }

        return $directory->createFile(
            $this->getName(),
            $this->read(),
            $mode
        );
    }

    /**
    * {@inheritdoc}
    */
    public function create(string $path, string $content = '', int $mode = 0775): FileInterface
    {
        $file = $this->adapter->file($path)->write($content);
        $file->chmod($mode);

        return $file;
    }

    /**
    * {@inheritdoc}
    */
    public function delete(): self
    {
        unlink($this->path);

        return $this;
    }

    /**
    * {@inheritdoc}
    */
    public function getExtension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
    * {@inheritdoc}
    */
    public function getMime(): string
    {
        $mime = mime_content_type($this->path);
        if (!$mime || $mime === 'application/octet-stream' || $mime === 'inode/x-empty') {
            $mime = Path::getMimeByExtension($this->getExtension());
        }

        return $mime;
    }

    /**
    * {@inheritdoc}
    */
    public function getType(): string
    {
        return 'file';
    }

    /**
    * {@inheritdoc}
    */
    public function read(): string
    {
        return (string) file_get_contents($this->path);
    }

    /**
    * {@inheritdoc}
    */
    public function write(string $content): self
    {
        file_put_contents($this->path, $content);

        return $this;
    }
}
