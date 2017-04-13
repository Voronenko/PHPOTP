<?php
/**
 * Created by PhpStorm.
 * Date: 3/26/2017
 * Time: 6:11 PM
 */
namespace AshaTob\GoogleAuth;
include("Algorithm.php");

/**
 * Class Verificator
 * @package AshaTob\GoogleAuth
 *
 * @property string $secret
 * @property string $code
 * @property Algorithm $algorithm
 */
class Verificator
{
    private $secret;
    private $code;
    private $algorithm;

    /**
     * Verificator constructor.
     *
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
        $this->algorithm = new Algorithm();
        $key = Base32Static::decode($this->secret);
        $checktime = floor(time() / 30);
        $thiskey = $this->algorithm->oath_hotp($key, $checktime);
        $this->code = $this->algorithm->oath_truncate($thiskey, 6);
    }

    /**
     * @param string $code
     * @return bool
     */
    public function verify($code)
    {
        return $this->code == $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}