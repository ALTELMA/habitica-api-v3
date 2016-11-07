<?php /**
 * Habitica
 * API vsersion 3.0
 */ class Habitica {
    private $uid;
    private $token;
    private $endpoint = 'https://habitica.com/api/v3/';
    private $headers;
    public function __construct($uid, $token)
    {
        $this->uid = $uid;
        $this->token = $token;
        $this->headers = [
            "x-api-user:". $this->uid,
            "x-api-key:". $this->token
        ];
    }
    public function getUser()
    {
        return $this->curl($this->endpoint . 'user');
    }
    public function getData($result, $header_length)
    {
        return substr($result, $header_length);
    }
    public function curl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, $this->headers);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}
