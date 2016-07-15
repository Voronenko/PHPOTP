<?php
	require_once('rfc6238.php');
	
	$secretkey = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';
	$currentcode = '571427';

	if (TokenAuth6238::verify($secretkey,$currentcode)) {
		echo "Code is valid\n";
	} else {
		echo "Invalid code\n";
	}

  print sprintf('<img src="%s"/>',TokenAuth6238::getBarCodeUrl('','',$secretkey,'My%20App'));
  print TokenAuth6238::getTokenCodeDebug($secretkey,0); 