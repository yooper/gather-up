<?php
declare(strict_types=1);

namespace App\Controller;

use App\Message\SendSlackEmail;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Api endpoint for the front end to send message via email to a slack channel
 */
class SendSlackEmailController extends AbstractController
{

    #[Route("/send-slack-email", name: 'send_slack_email')]
    public function send(Request $request,
                         MailerInterface $mailer,
                         LoggerInterface $logger,
                         ValidatorInterface $validator): Response
    {
        $message = $request->request->get('message');

        $emailAddress = $request->request->get('email');
        $emailConstraint = new Assert\Email();
        $emailConstraint->message = $emailAddress;

        // use the validator to validate the value
        $errors = $validator->validate(
            $emailAddress,
            $emailConstraint
        );

        // bad email address
        if ($errors->count()) {
            return $this->json(['success' => false], 400);
        }

        $slackEmailer = new SendSlackEmail($mailer, $logger);

        if($slackEmailer->send($emailAddress, strip_tags($message))){
            return $this->json(['success' => true]);
        }
        return $this->json(['success'=> false], 500);
    }
}