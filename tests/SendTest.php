<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use SmsPubli\SmsClient;

class SendTest extends TestCase {
    public function testCanSendSMS () {

        $sms_client = new SmsClient(getenv('KEY'), "BARBERSMS", null, true);
        $send = $sms_client
            ->send_sms("351912791994", 'Omds isto resultou com o primeiro pacote')
            ->get_status();

        $this->assertArrayHasKey('status', $send);
        $this->assertEquals(200, $send['status']);
    }
}