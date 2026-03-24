<?php

require_once 'HOTP.php';

$counter = 2334;

$hotp_algo = new HOTP();

$string = $hotp_algo->getHOTPValue($counter);

$output  = $hotp_algo->verifyOTP($string, $counter);

print_r($output);
