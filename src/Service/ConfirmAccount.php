<?php 

namespace App\Service;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;

class ConfirmAccount
{   
    private $emailVerifier;
    private $parameterBag;

    public function __construct(
        EmailVerifier $emailVerifier, 
        ParameterBagInterface $parameterBag
    ) {
        $this->emailVerifier = $emailVerifier;
        $this->parameterBag = $parameterBag;
    }

    public function sendBeforeRegisterMessage(User $user): void
    {
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address($this->parameterBag->get('admin_email'), 'TrelloBackEnd'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
