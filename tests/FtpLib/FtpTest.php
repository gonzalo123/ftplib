<?php
namespace FtpLib;

class FtpTest extends \PHPUnit_Framework_TestCase
{
    /** @var FtpLib\Ftp */
    private $ftp;

    public function setUp()
    {
        $this->ftp = new Ftp("ftphost", "username", "password");
        $this->ftp->setFtpWrapper($this->getMockFunctions());
        $this->ftp->connect();
        $this->ftp->setPasv();
    }

    private function getMockFunctions()
    {
        $functions = $this->getMock(
            'FtpLib\Functions',
            array(
                'connect', 'ssl_connect', 'login', 'pasv', 'fput', 'delete',
                'mkdir', 'chdir', 'fget', 'rmdir', 'nlist', 'size'
            )
        );

        $functions->expects($this->any())->method('connect')->will($this->returnValue(true));
        $functions->expects($this->any())->method('ssl_connect')->will($this->returnValue(true));
        $functions->expects($this->any())->method('login')->will($this->returnValue(true));
        $functions->expects($this->any())->method('pasv')->will($this->returnValue(true));
        $functions->expects($this->any())->method('fput')->will($this->returnValue(true));
        $functions->expects($this->any())->method('delete')->will($this->returnValue(true));
        $functions->expects($this->any())->method('mkdir')->will($this->returnValue(true));
        $functions->expects($this->any())->method('chdir')->will($this->returnValue(true));
        $functions->expects($this->any())->method('chdir')->will($this->returnValue(true));
        $functions->expects($this->any())->method('rmdir')->will($this->returnValue(true));
        $functions->expects($this->any())->method('size')->will($this->returnValue(1));

        $functions->expects($this->any())->method('nlist')->will(
            $this->returnCallback(
                function () {
                    return array('filename1', 'filename2');
                }
            )
        );

        $functions->expects($this->any())->method('fget')->will(
            $this->returnCallback(
                function ($ftp_stream, $handle, $remote_file, $mode) {
                    $tempHandle = fopen('php://temp', 'w+');
                    fwrite($tempHandle, 'foo', strlen('foo'));
                    rewind($tempHandle);
                    $handle = $tempHandle;
                    // AFAIK this doesn't work. PHP clones the parameters and cannot use references
                    return true;
                }
            )
        );

        return $functions;
    }

    public function testPutAFileFromPath()
    {
        $file = $this->ftp->putFileFromPath(__DIR__ . '/../fixtures/foo');
        $this->assertInstanceOf('FtpLib\File', $file);
        $this->assertEquals('foo', $file->getName(), 'Check file name');
        $this->assertEquals("foo", $file->getContent(), 'Check file content');
        $file->delete();
    }

    public function testPutAFileFromString()
    {
        $file = $this->ftp->putFileFromString('filename', 'bla, bla, bla');
        $this->assertInstanceOf('FtpLib\File', $file);
        $this->assertEquals('filename', $file->getName(), 'Check file name');
        $this->assertEquals('bla, bla, bla', $file->getContent(), 'Check file content');
        $file->delete();
    }

    public function testPutFileIntoDirectory()
    {
        $this->ftp->mkdir("/directory");
        $this->ftp->chdir("/directory");
        $this->ftp->putFileFromPath(__DIR__ . '/../fixtures/foo');

        $file = $this->ftp->getFile('foo');
        $this->assertEquals('foo', $file->getName(), 'Check file name');
        //$this->assertEquals("foo", $file->getContent(), 'Check file content');
        $file->delete();
        $this->ftp->rmdir("/directory");
    }

    public function testListFiles()
    {
        $file = $this->ftp->putFileFromString('filename1', 'bla1, bla1, bla1');
        $file = $this->ftp->putFileFromString('filename2', 'bla2, bla2, bla2');

        $this->assertCount(2, $this->ftp->listFiles());
    }

    public function testGetFiles()
    {
        $this->ftp->putFileFromString('filename1', 'bla1, bla1, bla1');
        $this->ftp->putFileFromString('filename2', 'bla2, bla2, bla2');

        $files = array();
        $this->ftp->getFiles(
            function (File $file) use (&$files) {
                $this->assertInstanceOf('FtpLib\File', $file);
                $files[] = $file;
            }
        );

        $this->assertEquals('filename1', $files[0]->getName(), 'Check file name1');
        //$this->assertEquals('bla1, bla1, bla1', $files[0]->getContent(), 'Check file content');

        $this->assertEquals('filename2', $files[1]->getName(), 'Check file name2');
        //$this->assertEquals('bla2, bla2, bla2', $files[1]->getContent(), 'Check file content');

        $files[0]->delete();
        $files[1]->delete();
    }

    public function testSSlConnection()
    {
        $ftp = new Ftp("ftphost", "username", "password");
        $ftp->setFtpWrapper($this->getMockFunctions());
        $ftp->connectSSL();
        $file = $ftp->putFileFromPath(__DIR__ . '/../fixtures/foo');
        $this->assertInstanceOf('FtpLib\File', $file);
        $file->delete();
    }
}
