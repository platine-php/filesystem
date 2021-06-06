<?php

declare(strict_types=1);

namespace Platine\Filesystem\Adapter\Local;

$mock_mime_content_type_to_false = false;
$mock_realpath_to_false = false;
$mock_realpath_to_foodir = false;
$mock_realpath_to_same = false;
$mock_is_file_to_true = false;
$mock_is_dir_to_true = false;
$mock_scandir_to_false = false;
$mock_fileperms_to_false = false;
$mock_is_writable_to_false = false;
$mock_is_writable_to_true = false;

function is_writable(string $key)
{
    global $mock_is_writable_to_true,
       $mock_is_writable_to_false;
    if ($mock_is_writable_to_false) {
        return false;
    }

    if ($mock_is_writable_to_true) {
        return true;
    }

    return \is_writable($key);
}

function fileperms(string $key)
{
    global $mock_fileperms_to_false;
    if ($mock_fileperms_to_false) {
        return false;
    }

    return \fileperms($key);
}

function scandir(string $key)
{
    global $mock_scandir_to_false;
    if ($mock_scandir_to_false) {
        return false;
    }

    return \scandir($key);
}

function realpath(string $key)
{
    global $mock_realpath_to_false,
           $mock_realpath_to_foodir,
           $mock_realpath_to_same;
    if ($mock_realpath_to_false) {
        return false;
    } elseif ($mock_realpath_to_foodir) {
        return 'foodir';
    } elseif ($mock_realpath_to_same) {
        return $key;
    }

    return \realpath($key);
}

function is_file(string $key)
{
    global $mock_is_file_to_true;
    if ($mock_is_file_to_true) {
        return true;
    }

    return \is_file($key);
}

function is_dir(string $key)
{
    global $mock_is_dir_to_true;
    if ($mock_is_dir_to_true) {
        return true;
    }

    return \is_dir($key);
}

function mime_content_type($key)
{
    global $mock_mime_content_type_to_false;
    if ($mock_mime_content_type_to_false) {
        return false;
    }

    return \mime_content_type($key);
}
