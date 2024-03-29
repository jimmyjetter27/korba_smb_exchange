<?php

/**
 * Class Util at src/Util.php.
 * File containing Util class
 * @api
 * @author Isaac Adzah Sai <isaacsai030@gmail.com>
 * @version 2.5.2
 */
//namespace JimmyJetter\SmbExchange;
namespace Korba;
/**
 * Class Util is a helper class.
 * A class with function and constant to help easily create USSD.
 * The USSD provider is TxtGhana and the consumer is Korba
 * @see https://www.txtghana.com TxtGhana Website
 * @see https://www.korbaweb.com Korba Website
 * @package Korba
 */
final class Util
{
    /**
     * Util constructor.
     *  This construct prevents creating an instance of the class since it has only static methods and constants.
     */
    private function __construct(){ }

    /** @var int constant 17 */
    const TERMINATION_CODE = 17;

    /** @var int constant 2 */
    const CONTINUE_CODE = 2;

    /**
     * Util public static function codeToNetwork.
     * It converts TxtGh network code to Korba network code
     * @param string $code TxtGhana Code
     * @return string|null
     */
    public static function codeToNetwork($code) {
        if ($code == "01") {
            return "MTN";
        } else if ($code == "02") {
            return "VOD";
        } else if ($code == "06") {
            return "AIR";
        } else if ($code == "03") {
            return "TIG";
        } else {
            return null;
        }
    }

    /**
     * Util public static function random.
     * It generates random ids
     * @return string
     */
    public static function random() {
        return rand(1000, 10000).rand(1000, 10000).rand(1000, 10000).rand(1000, 10000);
    }

    /**
     * Util public static function numberGHFormat.
     * It convert phone numbers to Ghana Phone Number Format
     * @param string $int_number Number to convert
     * @return string
     */
    public static function numberGHFormat($int_number) {
        if (preg_match('/^\+233/', $int_number)) {
            return preg_replace("/^\+233/", "0", $int_number);
        }
        return preg_replace('/^233/', '0', $int_number);
    }

    /**
     * Util public static function numberIntFormat.
     * It convert phone numbers to the Internation Number Format
     * @param string $gh_number Number to convert
     * @return string
     */
    public static function numberIntFormat($gh_number) {
        if (preg_match('/^0/', $gh_number)) {
            return preg_replace('/^0/', '+233', $gh_number);
        }
        return preg_replace('/^233/', '+233', $gh_number);
    }

    /**
     * Util public static function number233Format.
     * It convert phone numbers to the 233 Number Format
     * @param string $number Number to convert
     * @return string
     */
    public static function number233Format($number) {
        if (preg_match('/^0/', $number)) {
            return preg_replace('/^0/', '233', $number);
        }
        return preg_replace('/^\+233/', '233', $number);
    }

    /**
     * Util public static function processBack.
     * It allows for ussd to move to previous menu
     * @param string $key
     * @param string $value
     * @param array[] $history
     * @param string $option
     */
    public static function processBack($key, &$value, &$history, &$option) {
        if ($value === $key) {
            array_pop($history);
            if (count($history) > 1) {
                $index = count($history)  - 1;
                $option = $history[$index]->{'option'};
                $value = $history[$index]->{'param'};
                array_pop($history);
            } else if (count($history) == 1) {
                $option = $history[0]->{'option'};
                array_pop($history);
            }
        }
    }

    /**
     * Util public static function processNext.
     * It allows ussd to move to next page on a menu
     * @param string $key
     * @param string $value
     * @param string $target
     * @param array[] $history
     * @param string $option
     */
    public static function processNext($key, &$value, &$target, &$history, &$option) {
        if ($value === $key) {
            if (count($history) > 0) {
                $index = count($history)  - 1;
                $option = $history[$index]->{'option'};
                $value = $history[$index]->{'param'};
                array_pop($history);
                $target++;
            }
        }
    }

    /**
     * Util public static function processPrevious.
     * It allows ussd to move to previous page on a menu
     * @param string $key
     * @param string $value
     * @param string $target
     * @param array[] $history
     * @param string $option
     */
    public static function processPrevious($key, &$value, &$target, &$history, &$option) {
        if ($value === $key) {
            if (count($history) > 0) {
                $index = count($history)  - 1;
                $option = $history[$index]->{'option'};
                $value = $history[$index]->{'param'};
                array_pop($history);
                $target--;
            }
        }
    }

    /**
     * Util public static function processReset.
     * It allows the index of pages of menus to be reset after navigating it
     * @param string[] $keys
     * @param string $value
     * @param integer $target
     */
    public static function processReset($keys, $value, &$target) {
        if (!in_array($value, $keys)) {
            $target = 1;
        }
    }

    /**
     * Util public static function processBeginning.
     * It allows ussd to move to first menu
     * @param string $key
     * @param string $value
     * @param array[] $history
     * @param string $option
     */
    public static function processBeginning($key, &$value, &$history, &$option) {
        if ($value === $key) {
            if (count($history) > 0) {
                $option = $history[0]->{'option'};
                $value = $history[0]->{'param'};
                $history = array();
            }
        }
    }

    /**
     * Util public static function verifyPhoneNumber.
     * It verifies if a number is correct
     * @param string $number
     * @return boolean
     */
    public static function verifyPhoneNumber($number) {
        return preg_match("/^[0][0-9]{9}$/", $number) ? true : false;
    }

