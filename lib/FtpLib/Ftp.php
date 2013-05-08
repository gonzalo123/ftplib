<?php
namespace FtpLib;

class Ftp
{
    private $host;
    private $user;
    private $pass;
    private $ftp;
    private $conn;

    public function __construct($host, $user, $pass)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->ftp = new Functions();
    }

    public function setFtpWrapper($ftp)
    {
        $this->ftp = $ftp;
    }

    public function connect()
    {
        $this->conn = $this->ftp->connect($this->host);
        $this->login();

        return $this;
    }

    protected function login()
    {
        if (!$this->ftp->login($this->conn, $this->user, $this->pass)) {
            throw new Exception("Error connecting to FTP server");
        }
    }

    public function connectSSL()
    {
        $this->conn = $this->ftp->ssl_connect($this->host);
        $this->login();

        return $this;
    }

    public function setPasv()
    {
        $this->ftp->pasv($this->conn, true);

        return $this;
    }

    public function putFileFromString($remoteFileName, $content)
    {
        $tempHandle = fopen('php://temp', 'w');
        fwrite($tempHandle, $content, strlen($content));
        rewind($tempHandle);

        if ($this->ftp->fput($this->conn, $remoteFileName, $tempHandle, FTP_BINARY)) {
            return new File($this, $remoteFileName, $content);
        }

        throw new Exception("Error when put the remote file '{$remoteFileName}'");
    }

    public function putFileFromPath($localPath)
    {
        $remoteFile = basename($localPath);
        $tempHandle = fopen($localPath, 'r');

        if ($this->ftp->fput($this->conn, $remoteFile, $tempHandle, FTP_BINARY)) {
            rewind($tempHandle);
            return new File($this, $remoteFile, stream_get_contents($tempHandle));
        }

        throw new Exception("Error when put the remote file From Path'{$localPath}'");
    }

    public function getFile($remoteFile)
    {
        $tempHandle = fopen('php://temp', 'r+');

        if ($this->ftp->fget($this->conn, $tempHandle, $remoteFile, FTP_BINARY)) {
            rewind($tempHandle);
            return new File($this, $remoteFile, stream_get_contents($tempHandle));
        }

        throw new Exception("Error opening the remote file '{$remoteFile}'");
    }

    public function deleteFile($remoteFile)
    {
        if (!$this->ftp->delete($this->conn, $remoteFile)) {
            throw new Exception("Error deleteting the remote file '{$remoteFile}'");
        }

        return $this;
    }

    public function chdir($directory)
    {
        if (!$this->ftp->chdir($this->conn, $directory)) {
            throw new Exception("Error changing the directory '{$directory}'");
        }

        return $this;
    }

    public function mkdir($directory)
    {
        if (!$this->ftp->mkdir($this->conn, $directory)) {
            throw new Exception("Error creating the directory '{$directory}'");
        }

        return $this;
    }

    public function rmdir($directory)
    {
        if (!$this->ftp->rmdir($this->conn, $directory)) {
            throw new Exception("Error deleting the directory '{$directory}'");
        }

        return $this;
    }

    public function listFiles($directory = '.')
    {
        return $this->ftp->nlist($this->conn, $directory);
    }

    public function getFiles(\Closure $closure, $directory = '.')
    {
        $files = $this->listFiles($directory);

        foreach ($files as $fileName) {
            if ($this->ftp->size($this->conn, $fileName) != '-1') {
                $closure($this->getFile($fileName));
            }
        }

        return $this;
    }
}
