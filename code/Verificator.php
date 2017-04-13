<?php
/**
 * Created by PhpStorm.
 * Date: 3/26/2017
 * Time: 6:11 PM
 */
namespace AshaTob\GoogleAuth;
include("Algorithm.php");
include("Logger.php");

/**
 * Class Verificator
 * @package AshaTob\GoogleAuth
 *
 * @property string    $secret
 * @property string[]  $codes
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
     * @param string  $secret
	 * @param integer $fuzziness - amount of past codes we keep track of.
     */
    public function __construct($secret, $fuzziness = 3)
    {
        $this->secret = $secret;
        $key = Base32Static::decode($this->secret);
		$this->algorithm = new Algorithm();
		for($i = 0; $i < $fuzziness; $i++) {
			$checktime = floor(time() / 30) - $i;
			$tokenCode = $this->algorithm->oath_hotp($key, $checktime);
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