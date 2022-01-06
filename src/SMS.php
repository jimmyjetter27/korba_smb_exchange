<?php


namespace Korba;

use Illuminate\Support\Facades\Log;

/**
 * Class SMS help send messages.
 * Class to make use of Info SMS API service to send sms messages. It extends Class API. {@inheritDoc}
 * @see http://infobip.com Infobip Website
 * @package Korba
 */
class SMS extends API
{
    /** @var string|null Global recognition for source of message to avoid writing with each message sent. */
    protected $global_from;

    /**
     * SMS constructor.
     * It used to create a new instance of the SMS Class.
     * @param string $base_url INfobip Account Personal Base url.
     * @param string $username Infobip Account Username.
     * @param string $password Infobip Account Password.
     * @param string|null $global_from Identification of the source of the SMS.
     */
    public function __construct($base_url, $username, $password, $global_from = null)
    {
//        $authorization = base64_encode("{$username}:{$password}");
        $headers = [
            'Authorization: Token '.env('SMS_AUTH_TOKEN'),
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        $this->global_from = $global_from == null ? 'Korba' : $global_from;
        parent::__construct($base_url, $headers);
    }

    /**
     * SMS public function send.
     * It is used to send SMS
     * @param string $text Body of the SMS
     * @param string|array $to Number of the recipient
     * @param string|null $from Name of the send. if null will fallback to global_from
     * @return bool|string
     */
    public function send($text, $to, $from = null)
    {
        $formatter = function ($value) {
            return Util::numberIntFormat($value);
        };
        $to = gettype($to) == 'array' ? array_map($formatter, $to) : Util::number233Format($to);
        $data = [
            'to' => $to,
            'text' => $text
        ];
        $data['from'] = $from == null ? $this->global_from : $from;
//        return $this->call('/send_sms/', $data);
        return $this->sending($to, $text);
    }

    public function sending($to, $text)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SMS_BASE_URL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
    "phone_number": "'.$to.'",
    "message":"'.$text.'",
    "sender_id":"KorbaSMB"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token '.env('SMS_AUTH_TOKEN'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        echo $response;
        Log::debug($response);
    }
}
