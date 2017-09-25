<?php
/**
 *
 */
require __DIR__."/base32.php";
require __DIR__."/tokenAuth.php";

$secretkey = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';
$currentcode = '34890';

if (\phpotp\voronenko\tokenAuth::verify($secretkey,$currentcode)) {
    echo "Code is valid\n";
} else {
    echo "Invalid code\n";
}

print sprintf('<img src="%s"/>',\phpotp\voronenko\tokenAuth::getBarCodeUrl('','',$secretkey,'My%20App'));
print \phpotp\voronenko\tokenAuth::getTokenCode($secretkey,0);