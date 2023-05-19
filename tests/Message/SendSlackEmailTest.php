<?php

namespace App\Tests\Message;

use App\Message\SendSlackEmail;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendSlackEmailTest extends TestCase
{
    public function testGoodSend(): void
    {
        $mailerMock = $this->createMock(MailerInterface::class);
        $mailerMock
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Email::class));

        $sender = new SendSlackEmail($mailerMock, $this->createMock(LoggerInterface::class));
        $r = $sender->send('test@example.org', 'test_message');
        $this->assertTrue($r);
    }

    /**
     * @ep
     * @return void
     */
    public function testBadSend(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock
            ->expects($this->once())
            ->method('error');

        $mailerMock = $this->createMock(MailerInterface::class);
        $mailerMock
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Email::class))
            ->willThrowException(new TransportException("not configured"));

        $sender = new SendSlackEmail($mailerMock, $loggerMock);

        $r = $sender->send('test@example.org', 'test_message');
        $this->assertFalse($r);
    }
}
