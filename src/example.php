<?php
/**
 *
 */
require __DIR__."/base32.php";
require __DIR__."/tokenAuth.php";

$secretkey = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';
$currentcode = '34890';

if (\voronenko\phpotp\tokenAuth::verify($secretkey,$currentcode)) {
    echo "Code is valid\n";
} else {
    echo "Invalid code\n";
}

print sprintf('<img src="%s"/>',\voronenko\phpotp\tokenAuth::getBarCodeUrl('','',$secretkey,'My%20App'));
print \voronenko\phpotp\tokenAuth::getTokenCode($secretkey,0);