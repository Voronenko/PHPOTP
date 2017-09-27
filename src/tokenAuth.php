<?php
/**
 * http://www.faqs.org/rfcs/rfc6238.html
 *
 * @author Vyacheslav Voronenko git@voronenko.info.
 *
 * MIT License
 * Copyright (c) 2014 Vyacheslav Voronenko git@voronenko.info

 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace voronenko\phpotp;


class tokenAuth
{
    /**
     * verify
     *
     * @param string $secret_key Secret clue (base 32).
     * @param $code
     * @param int $range_in_30s
     * @return bool True if success, false if failure
     */
    public static function verify($secret_key, $code, $range_in_30s = 3)
    {
        $key = base32::decode($secret_key);
        $unix_time_stamp = time() / 30;

        for ($i = -($range_in_30s); $i <= $range_in_30s; $i++) {
            $check_time = (int)($unix_time_stamp + $i);
            $this_key = self::oath_hotp($key, $check_time);

            if ((int)$code == self::oath_truncate($this_key, 6)) {
                return true;
            }

        }
        return false;
    }


    /**
     * @Method
     * @Description Returns the generated token code
     * @param $secret_key
     * @param int $range_in_30s
     * @return string
     */
    public static function getTokenCode($secret_key, $range_in_30s = 3)
    {
        $result = "";
        $key = base32::decode($secret_key);
        $unix_time_stamp = time() / 30;

        for ($i = -($range_in_30s); $i <= $range_in_30s; $i++) {
            $check_time = (int)($unix_time_stamp + $i);
            $this_key = self::oath_hotp($key, $check_time);
            $result = $result . " # " . self::oath_truncate($this_key, 6);
        }

        return $result;
    }

    /**
     * @Method getTokenCodeDebug()
     * @Description Only use for Debugging purpose
     * @param $secret_key
     * @param int $range_in_30s
     * @return string
     */
    public static function getTokenCodeDebug($secret_key, $range_in_30s = 3)
    {
        $result = "";
        print "<br/>SecretKey: $secret_key <br/>";

        $key = base32::decode($secret_key);
        print "Key(base 32 decode): $key <br/>";

        $unix_time_stamp = time() / 30;
        print "UnixTimeStamp (time()/30): $unix_time_stamp <br/>";

        for ($i = -($range_in_30s); $i <= $range_in_30s; $i++) {
            $check_time = (int)($unix_time_stamp + $i);
            print "Calculating oath_hotp from (int)(unixtimestamp +- 30sec offset): $check_time basing on secret key<br/>";

            $this_key = self::oath_hotp($key, $check_time, true);
            print "======================================================<br/>";
            print "CheckTime: $check_time oath_hotp:" . $this_key . "<br/>";

            $result = $result . " # " . self::oath_truncate($this_key, 6, true);
        }

        return $result;
    }

    public static function getBarCodeUrl($username, $domain, $secret_key, $issuer)
    {
        $url = "http://chart.apis.google.com/chart";
        $url = $url . "?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/";
        $url = $url . $username . "@" . $domain . "%3Fsecret%3D" . $secret_key . '%26issuer%3D' . rawurlencode($issuer);

        return $url;
    }

    public static function generateRandomClue($length = 16)
    {
        $b32 = "234567QWERTYUIOPASDFGHJKLZXCVBNM";
        $s = "";

        for ($i = 0; $i < $length; $i++)
            $s .= $b32[rand(0, 31)];

        return $s;
    }

    private static function hotp_tobytestream($key)
    {
        $result = array();
        $last = strlen($key);
        for ($i = 0; $i < $last; $i = $i + 2) {
            $x = $key[$i] + $key[$i + 1];
            $x = strtoupper($x);
            $x = hexdec($x);
            $result = $result . chr($x);
        }

        return $result;
    }

    /**
     * @Method
     * @Description
     * @param $key
     * @param $counter
     * @param bool $debug
     * @return string
     */
    private static function oath_hotp($key, $counter, $debug = false)
    {
        $result = "";
        $orgcounter = $counter;
        $cur_counter = array(0, 0, 0, 0, 0, 0, 0, 0);

        if ($debug) {
            print "Packing counter $counter (" . dechex($counter) . ")into binary string - pay attention to hex representation of key and binary representation<br/>";
        }

        for ($i = 7; $i >= 0; $i--) { // C for unsigned char, * for  repeating to the end of the input data
            $cur_counter[$i] = pack('C*', $counter);

            if ($debug) {
                print $cur_counter[$i] . "(" . dechex(ord($cur_counter[$i])) . ")" . " from $counter <br/>";
            }

            $counter = $counter >> 8;
        }

        if ($debug) {
            foreach ($cur_counter as $char) {
                print ord($char) . " ";
            }

            print "<br/>";
        }

        $binary = implode($cur_counter);

        // Pad to 8 characters
        str_pad($binary, 8, chr(0), STR_PAD_LEFT);

        if ($debug) {
            print "Prior to HMAC calculation pad with zero on the left until 8 characters.<br/>";
            print "Calculate sha1 HMAC(Hash-based Message Authentication Code http://en.wikipedia.org/wiki/HMAC).<br/>";
            print "hash_hmac ('sha1', $binary, $key)<br/>";
        }

        $result = hash_hmac('sha1', $binary, $key);

        if ($debug) {
            print "Result: $result <br/>";
        }

        return $result;
    }

    /**
     * @Method
     * @Description
     * @param $hash
     * @param int $length
     * @param bool $debug
     * @return int|string
     */
    private static function oath_truncate($hash, $length = 6, $debug = false)
    {
        $result = "";

        // Convert to dec
        if ($debug) {
            print "converting hex hash into characters<br/>";
        }

        $hash_characters = str_split($hash, 2);

        if ($debug) {
            print_r($hash_characters);
            print "<br/>and convert to decimals:<br/>";
        }

        for ($j = 0; $j < count($hash_characters); $j++) {
            $hmac_result[] = hexdec($hash_characters[$j]);
        }

        if ($debug) {
            print_r($hmac_result);
        }

        // http://php.net/manual/ru/function.hash-hmac.php
        // adopted from brent at thebrent dot net 21-May-2009 08:17 comment

        $offset = $hmac_result[19] & 0xf;

        if ($debug) {
            print "Calculating offset as 19th element of hmac:" . $hmac_result[19] . "<br/>";
            print "offset:" . $offset;
        }

        $result = (
                (($hmac_result[$offset + 0] & 0x7f) << 24) |
                (($hmac_result[$offset + 1] & 0xff) << 16) |
                (($hmac_result[$offset + 2] & 0xff) << 8) |
                ($hmac_result[$offset + 3] & 0xff)
            ) % pow(10, $length);

        return $result;
    }
}