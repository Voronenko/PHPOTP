<?php

/**
 * Created by PhpStorm.
 * Date: 3/26/2017
 * Time: 4:09 PM
 *
 * @author Vyacheslav Voronenko
 */
namespace Voronenko\PHPOTP;
include("Base32Static.php");

class Algorithm
{
    public function oath_hotp($key, $counter)
    {
        $cur_counter = [0, 0, 0, 0, 0, 0, 0, 0];
        Logger::log("Packing counter $counter (" . dechex($counter) . ") into binary string - pay attention to hex representation of key and binary representation");

        for ($i = 7; $i >= 0; $i--) {
            // C for unsigned char, * for  repeating to the end of the input data
            $cur_counter[$i] = pack('C*', $counter);
            Logger::log(" - " . $cur_counter[$i] . "(" . dechex(ord($cur_counter[$i])) . ")" . " from $counter");
            $counter = $counter >> 8;
        }
        $logMessage = "";
        foreach ($cur_counter as $char) {
            $logMessage .= ord($char) . " ";
        }
        Logger::log($logMessage);
        $binary = implode($cur_counter);
        str_pad($binary, 8, chr(0), STR_PAD_LEFT);

        Logger::log("Prior to HMAC calculation pad with zero on the left until 8 characters.");
        Logger::log("Calculate sha1 HMAC(Hash-based Message Authentication Code http://en.wikipedia.org/wiki/HMAC).");
        Logger::log("hash_hmac ('sha1', $binary, $key)");

        $result = hash_hmac('sha1', $binary, $key);

        Logger::log("Result: $result");

        return $result;
    }

    public function oath_truncate($hash, $length = 6)
    {
        Logger::log("Converting hex hash into characters");
        $hashcharacters = str_split($hash, 2);
        $hmac_result = [];
        for ($j = 0; $j < count($hashcharacters); $j++) {
            $hmac_result[] = hexdec($hashcharacters[$j]);
        }
        Logger::log("Hash characters and convert to decimals: " . json_encode($hmac_result));
        $offset = $hmac_result[19] & 0xf;

        Logger::log("Calculating offset as 19th element of hmac:" . $hmac_result[19]);
        Logger::log("Offset:" . $offset);

        $result = (
                (($hmac_result[$offset + 0] & 0x7f) << 24) |
                (($hmac_result[$offset + 1] & 0xff) << 16) |
                (($hmac_result[$offset + 2] & 0xff) << 8) |
                ($hmac_result[$offset + 3] & 0xff)
            ) % pow(10, $length);
        Logger::log("Resulting code:" . $result);
        Logger::log("=====================================================");

        return $result;
    }
}