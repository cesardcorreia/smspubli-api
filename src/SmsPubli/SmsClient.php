<?php

namespace SmsPubli;

use GuzzleHttp\Client;

class SmsClient implements SmsClientInterface
{
    protected $build;
    private $api_key, $from, $report_url;
    public $message, $status;

    /**
     * Calls the from validation function and assigns the general variables for API usage
     *
     * SmsClient constructor.
     * @param $key
     * @param $from
     * @param null $callback
     * @throws \Exception
     */
    public function __construct($key, $from, $callback = null)
    {
        $this->validate_from($from);
        $this->api_key = $key;
        $this->from = $from;
        $this->report_url = $callback;
        return $this;
    }

    /**
     * Resets the object of build
     */
    public function reset () {
        $this->build = new \stdClass;
    }

    /**
     * Validates the name of the FROM parameter on the API in which it can not be
     * longer than 11 characters.
     * @param $from
     * @throws \Exception
     */
    private function validate_from($from)
    {
        if (strlen($from) > 11) throw new \Exception('From can not be longer than 11 characters');
    }

    /**
     * Calls the Validation Class to verify every parameter of the contact to check out if it is valid or not
     *
     * @param $contact
     * @throws \Exception
     */
    private function validate_contact($contact)
    {
        $contact_validator = new ContactValidator();
        if ($contact_validator->validate($contact)['is_valid'] !== true) {
            throw new \Exception('Validating this contact as failed!');
        }
    }

    /**
     * Handles the response after the request was finished and
     * sets the messages and status according to the response.
     *
     * @param $response
     * @return $this
     * @throws \Exception
     */
    private function handle_response($response, $reponse_message)
    {
        $this->status = $response->getStatusCode();

        if (!is_string($reponse_message)) throw new \Exception('SMSPUBLI API response is not json');

        $status_response = json_decode($reponse_message, true);

        if ($status_response['result'][0]['status'] === "error") {
            $this->message = [
                'error_id' => $status_response['result'][0]['error_id'],
                'error_msg' => $status_response['result'][0]['error_msg']
            ];
            return $this;
        } elseif ($status_response['result'][0]['status'] === "ok") {
            $this->message = [
                'success_msg' => 'Sent with success!',
                'sms_id' => $status_response['result'][0]['sms_id']
            ];
            return $this;
        }

        $this->message = ['Something went wrong is the status response answer.'];

        return $this;
    }

    public function send_sms($to_contact, $message)
    {
        $this->reset();
        $this->build->sent = 1;
        $this->validate_contact($to_contact);

        $response = new Client();

        try {
            $request = $response->request('POST', 'https://api.gateway360.com/api/3.0/sms/send', [
                'headers' => [
                    'content-type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'api_key' => $this->api_key,
                    'report_url' => 'http://localhost',
                    'concat' => 1,
                    'messages' => [
                        [
                            'from' => $this->from,
                            'to' => $to_contact,
                            'text' => $message
                        ]
                    ]
                ],
                'allow_redirects' => false,
            ]);
        } catch (\Exception $e) { //catch errors thrown by guzzle
            $this->status = false;
            $this->message = [
                'error_msg' => $e->getMessage()
            ];
            return $this;
        }

        $this->handle_response($request, $request->getBody()->getContents());

        return $this;
    }

    /**
    * Compiles the information of the given result by the sms call
    *
    * @return array
    **/
    public function get_status()
    {
        //validate if send_sms was called
        if(!isset($this->build->sent)) throw new \Exception('Can not call gesStatus before send_sms');

        return array_merge(['status' => $this->status], $this->message);
    }
}