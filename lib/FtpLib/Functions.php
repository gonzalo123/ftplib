<?php
namespace FtpLib;

class Functions
{
    public function __call($function, $arguments)
    {
        $function = 'ftp_' . $function;

        if (function_exists($function)) {
            return call_user_func_array($function, $arguments);
        } else {
            throw new Exception("{$function} is not a valid FTP function");
        }
    }
}
