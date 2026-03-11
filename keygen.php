<?php

class KeyGen{
    private $key;
    private $key_length = 32;

    private $env_path = __DIR__.'/.env';
    
    public function __construct(){
        $this->key = $this->generateSymmetricKey();
    }

    private function generateSymmetricKey(){   
        $random_bytes = random_bytes($this->key_length);
        $converted_key = bin2hex($random_bytes);

        return $converted_key;
    }

    public function getKey(){
        return $this->key;
    }

    public function getEncodedKey(){
        return base64_encode($this->key);
    }

    public function writeEncodedKey(){
        $encoded_key = $this->getEncodedKey();
        $env_content = file_get_contents($this->env_path);

        if(str_contains($env_content, 'SYMM_KEY=')){
            $env_content = preg_replace('/^SYMM_KEY=.*/m', "SYMM_KEY={$encoded_key}", $env_content);
        }else{
            $env_content .= "\nSYMM_KEY={$encoded_key}";
        }

        file_put_contents($this->env_path, $env_content);

        return "Encoded Key written to .env";
    }

    public function readEncodedKey(){
        //get content from env path;
        $env_content = file_get_contents($this->env_path);

        if(str_contains($env_content, 'SYMM_KEY=')){
            //get whole string with symm key and stop at break line \n
            $symm_key = preg_match("/^SYMM_KEY=.*/m", $env_content, $match);
            $key = $match[0];

            //Retrieve the key
            $encoded_key = preg_replace("/^SYMM_KEY=/", '',$key);

            $decoded_key = base64_decode($encoded_key, true);

            return $decoded_key;
        }else{
            throw new Exception('SYMM_KEY not found on .rnv file');
        }
    }
}