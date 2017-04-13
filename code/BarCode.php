<?php
/**
 * Created by PhpStorm.
 * Date: 3/26/2017
 * Time: 6:15 PM
 */
namespace AshaTob\GoogleAuth;
/**
 * Class BarCode
 * @package AshaTob\GoogleAuth
 */
class BarCode
{
    const base = "http://chart.apis.google.com/chart";

    /**
     * @param string $username
     * @param string $domain
     * @param string $secretkey
     * @param string $issuer
     * @return string
     */
    public static function generate($username, $domain, $secretkey, $issuer)
    {
        $url = self::base . "?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/" . $username . "@" . $domain . "%3Fsecret%3D" . $secretkey . '%26issuer%3D' . rawurlencode($issuer);
        return "<img src=$url alt=$secretkey />";
    }
}