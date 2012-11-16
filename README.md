```php
[![Build Status](https://secure.travis-ci.org/gonzalo123/ftplib.png?branch=master)](https://travis-ci.org/gonzalo123/ftplib)

<?php
include __DIR__ . "/../vendor/autoload.php";

use FtpLib\Ftp,
    FtpLib\File;

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

$ftp->getFiles(function (File $file) use ($ftp) {
    switch($file->getName()) {
        case 'file1':
            $file->delete();
            break;
        case 'file2':
            $ftp->mkdir('backup')->chdir('backup')->putFileFromString($file->getName(), $file->getContent());
            break;
    }
});
```

FTP over ssl is also availabel with Ftp::connectSSL() instead Ftp::connect():

```php
<?php
$ftp = new Ftp($host, $user, $pass);
$ftp->connectSSL();
```
