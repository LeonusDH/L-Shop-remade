<?php
declare(strict_types = 1);

namespace app\Mail\Auth;

use app\Entity\Activation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Mail\Mailable;

class Confirmation extends Mailable
{
    use Queueable;

    /**
     * @var Activation
     */
    private $activation;

    public function __construct(Activation $activation)
    {
        $this->activation = $activation;
    }

    public function build(Repository $config, Translator $translator, UrlGenerator $url)
    {
        $this->subject = $translator->trans('mail.auth.confirmation.subject');

        return $this->view("mail.auth.confirmation", [
            'username' => $this->activation->getUser()->getUsername(),
            'link' => $url->route('frontend.auth.activation.complete', ['code' => $this->activation->getCode()]),
            'appName' => $config->get('app.name')
        ]);
    }
}
