PHPOTP
======

PHP Implementation of the OTP algorithm

Two factor authentication in PHP
I hope it could help you to make your applications more secure. Two factor authentication adds one more step into the authentication process and therefore provides a mechanism to provide more security for your systems.

Explain in detail - http://en.wikipedia.org/wiki/Two-factor_authentication

If you are interested in understanding algorythm step by step - you are invited to read article http://www.codeproject.com/Articles/502240/Mysterious-Google-two-step-authentication-in-debug

I will be using PHP in this post, but the same can be implemented in any other programming language.

Required libraries
To simplify the development and not reinvent the wheel, it is always useful to try to find if someone else has implemented it already. For PHP I have adopted: 

a) Base32 implementation for PHP by Bryan Ruiz
b) PHP HMAC hash implementation from community feedbacks on http://php.net/manual/ru/function.hash-hmac.php

in a result proof of concept implementation of RFC6238 have born: 
rfc6238.php which contains helper class TokenAuth6238 with several useful functions

# Generating a secret
A secret is used to provide a base for your application and the device generating the code to validate the user's identity. The secret is important and should be transfered over a secured channel. If attacker will get access to the secret, it's possible to generate the verification code and get around the security procedure.

secret = Base32Static::encode("yourrandomsecretkey")

#Google authenticator
Google provides Android and iPhone application that generates the verification code for the user.

Install the application and create new account by entering the a code. Name your account as you want and enter the secret generated in the previous step. Choose time based token.

Now you can see on you smartphone 6 character long password that allows you to validate the user's identity.

#Validating the integrity
Now that we have the secret and the smartphone is generating the verification code, let's try to validate the it.

```php
<?php
	require_once("rfc6238.php");
	
	$secretkey = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';  //your secret code
	$currentcode = '571427';  //code to validate, for example received from device


	
	if (TokenAuth6238::verify($secretkey,$currentcode))
	{
		echo "Code is valid\n";
	}
	else
	{
		echo "Invalid code\n";
	}
```

When you run such a script and you put in the correct secret and correct verification code, it will print "Code is valid" or "Invalid code" on the standard output.

# Generating the code
You can also generate the verification code yourself using the library.

```print TokenAuth6238::getTokenCodeDebug($secretkey,0);```


# Generating the QRCode for GOOGLE Authenticator
You can also generate the image that can be used by mobile device to configure authentication program

```print sprintf('<img src="%s"/>',TokenAuth6238::getBarCodeUrl('','',$secretkey));```

Parameters for getBarCodeUrl are as follows:
```php
/**
 * @param $username (Optional) The specified username on the website
 * @param $domain (Optional) The domain/web site this 2FA is used on
 * @param $secretkey The secret key
 * @param $issuer (Optional) The company or app this code is for (app name? company name?)
 * @return return a Google charts QR Code image link for displaying to users
 */
TokenAuth6238::getBarCodeUrl($username, $domain, $secretkey, $issuer)
```

# Conclusion
Using this few simple steps, you can add additional validation layer into your authentication process in your application and thus provide higher security for your users.