    public static function verify233Format($phoneNumber)
    {
        $phoneNumber = (int)$phoneNumber;
        return is_int($phoneNumber) && str_starts_with($phoneNumber, '233') && strlen($phoneNumber) == 12;
    }

    public static function verifyGHFormat($phoneNumber)
    {
//        return strlen($phoneNumber) == 10;
        return preg_match("/^[0][0-9]{9}$/", $phoneNumber) && str_starts_with($phoneNumber, '0') && strlen($phoneNumber) == 10;
    }

    /**
     * Util public static function verifyNumberLength.
     * It verifies if a number is exactly a particular length
     * @param string $number Number String to verify
     * @param int $length Length to use for validation
     * @return boolean
     */
    public static function verifyNumberLength($number, $length = 10) {
        return preg_match("/^[0-9]{".$length."}$/", $number) ? true : false;
    }

    /**
     * Util public static function verifyWholeNumber.
     * It verifies if a number is a positive integer
     * @param string $number
     * @return boolean
     */
    public static function verifyWholeNumber($number) {
        return preg_match("/^[1-9][0-9]*$/", $number) ? true : false;
    }

    /**
     * Util public static function verifyAmount.
     * It verifies if amount if a correct value
     * @param string $amount
     * @return boolean
     */
    public static function verifyAmount($amount) {
        return preg_match("/^[0-9]+(?:\.[0-9]{1,2})?$/", $amount) ? true : false;
    }

    /**
     * Util public static function verifyNumber.
     * Verifies if number is a single digit number
     * @param string $number
     * @return boolean
     */
    public static function verifyNumber($number) {
        return preg_match("/^[0-9]*$/", $number) ? true : false;
    }

    /**
     * Util public static function requestToHashedMapArray.
     * It convert request to an array that can be stored in a database
     * @param $request
     * @param View $response
     * @param string $option
     * @param null|string $auth
     * @param null|string $type
     * @return array
     */
    public static function requestToHashedMapArray($request, $response, $option = "MAIN_MENU", $auth = null, $type = null) {
        return [
            Param::PHONE_NUMBER => $request->msisdn,
            Param::SESSION_ID => $request->sessionID,
            Param::NETWORK => Util::codeToNetwork($request->network),
            Param::OPTION => $response->getNext(),
            Param::TYPE => $type,
            Param::AUTHORIZATION => $auth,
            Param::HISTORY => json_encode([
                [Param::PARAM => 'null', Param::OPTION => $option]
            ]),
            Param::TRANSACTION_ID => Util::random()
        ];
    }

    /**
     * Util public static function isInitialRequest.
     * Check if request in an initial request
     * @param $request
     * @return bool
     */
    public static function isInitialRequest($request) {
        return ($request->ussdServiceOp == 1 ? true : false);
    }

    /**
     * Util public static function isContinuingRequest.
     * Check if request indicates continuity
     * @param $request
     * @return bool
     */
    public static function isContinuingRequest($request) {
        return ($request->ussdServiceOp == 18 ? true : false);
    }

    /**
     * Util public static function getRequestSessionId.
     * Returns the session id in the request
     * @param $request
     * @return string
     */
    public static function getRequestSessionId($request) {
        return $request->sessionID;
    }

    /**
     * Util public static function parseHistoryToArray.
     * Convert History in database back to php array
     * @param string $history
     * @return array
     */
    public static function parseHistoryToArray($history) {
        return (array)json_decode($history);
    }

    /**
     * Util public static function parseResponseToArray.
     * Converts request to array which can be sent back to operator
     * @param View $response
     * @param integer $code
     * @return array
     */
    public static function parseResponseToArray($response, $code) {
        return [
            'message' => $response->parseToString(),
            'ussdServiceOp' => $code
        ];
    }

    /**
     * Util public static function appendHistory.
     * Adds a new entry to history
     * @param array $history
     * @param string $input
     * @param string $option
     */
    public static function appendHistory(&$history, $input, $option) {
        array_push($history, [
                'param' => $input,
                'option' => $option
            ]
        );
    }

    /**
     * Util public static function reverse.
     * Move ussd back to point in the past
     * @param string $key
     * @param string $value
     * @param array[] $history
     * @param string $option
     * @param string $from
     * @param string $to
     */
    public static function reverse($key, &$value, &$history, &$option, $from, $to) {
        if ($value === $key && $from === $option) {
            for($i = count($history) - 1;$i >= 1;$i--) {
                if ($history[$i]->{'option'} === $to) {
                    $option = $history[$i]->{'option'};
                    $value = $history[$i]->{'param'};
                    array_pop($history);
                    break;
                } else {
                    array_pop($history);
                }
            }
        }
    }

    /**
     * Util public static function redirect.
     * Redirect ussd request from one menu to the other
     * @param string $key
     * @param string $value
     * @param string $new_value
     * @param string $option
     * @param string $from
     * @param string $to
     */
    public static function redirect($key, &$value, $new_value, &$option, $from, $to) {
        if ($value === $key && $from === $option) {
            $option = $to;
            $value = $new_value;
        }
    }


    public static function checkNetworkName(array $response)
    {
        if (isset($response['status']) && $response['status'] == 'OK') {
            if ($response['network'] == 'Vodafone') {
                $response['network'] = 'VOD';
            } else if ($response['network'] == 'AirtelTigo') {
                $response['network'] = 'AIR';
            }

            return $response;
        }
        return $response;
    }
}
