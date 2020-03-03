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
        $message = (new \Swift_Message('Création d\'un nouvel article'))
                    ->setFrom('send@example.com')
                    ->setTo($mail_author)
                    ->setBody(
                        $this->templating->render(
                            'mail/email_waiting_validation.html.twig',
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

    public function mail_article($mail_author, $firstname, $lastname, $title, $action){
        $message = (new \Swift_Message('Article '. $action))
                    ->setFrom('send@example.com')
                    ->setTo($mail_author)
                    ->setBody(
                        $this->templating->render(
                            'mail/email_article.html.twig',
                            [
                                'lastname' => $lastname,
                                'firstname' => $firstname,
                                'articleTitle' => $title,
                                'action' => $action
                            ]
                        ),
                        'text/html'
                    );
                $this->mailer->send($message);
    }
    public function send_mail_validated($mail_author, $firstname, $lastname, $title, $path){
        $message = (new \Swift_Message('Validation d\'un nouvel article'))
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

