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
 *  @file Directory.php
 *
 *  The local directory class
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
use Platine\Filesystem\Adapter\Local\Exception\NotFoundException;
use Platine\Filesystem\DirectoryInterface;
use Platine\Filesystem\FileInterface;

/**
 * Class Directory
 * @package Platine\Filesystem\Adapter\Local
 */
class Directory extends AbstractLocal implements DirectoryInterface
{

    /**
    * {@inheritdoc}
    */
    public function copyTo($directory, int $mode = 0775)
    {
        if (!$this->exists()) {
            throw new NotFoundException(sprintf(
                'Source path [%s] not found',
                $this->path
            ));
        }

        if (is_string($directory)) {
            $directory = $this->adapter->directory($directory);
        }

        if ($directory->getPath() === $this->getPath()) {
            throw new InvalidArgumentException(sprintf(
                'Source and destination path [%s] can not be same',
                $this->path
            ));
        }

        $destination = $directory->create($this->getName(), $mode);
        foreach ($this->read() as $item) {
            if ($item->isDir() && $item->getPath() === $directory->getPath()) {
                // not allow copying into self child directory
                continue;
            }
            $item->copyTo($destination);
        }

        return $destination;
    }

    /**
    * {@inheritdoc}
    */
    public function create(string $name, int $mode = 0775)
    {
        if (!file_exists($this->path . DIRECTORY_SEPARATOR . $name)) {
            mkdir($this->path . DIRECTORY_SEPARATOR . $name, $mode);
        } else {
            chmod($this->path . DIRECTORY_SEPARATOR . $name, $mode);
        }

        return $this->adapter->directory(
            $this->originalPath . DIRECTORY_SEPARATOR . $name
        );
    }

    /**
    * {@inheritdoc}
    */
    public function createFile(
        string $name,
        string $content = '',
        int $mode = 0775
    ): FileInterface {
        $file = new File('', $this->adapter);
        return $file->create(
            $this->originalPath . DIRECTORY_SEPARATOR . $name,
            $content,
            $mode
        );
    }

    /**
    * {@inheritdoc}
    */
    public function delete(): self
    {
        if (!$this->exists()) {
            return $this;
        }

        foreach ($this->read() as $item) {
            $item->delete();
        }
        rmdir($this->path);

        return $this;
    }

    /**
    * {@inheritdoc}
    */
    public function getType(): string
    {
        return 'dir';
    }

    /**
    * {@inheritdoc}
    */
    public function read(int $type = self::ALL): array
    {
        $files = [
            'dir' => [],
            'file' => [],
        ];

        $items = scandir($this->path);
        if ($items === false) {
            return [];
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $info = $this->adapter->get(
                $this->originalPath . DIRECTORY_SEPARATOR . $item
            );
            if ($info !== null) {
                $files[$info->getType()][] = $info;
            }
        }

        switch ($type) {
            case self::ALL:
                return array_merge($files['dir'], $files['file']);
            case self::FILE:
                return $files['file'];
            case self::DIR:
                return $files['dir'];
            default:
                throw new InvalidArgumentException(sprintf(
                    'Invalid filter value [%d] must be one of [%s]',
                    $type,
                    implode(', ', [self::ALL, self::FILE, self::DIR])
                ));
        }
    }

    /**
    * {@inheritdoc}
    */
    public function scan(): array
    {
        $list = [];
        $items = scandir($this->path);
        if ($items !== false) {
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..') {
                    $list[] = $item;
                }
            }
        }

        return $list;
    }
}
