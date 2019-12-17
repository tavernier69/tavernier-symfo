<?php

namespace App\Service;

use Swift_Mailer;


class MailService{

    public function __construct(\Twig_Environment $templating, Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    public function send_mail($mail_author, $firstname, $lastname, $title, $path){
        $message = (new \Swift_Message('Hello Email'))
                    ->setFrom('send@example.com')
                    ->setTo($mail_author)
                    ->setBody(
                        $this->templating->render(
                            'mail/email.html.twig',
                            [
                                'lastname' => $lastname,
                                'firstname' => $firstname,
                                'articleTitle' => $title,
                                'path' => $path
                            ]
                        ),
                        'text/html'
                    );
                $this->mailer->send($message);
    }
}

