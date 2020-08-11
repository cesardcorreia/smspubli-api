<?php


namespace SmsPubli;


use Symfony\Component\Dotenv\Dotenv;

class ClientComponent
{
    public $api_key,$from, $debug,$contact_send;

    public function init()
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env');

        $this->api_key = $_ENV['SMS_PUBLI_KEY'];
        $this->from = $_ENV['SMS_NAME'];
        $this->contact_send = $_ENV['CONTACT_SEND'];
        $this->debug = $_ENV['DEBUG'];

        if(!isset($this->api_key) || !isset($this->from) || !isset($this->contact_send) || !isset($this->debug)){
            throw new \Exception('Settings not correctly loaded');
        }
    }
}