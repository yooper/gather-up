<?php
declare(strict_types=1);

namespace App\Message;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Sends an email to the designated address, so it shows up on a slack channel
 */
class SendSlackEmail
{
    protected LoggerInterface $logger;
    protected MailerInterface $mailer;

    /**
     * @param MailerInterface $mailer
     * @param LoggerInterface $logger
     */
    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @param string $slackEmailAddress -> The associated slack email
     * @param string $textMessage -> The message to send
     * @return bool -> Return a bool to indicate if the message correctly sent
     */
    public function send(string $slackEmailAddress, string $textMessage) : bool
    {
        try {
            $email = (new Email())
                ->from(new Address('gatherup@example.com'))
                ->to(new Address($slackEmailAddress))
                ->text($textMessage);
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e);
            return false;
        } catch(\Exception $ex) {
            $this->logger->error($ex);
            return false;
        }
    }
}