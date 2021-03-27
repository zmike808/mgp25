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
        if (isset(self::$logger)) {
            $method = $method.':  ';
            self::$logger->debug($method.$endpoint);
        } else {
            if (PHP_SAPI === 'cli') {
                $method = Utils::colouredString("{$method}:  ", 'light_blue');
            } else {
                $method = $method.':  ';
            }
            echo $method.$endpoint."\n";
        }
    }

    public static function printUpload(
        $uploadBytes)
    {
        if (isset(self::$logger)) {
            $dat = '→ '.$uploadBytes;
            self::$logger->debug($dat);
        } else {
            if (PHP_SAPI === 'cli') {
                $dat = Utils::colouredString('→ '.$uploadBytes, 'yellow');
            } else {
                $dat = '→ '.$uploadBytes;
            }
            echo $dat."\n";
        }
    }

    public static function printHttpCode(
        $httpCode,
        $bytes)
    {
        if (isset(self::$logger)) {
            $out = "← {$httpCode} \t {$bytes}";
            self::$logger->debug($out);
        } else {
            if (PHP_SAPI === 'cli') {
                $out = Utils::colouredString("← {$httpCode} \t {$bytes}", 'green');
            } else {
                $out = "← {$httpCode} \t {$bytes}";
            }
            echo $out."\n";
        }
    }

    public static function printResponse(
        $response,
        $truncated = false)
    {
        if ($truncated && mb_strlen($response, 'utf8') > 1000) {
            $response = mb_substr($response, 0, 1000, 'utf8').'...';
        }

        if (isset(self::$logger)) {
            $res = 'RESPONSE: ';
            self::$logger->debug($res.$response);
        } else {
            if (PHP_SAPI === 'cli') {
                $res = Utils::colouredString('RESPONSE: ', 'cyan');
            } else {
                $res = 'RESPONSE: ';
            }
            echo $res.$response."\n\n";
        }
    }

    public static function printPostData(
        $post)
    {
        $gzip = mb_strpos($post, "\x1f"."\x8b"."\x08", 0, 'US-ASCII') === 0;
        $out = urldecode(($gzip ? zlib_decode($post) : $post));

        if (isset(self::$logger)) {
            $dat = 'DATA: ';
            self::$logger->debug($dat.$out);
        } else {
            if (PHP_SAPI === 'cli') {
                $dat = Utils::colouredString(($gzip ? 'DECODED ' : '').'DATA: ', 'yellow');
            } else {
                $dat = 'DATA: ';
            }
            echo $dat.$out."\n";
        }
    }
}
