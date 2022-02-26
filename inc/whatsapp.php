<?php

namespace Responicwoo;


class Whatsapp
{
    private $base = 'https://api.responic.com';

    private $token;

    private $_recipient;

    private $_message;

    public function __construct()
    {
        $this->token = get_option('responicwoo_api_token');
    }

    /**
     * to
     *
     * @param  string $phone
     * @return mixed
     */
    public function to($phone)
    {
        $phone_to_check = str_replace('-', '', $phone);
        $phone_to_check = preg_replace('/[^0-9]/', '', $phone_to_check);
        if (strlen($phone_to_check) < 9 || strlen($phone_to_check) > 14) return $this;

        $phone_to_check = preg_replace('/^620/', '62', $phone_to_check);
        $phone_to_check = preg_replace('/^0/', '62', $phone_to_check);
        $this->_recipient = $phone_to_check;
        return $this;
    }

    /**
     * set message
     *
     * @param  string $message
     * @return mixed
     */
    public function message($message, $args)
    {
        $message = $message;
        preg_match_all('@\[([^<>&/\[\]\x00-\x20=]++)@', $message, $matches);

        foreach ($matches[1] as $key => $tag) {
            if (isset($args[$tag])) {
                $message = str_replace('[' . $tag . ']', $args[$tag], $message);
            }
        }

        $this->_message = $message;

        return $this;
    }

    public function send()
    {
        if (empty($this->_recipient) || empty($this->_message) || empty($this->token)) return false;

        error_log('responic_notif : recipient ' . $this->_recipient . ' is valid');
        error_log('responic_notif : token ready ' . $this->token);
        error_log('responic_notif : message ' . $this->_message);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base . '/message/text',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "recipient": "' . $this->_recipient . '",
    "message": "' . rawurldecode($this->_message) . '"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        error_log('responic_send_status : ' . wp_json_encode($response));
        return $response;
    }
}
