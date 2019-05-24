<?php
namespace Codenation;
class Codenation
{
    private $letters = 'abcdefghijklmnopqrstuvwxyz';
    public $house_numbers = 9;
    public $encrypted = 'r uxen mnjmurwnb. r urtn cqn fqxxbqrwp bxdwm cqnh vjtn jb cqnh ouh kh. mxdpujb jmjvb';
    public $deciphered = '';
    public $cryptographic_summary = '';
    public function decipher()
    {
        for ($i=0; $i < strlen($this->encrypted); $i++) { 
            if(in_array($this->encrypted[$i], str_split($this->letters))){
                $pos = ( (strripos($this->letters,$this->encrypted[$i]) - $this->house_numbers) % 26);
                $this->deciphered .= $this->letters[$pos];
            }else{
                $this->deciphered .= $this->encrypted[$i];
            }
        }
    }
    public function generate_summary()
    { 
        $this->cryptographic_summary = sha1($this->deciphered);
    }
}
$codenation = new Codenation();
$codenation->decipher();
$codenation->generate_summary();
echo json_encode($codenation);

