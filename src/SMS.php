<?php


namespace Korba;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Korba\Util;

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
    public function sending($text, $to, $from = null)
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

    public function send($message, $phoneNumber)
    {
        $changeNumberFormat = self::numberIntFormat($phoneNumber);
        error_log('logging phone number being sent');
        error_log($changeNumberFormat);
        return $response = Http::withHeaders([
            'Authorization' => 'Token ' . env('SMS_AUTH_TOKEN'),
            'Content-Type' => 'application/json'
        ])
            ->withOptions([
                'debug' => fopen('php://stderr', 'w'),
                'verify' => false
            ])
            ->post(env('SMS_BASE_URL'), [
                'phone_number' => $changeNumberFormat,
                'message' => $message,
                'sender_id' => 'KorbaSMB'
            ]);
    }

    public function numberIntFormat($gh_number) {
        if (preg_match('/^0/', $gh_number)) {
            return preg_replace('/^0/', '+233', $gh_number);
        }
        return preg_replace('/^233/', '+233', $gh_number);
    }
}
