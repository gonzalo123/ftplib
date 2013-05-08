<?php
/**
 * This file is part of FtpLib
 *
 * @license GPL
 * @link https://github.com/gonzalo123/ftplib
 * @author Gonzalo Ayuso <gonzalo123@gmail.com>
 */

namespace FtpLib;

/**
 * FTP client
 *
 * @author Gonzalo Ayuso <gonzalo123@gmail.com>
 */
class Ftp
{
    /**
     * The server hostname or address
     *
     * @var string
     */
    private $host;

    /**
     * The username to be authenticated
     *
     * @var string
     */
    private $user;

    /**
     * The user's password
     *
     * @var string
     */
    private $pass;

    /**
     * FTP functions wrapper
     *
     * @var Functions
     */
    private $ftp;

    /**
     * The connection with the server
     *
     * @var resource
     */
    private $conn;

    /**
     * Initialize the object
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     */
    public function __construct($host, $user, $pass)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->ftp = new Functions();
    }

    /**
     * Configures the FTP functions wrapper
     *
     * @param Functions $ftp
     */
    public function setFtpWrapper(Functions $ftp)
    {
        $this->ftp = $ftp;
    }

    /**
     * Establishes a connection with the server
     *
     * @return \FtpLib\Ftp
     */
    public function connect()
    {
        $this->conn = $this->ftp->connect($this->host);
        $this->login();

        return $this;
    }

    /**
     * Authenticate using user credentials
     *
     * @throws Exception When the authentication fails
     */
    protected function login()
    {
        if (!$this->ftp->login($this->conn, $this->user, $this->pass)) {
            throw new Exception("Error connecting to FTP server");
        }
    }

    /**
     * Establishes a secure connection with the server
     *
     * @return \FtpLib\Ftp
     */
    public function connectSSL()
    {
        $this->conn = $this->ftp->ssl_connect($this->host);
        $this->login();

        return $this;
    }

    /**
     * Activates the passive mode
     *
     * @return \FtpLib\Ftp
     */
    public function setPasv()
    {
        $this->ftp->pasv($this->conn, true);

        return $this;
    }

    /**
     * Uploads a file to the server from a string
     *
     * @param string $remoteFileName
     * @param string $content
     * @return \FtpLib\File
     * @throws Exception When the transfer fails
     */
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

    /**
     * Uploads a file to the server
     *
     * @param string $localPath
     * @return \FtpLib\File
     * @throws Exception When the transfer fails
     */
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

    /**
     * Downloads a file from the server
     *
     * @param string $remoteFile
     * @return \FtpLib\File
     * @throws Exception When the download fails
     */
    public function getFile($remoteFile)
    {
        $tempHandle = fopen('php://temp', 'r+');

        if ($this->ftp->fget($this->conn, $tempHandle, $remoteFile, FTP_BINARY)) {
            rewind($tempHandle);
            return new File($this, $remoteFile, stream_get_contents($tempHandle));
        }

        throw new Exception("Error opening the remote file '{$remoteFile}'");
    }

    /**
     * Removes a file from the server
     *
     * @param string $remoteFile
     * @return \FtpLib\Ftp
     * @throws Exception When the delete fails
     */
    public function deleteFile($remoteFile)
    {
        if (!$this->ftp->delete($this->conn, $remoteFile)) {
            throw new Exception("Error deleteting the remote file '{$remoteFile}'");
        }

        return $this;
    }

    /**
     * Changes the current directory on the server
     *
     * @param string $directory
     * @return \FtpLib\Ftp
     * @throws Exception When the change fails
     */
    public function chdir($directory)
    {
        if (!$this->ftp->chdir($this->conn, $directory)) {
            throw new Exception("Error changing the directory '{$directory}'");
        }

        return $this;
    }

    /**
     * Changes to the parent directory
     *
     * @return \FtpLib\Ftp
     * @throws Exception When the change fails
     */
    public function up()
    {
        if (!$this->ftp->cdup($this->conn)) {
            throw new Exception("Error changing to the parent directory");
        }

        return $this;
    }

    /**
     * Creates a directory on the server
     *
     * @param string $directory
     * @return \FtpLib\Ftp
     * @throws Exception When the creation fails
     */
    public function mkdir($directory)
    {
        if (!$this->ftp->mkdir($this->conn, $directory)) {
            throw new Exception("Error creating the directory '{$directory}'");
        }

        return $this;
    }

    /**
     * Removes a directory from the server
     *
     * @param string $directory
     * @return \FtpLib\Ftp
     * @throws Exception When the delete fails
     */
    public function rmdir($directory)
    {
        if (!$this->ftp->rmdir($this->conn, $directory)) {
            throw new Exception("Error deleting the directory '{$directory}'");
        }

        return $this;
    }

    /**
     * Retrieve the file list from a directory
     *
     * @param string $directory
     * @return array
     */
    public function listFiles($directory = '.')
    {
        return $this->ftp->nlist($this->conn, $directory);
    }

    /**
     * Downloads all the files from the directory applying a callback for each one
     *
     * @param \Closure $closure
     * @param string $directory
     * @return \FtpLib\Ftp
     */
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
