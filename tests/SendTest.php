<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use SmsPubli\SmsClient;

class SendTest extends TestCase {
    public function testCanSendSMS () {

        $sms_client = new SmsClient($_ENV['KEY'], $_ENV['SMS_NAME'], null, true);
        $send = $sms_client
            ->send_sms($_ENV['CONTACT_SEND'], 'This is a test message.')
            ->get_status();

        $this->assertArrayHasKey('status', $send);
        $this->assertEquals(200, $send['status']);
    }
}