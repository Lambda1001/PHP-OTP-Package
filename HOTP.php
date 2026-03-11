<?php

require_once 'keygen.php';

class HOTP{
    private $keygenService;
    private $counter = 12314141;
    private $digits = 6;

    public function __construct(){
        $this->keygenService = new KeyGen();
    }

    public function getHOTPValue(){
        $key = $this->keygenService->readEncodedKey();
        $string = $this->generateHashValue($key, $this->counter);
        
        $SBits = $this->dynamicTruncation($string);

        $otp_value = $SBits % pow(10, $this->digits);

        return $otp_value;
    }

    private function generateHashValue($key, $counter){
        $output = hash_hmac('sha1', $counter, $key, true);
        return $output;
    }

    private function dynamicTruncation($hash_value){
        /**
         * Take last byte of $hash_value and perform a bitwise AND operation with 0x0F(decimal 15) to calculate an offset;
         * The offset is a 4bit integer derived fro last byte of HMAC-SHA-1 result.
        **/
        $offsetBits = ord($hash_value[19]) & 0x0F;
        
        echo($offsetBits);
        $truncated = ((ord($hash_value[$offsetBits]) & 0x7F) << 24) |
                    ((ord($hash_value[$offsetBits + 1]) & 0xFF) << 16) |
                    ((ord($hash_value[$offsetBits + 2]) & 0xFF) << 8) |
                    (ord($hash_value[$offsetBits + 3]) & 0xFF);


        return $truncated;
    }
}
