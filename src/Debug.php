<?php

namespace InstagramAPI;

use Psr\Log\LoggerInterface;

class Debug
{
    private static $logger;

    public static function setLogger(
        LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    public static function printRequest(
        $method,
        $endpoint)
    {
        if (PHP_SAPI === 'cli') {
            $method = Utils::colouredString("{$method}:  ", 'light_blue');
        } else {
            $method = $method.':  ';
        }
        
        if (isset(self::$logger)) {
            self::$logger->debug($method.$endpoint);
        } else {
            echo $method.$endpoint."\n";
        }
    }

    public static function printUpload(
        $uploadBytes)
    {
        if (PHP_SAPI === 'cli') {
            $dat = Utils::colouredString('→ '.$uploadBytes, 'yellow');
        } else {
            $dat = '→ '.$uploadBytes;
        }

        if (isset(self::$logger)) {
            self::$logger->debug($dat);
        } else {
            echo $dat."\n";
        }
    }

    public static function printHttpCode(
        $httpCode,
        $bytes)
    {
        if (PHP_SAPI === 'cli') {
            $out = Utils::colouredString("← {$httpCode} \t {$bytes}", 'green');
        } else {
            $out = "← {$httpCode} \t {$bytes}";
        }
        
        if (isset(self::$logger)) {
            self::$logger->debug($out);
        } else {
            echo $out."\n";
        }
    }

    public static function printResponse(
        $response,
        $truncated = false)
    {
        if (PHP_SAPI === 'cli') {
            $res = Utils::colouredString('RESPONSE: ', 'cyan');
        } else {
            $res = 'RESPONSE: ';
        }
        if ($truncated && mb_strlen($response, 'utf8') > 1000) {
            $response = mb_substr($response, 0, 1000, 'utf8').'...';
        }

        if (isset(self::$logger)) {
            self::$logger->debug($res.$response);
        } else {
            echo $res.$response."\n\n";
        }
    }

    public static function printPostData(
        $post)
    {
        $gzip = mb_strpos($post, "\x1f"."\x8b"."\x08", 0, 'US-ASCII') === 0;
        if (PHP_SAPI === 'cli') {
            $dat = Utils::colouredString(($gzip ? 'DECODED ' : '').'DATA: ', 'yellow');
        } else {
            $dat = 'DATA: ';
        }

        $out = $dat.urldecode(($gzip ? zlib_decode($post) : $post));

        if (isset(self::$logger)) {
            self::$logger->debug($out);
        } else {
            echo $out."\n";
        }
    }
}
