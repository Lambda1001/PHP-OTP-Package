<?php

require_once 'keygen.php';

class HOTP{
    private $keygenService;
    private $lookahead_window = 5;
    private $digits = 6;

    public function __construct(){
        $this->keygenService = new KeyGen();
    }

    public function getHOTPValue($counter){
        $key = $this->keygenService->readEncodedKey();
        $string = $this->generateHashValue($key, $counter);
        
        $SBits = $this->dynamicTruncation($string);

        $otp_value = $SBits % pow(10, $this->digits);

        return str_pad($otp_value, $this->digits, 0, STR_PAD_LEFT);
    }

    private function generateHashValue($key, $counter){
        $output = hash_hmac('sha1', $counter, $key, true);
        return $output;
    }

    /**
     * Dynamic truncation of the HMAC-SHA-1 value to generate a 4byte dynamic binary code from the 160 bit value.
     * @param string $hash_value HMAC-SHA-1 Value
     * @return int 31-bit $truncated value
     * 
     */

    private function dynamicTruncation($hash_value){
        $offset = ord($hash_value[19]) & 0x0F;
        
        $truncated = ((ord($hash_value[$offset]) & 0x7F) << 24) |
                    ((ord($hash_value[$offset + 1]) & 0xFF) << 16) |
                    ((ord($hash_value[$offset + 2]) & 0xFF) << 8) |
                    (ord($hash_value[$offset + 3]) & 0xFF);

        return $truncated;
    }


    public function verifyOTP($clientOTP, $counter){
        $output = [];

        for($i = $counter; $i <= $counter + $this->lookahead_window; ++$i){
            if(hash_equals($this->getHOTPValue($i), $clientOTP)){
                ++$counter;

                return $output = [
                    'verified' => true,
                    'message' => 'User successfully authenticated',
                ];
            }
        }

        return $output = [
            'verified' => false,
            'message' => 'Error during authentication',
        ];
    }
}
