<?php
namespace FtpLib;

class File
{
    private $name, $content, $ftp;

    public function __construct($ftp, $name, $content)
    {
        $this->name    = $name;
        $this->content = $content;
        $this->ftp     = $ftp;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function delete()
    {
        $this->ftp->deleteFile($this->name);
    }
}