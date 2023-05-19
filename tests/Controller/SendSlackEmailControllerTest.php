<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\Transport;


class SendSlackEmailControllerTest extends WebTestCase
{
    public function testGoodEmail(): void
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/send-slack-email',
            ['email' => 'dcardin2007@gmail.com', 'message' => 'hello world']);
        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString('{"success": true}', $client->getResponse()->getContent());

        $this->assertEmailCount(1); // use assertQueuedEmailCount() when using Messenger
        $email = $this->getMailerMessage();
        $this->assertEmailTextBodyContains($email, 'hello world');
    }

    public function testBadEmail(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/send-slack-email',
            ['email' => 'not an email', 'message' => 'hello world']);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString('{"success": false}', $client->getResponse()->getContent());
        $this->assertEmailCount(0); // use assertQueuedEmailCount() when using Messenger
    }

    public function testMisconfiguredMailSettings(): void
    {
        $this->markTestSkipped('TODO, need to change the MAIL DSN setting so the mail transport exception is thrown');
        $client = static::createClient();
        $crawler = $client->request('POST', '/send-slack-email',
            ['email' => 'dcardin2007@gmail.com', 'message' => 'hello world']);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString('{"success": false}', $client->getResponse()->getContent());
        $this->assertEmailCount(0); // use assertQueuedEmailCount() when using Messenger
    }

}
