<?php

require_once 'HOTP.php';

$hotp_algo = new HOTP();

$string = $hotp_algo->getHOTPValue();

echo $string;
