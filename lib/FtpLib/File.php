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
 * File that has been uploaded/download to/from the FTP Server
 *
 * @author Gonzalo Ayuso <gonzalo123@gmail.com>
 */
class File
{
    /**
     * The filename
     *
     * @var string
     */
    private $name;

    /**
     * The contents of the file
     *
     * @var string
     */
    private $content;

    /**
     * The Ftp connection
     *
     * @var Ftp
     */
    private $ftp;

    /**
     * Initialize the object
     *
     * @param Ftp $ftp
     * @param string $name
     * @param string $content
     */
    public function __construct(Ftp $ftp, $name, $content)
    {
        $this->name = $name;
        $this->content = $content;
        $this->ftp = $ftp;
    }

    /**
     * Returns the filename
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the file content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Removes the file from the FTP server
     */
    public function delete()
    {
        $this->ftp->deleteFile($this->name);
    }
}
