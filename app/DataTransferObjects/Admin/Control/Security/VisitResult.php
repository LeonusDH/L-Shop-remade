<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Control\Security;

use app\Services\Response\JsonRespondent;

class VisitResult implements JsonRespondent
{
    /**
     * @var bool
     */
    private $captchaEnabled;

    /**
     * @var string|null
     */
    private $recaptchaPublicKey;

    /**
     * @var string|null
     */
    private $recaptchaSecretKey;

    /**
     * @var bool
     */
    private $changePasswordEnabled;

    /**
     * @var bool
     */
    private $resetPasswordEnabled;


    /**
     * @param bool $captchaEnabled
     *
     * @return VisitResult
     */
    public function setCaptchaEnabled(bool $captchaEnabled): VisitResult
    {
        $this->captchaEnabled = $captchaEnabled;

        return $this;
    }

    /**
     * @param null|string $recaptchaPublicKey
     *
     * @return VisitResult
     */
    public function setRecaptchaPublicKey(?string $recaptchaPublicKey): VisitResult
    {
        $this->recaptchaPublicKey = $recaptchaPublicKey;

        return $this;
    }

    /**
     * @param null|string $recaptchaSecretKey
     *
     * @return VisitResult
     */
    public function setRecaptchaSecretKey(?string $recaptchaSecretKey): VisitResult
    {
        $this->recaptchaSecretKey = $recaptchaSecretKey;

        return $this;
    }

    /**
     * @param bool $changePasswordEnabled
     *
     * @return VisitResult
     */
    public function setChangePasswordEnabled(bool $changePasswordEnabled): VisitResult
    {
        $this->changePasswordEnabled = $changePasswordEnabled;

        return $this;
    }

    /**
     * @param bool $resetPasswordEnabled
     *
     * @return VisitResult
     */
    public function setResetPasswordEnabled(bool $resetPasswordEnabled): VisitResult
    {
        $this->resetPasswordEnabled = $resetPasswordEnabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function response(): array
    {
        return [
            'captchaEnabled' => $this->captchaEnabled,
            'recaptchaPublicKey' => $this->recaptchaPublicKey,
            'recaptchaSecretKey' => $this->recaptchaSecretKey,
            'changePasswordEnabled' => $this->changePasswordEnabled,
            'resetPasswordEnabled' => $this->resetPasswordEnabled,
        ];
    }
}
