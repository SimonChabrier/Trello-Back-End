<?php

namespace App\Service;

use App\Message\AdminNotification;
use App\Message\EmailNotification;
use App\Message\AccountCreatedNotification;
use Symfony\Component\Messenger\MessageBusInterface;

class MailSender
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function sendAdminNotification($subject, $email, $status)
    {
        $message = new AdminNotification($subject, $email, $status);

        $this->bus->dispatch($message);
    }

    public function sendTemplateEmailNotification($from, $to, $subject, $template, $context)
    {   
        $message = new EmailNotification($from, $to, $subject, $template, $context);
        $this->bus->dispatch($message);
    }
}