<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
include'config.php'; //Holds a constant variable for the api key and contact for test pourposes.

use SmsPubli\SmsClient;
$sms_client = new SmsClient(SMSPUBLIKEY, 'BARBERSMS');
$send = $sms_client->send_sms(SMSNUMBER, 'Omds isto resultou com o primeiro pacote')->get_status();