<?php
namespace Codenation;

class Codenation
{
    private $letters = 'abcdefghijklmnopqrstuvwxyz';
    public $numero_casas = 0;
    public $token = '';
    public $cifrado = '';
    public $decifrado = '';
    public $resumo_criptografico = '';

    public function __construct()
    {
        $json = file_get_contents('answer.json', 0, null, null);
        $json = json_decode($json);
        $this->numero_casas = $json->numero_casas;
        $this->token = $json->token;
        $this->cifrado = $json->cifrado;
    }

    public function decipher()
    {
        for ($i = 0; $i < strlen($this->cifrado); $i++) {
            if (in_array($this->cifrado[$i], str_split($this->letters))) {
                $pos = ((strripos($this->letters, $this->cifrado[$i]) - $this->numero_casas) % 26);
                $this->decifrado .= $this->letters[$pos];
            } else {
                $this->decifrado .= $this->cifrado[$i];
            }
        }
    }
    public function generateSummary()
    {
        $this->resumo_criptografico = sha1($this->decifrado);
    }

    public function sendFile()
    {
        $file_url = "answer.json";  //here is the file route, in this case is on same directory but you can set URL too like "http://examplewebsite.com/answer.json"
        $eol = "\r\n"; //default line-break for mime type
        $BOUNDARY = md5(time()); //random boundaryid, is a separator for each param on my post curl function
        $BODY = ""; //init my curl body
        $BODY .= '--' . $BOUNDARY . $eol; //start param header
        $BODY .= 'Content-Disposition: form-data; name="answer"' . $eol . $eol; // last Content with 2 $eol, in this case is only 1 content.
        $BODY .= "Some Data" . $eol; //param data in this case is a simple post data and 1 $eol for the end of the data
        $BODY .= '--' . $BOUNDARY . $eol; // start 2nd param,
        $BODY .= 'Content-Disposition: form-data; name="answer"; filename="answer.json"' . $eol; //first Content data for post file, remember you only put 1 when you are going to add more Contents, and 2 on the last, to close the Content Instance
        $BODY .= 'Content-Type: application/octet-stream' . $eol; //Same before row
        $BODY.= 'Content-Transfer-Encoding: base64' . $eol . $eol; // we put the last Content and 2 $eol,
        $BODY .= file_get_contents($file_url) . $eol; // just grab the data from the file and concatenate with $ eol
        $BODY .= '--' . $BOUNDARY . '--' . $eol . $eol; // we close the param and the post width "--" and 2 $eol at the end of our boundary header.

        $ch = curl_init(); //init curl
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array("Content-Type: multipart/form-data; boundary=" . $BOUNDARY) //setting our mime type for make it work on $_FILE variable
        );
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/1.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0'); //setting our user agent
        curl_setopt($ch, CURLOPT_URL, "https://api.codenation.dev/v1/challenge/dev-ps/submit-solution?token=" . $this->token); //setting our api post url
        curl_setopt($ch, CURLOPT_COOKIEJAR, $BOUNDARY . '.json'); //saving cookies just in case we want
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // call return content
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //navigate the endpoint
        curl_setopt($ch, CURLOPT_POST, true); //set as post
        curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY); // set our $BODY 

        $response = curl_exec($ch); // start curl navigation

        echo ($response); //print response
    }
}
$codenation = new Codenation();
$codenation->decipher();
$codenation->generateSummary();
$codenation->sendFile();
echo "<br/>";
echo json_encode($codenation);
