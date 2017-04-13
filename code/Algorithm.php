<?php

/**
 * Created by PhpStorm.
 * Date: 3/26/2017
 * Time: 4:09 PM
 *
 * @author Vyacheslav Voronenko
 */
namespace AshaTob\GoogleAuth;
include("Base32Static.php");

class Algorithm
{
    public function oath_hotp($key, $counter)
    {
        $cur_counter = [0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 7; $i >= 0; $i--) { // C for unsigned char, * for  repeating to the end of the input data
            $cur_counter[$i] = pack('C*', $counter);
            $counter = $counter >> 8;
        }
        $binary = implode($cur_counter);
        str_pad($binary, 8, chr(0), STR_PAD_LEFT);
        $result = hash_hmac('sha1', $binary, $key);

        return $result;
    }

    public function oath_truncate($hash, $length = 6)
    {
        $result = "";
        $hashcharacters = str_split($hash, 2);
        for ($j = 0; $j < count($hashcharacters); $j++) {
            $hmac_result[] = hexdec($hashcharacters[$j]);
        }
        $offset = $hmac_result[19] & 0xf;
        $result = (
                (($hmac_result[$offset + 0] & 0x7f) << 24) |
                (($hmac_result[$offset + 1] & 0xff) << 16) |
                (($hmac_result[$offset + 2] & 0xff) << 8) |
                ($hmac_result[$offset + 3] & 0xff)
            ) % pow(10, $length);

        return $result;
    }
}