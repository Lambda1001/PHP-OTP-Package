<?php

require 'keygen.php';

$keyGen = new KeyGen();

$key = $keyGen->readEncodedKey();
echo $key;
