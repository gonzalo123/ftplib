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
 * Wrapper class for PHP FTP functions
 *
 * @author Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * @method bool alloc() alloc(resource $ftp_stream, int $filesize, string &$result = null) Allocates space for a file to be uploaded
 * @method bool cdup() cdup(resource $ftp_stream) Changes to the parent directory
 * @method bool chdir() chdir(resource $ftp_stream, string $directory) Changes the current directory on a FTP server
 * @method int chmod() chmod(resource $ftp_stream, int $mode, string $filename) Set permissions on a file via FTP
 * @method bool close() close(resource $ftp_stream) Closes an FTP connection
 * @method resource connect() connect(string $host, int $port = 21, int $timeout = 90) Opens an FTP connection
 * @method bool delete() delete(resource $ftp_stream, string $path) Deletes a file on the FTP server
 * @method bool exec() exec(resource $ftp_stream, string $command) Requests execution of a command on the FTP server
 * @method bool fget() fget(resource $ftp_stream, resource $handle, string $remote_file, int $mode, int $resumepos = 0) Downloads a file from the FTP server and saves to an open file
 * @method bool fput() fput(resource $ftp_stream, string $remote_file, resource $handle, int $mode, int $startpos = 0) Uploads from an open file to the FTP server
 * @method mixed get_option() get_option(resource $ftp_stream, int $option) Retrieves various runtime behaviours of the current FTP stream
 * @method bool get() get(resource $ftp_stream, string $local_file, string $remote_file, int $mode, int $resumepos = 0) Downloads a file from the FTP server
 * @method bool login() login(resource $ftp_stream, string $username, string $password) Logs in to an FTP connection
 * @method int mdtm() mdtm(resource $ftp_stream, string $remote_file) Returns the last modified time of the given file
 * @method string mkdir() mkdir(resource $ftp_stream, string $directory) Creates a directory
 * @method int nb_continue() nb_continue(resource $ftp_stream) Continues retrieving/sending a file (non-blocking)
 * @method int nb_fget() nb_fget(resource $ftp_stream, resource $handle, string $remote_file, int $mode, int $resumepos = 0) Retrieves a file from the FTP server and writes it to an open file (non-blocking)
 * @method int nb_fput() nb_fput(resource $ftp_stream, string $remote_file, resource $handle, int $mode, int $startpos = 0) Stores a file from an open file to the FTP server (non-blocking)
 * @method int nb_get() nb_get(resource $ftp_stream, string $local_file, string $remote_file, int $mode, int $resumepos = 0) Retrieves a file from the FTP server and writes it to a local file (non-blocking)
 * @method int nb_put() nb_put(resource $ftp_stream, string $remote_file, string $local_file, int $mode, int $startpos = 0) Stores a file on the FTP server (non-blocking)
 * @method array nlist() nlist(resource $ftp_stream, string $directory) Returns a list of files in the given directory
 * @method bool pasv() pasv(resource $ftp_stream, bool $pasv) Turns passive mode on or off
 * @method bool put() put(resource $ftp_stream, string $remote_file, string $local_file, int $mode, int $startpos = 0) Uploads a file to the FTP server
 * @method string pwd() pwd(resource $ftp_stream) Returns the current directory name
 * @method bool quit() quit(resource $ftp_stream) Closes an FTP connection
 * @method array raw() raw(resource $ftp_stream, string $command) Sends an arbitrary command to an FTP server
 * @method array rawlist() rawlist(resource $ftp_stream, string $directory, bool $recursive = false) Returns a detailed list of files in the given directory
 * @method bool rename() rename(resource $ftp_stream, string $oldname, string $newname) Renames a file or a directory on the FTP server
 * @method bool rmdir() rmdir(resource $ftp_stream, string $directory) Removes a directory
 * @method bool set_option() set_option(resource $ftp_stream, int $option, mixed $value) Set miscellaneous runtime FTP options
 * @method bool site() site(resource $ftp_stream, string $command) Sends a SITE command to the server
 * @method int size() size(resource $ftp_stream, string $remote_file) Returns the size of the given file
 * @method resource ssl_connect() ssl_connect(string $host, int $port = 21, int $timeout = 90) Opens an Secure SSL-FTP connection
 * @method string systype() systype(resource $ftp_stream) Returns the system type identifier of the remote FTP server
 */
class Functions
{
    /**
     * Redirect the method call to FTP functions
     *
     * @param string $function
     * @param array $arguments
     * @return mixed
     * @throws Exception When the function is not valid
     */
    public function __call($function, array $arguments)
    {
        $function = 'ftp_' . $function;

        if (function_exists($function)) {
            return call_user_func_array($function, $arguments);
        }

        throw new Exception("{$function} is not a valid FTP function");
    }
}
