<?php
include __DIR__ . "/../vendor/autoload.php";

use FtpLib\File;
use FtpLib\Ftp;

list($host, $user, $pass) = include __DIR__ . "/credentials.php";

$ftp = new Ftp($host, $user, $pass);
$ftp->connect();
$ftp->setPasv();

$file = $ftp->putFileFromPath(__DIR__ . '/fixtures/foo');
echo $file->getName();
echo $file->getContent();
$file->delete();

$file = $ftp->putFileFromString('bar', 'bla, bla, bla');
echo $file->getName();
echo $file->getContent();
$file->delete();

$ftp->mkdir('directory')->chdir('directory')->putFileFromString('newFile', 'bla, bla')->delete();
$ftp->rmdir('directory');

$ftp->putFileFromString('file1', 'bla, bla, bla');
$ftp->putFileFromString('file2', 'bla, bla, bla');

$ftp->getFiles(
    function (File $file) use ($ftp) {
        switch($file->getName()) {
            case 'file1':
                $file->delete();
                break;
            case 'file2':
                $ftp->mkdir('backup')->chdir('backup')->putFileFromString($file->getName(), $file->getContent());
                break;
        }
    }
);
