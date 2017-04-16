<?php
/**
 * Created by PhpStorm.
 * Date: 3/26/2017
 * Time: 6:11 PM
 */
namespace Voronenko\PHPOTP;
include("Algorithm.php");
include("Logger.php");

/**
 * Class Verificator
 * @package Voronenko\PHPOTP
 *
 * @property string $secret
 * @property string[] $codes
 * @property Algorithm $algorithm
 */
class Verificator
{
    private $secret;
    private $codes;
    private $algorithm;

    public $trace;

    /**
     * Verificator constructor.
     *
     * @param string $secret
     * @param integer $fuzziness - amount of past codes we keep track of.
     */
    public function __construct($secret, $fuzziness = 3)
    {
        Logger::log("Secret key: $secret");
        $this->secret = $secret;

        $key = Base32Static::decode($this->secret);
        Logger::log("Key(base 32 decode): $key");

        $this->algorithm = new Algorithm();
        $unixTimeStamp = floor(time() / 30);
        Logger::log("UnixTimeStamp (time()/30): $unixTimeStamp <br/>");

        for ($i = 0; $i < $fuzziness; $i++) {
            $checktime = $unixTimeStamp - $i;
            Logger::log("Calculating oath_hotp from (int)(unixtimestamp - " . 30 * $i . "sec offset): $checktime basing on secret key");

            $tokenCode = $this->algorithm->oath_hotp($key, $checktime);
            Logger::log("CheckTime: $checktime, oath_hotp: $tokenCode");

            $this->codes[] = $this->algorithm->oath_truncate($tokenCode, 6);
        }
    }

    /**
     * @param string $code
     * @return bool
     */
    public function verify($code)
    {
        return in_array($code, $this->codes);
    }

    /**
     * @return string
     */
    public function getCodes()
    {
        return $this->codes;
    }
}