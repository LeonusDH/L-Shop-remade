<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Auth;

use app\Handlers\Frontend\Auth\CompleteActivationHandler;
use app\Handlers\Frontend\Auth\RepeatActivationHandler;
use app\Http\Controllers\Controller;
use app\Http\Middleware\Captcha as CaptchaMiddleware;
use app\Http\Requests\Frontend\Auth\RepeatActivationRequest;
use app\Services\Auth\AccessMode;
use app\Services\Auth\Exceptions\AlreadyActivatedException;
use app\Services\Auth\Exceptions\UserDoesNotExistException;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Success;
use app\Services\Notification\Notificator;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Security\Captcha\Captcha;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

/**
 * Class ActivationController
 * Handles requests related to user activation.
 */
class ActivationController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware(CaptchaMiddleware::NAME)->only('repeat');
    }

    /**
     * Returns the data needed to render the page with the activation sent form.
     *
     * @param Settings $settings
     * @param Captcha  $captcha
     *
     * @return JsonResponse
     */
    public function sent(Settings $settings, Captcha $captcha): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, [
            'accessModeAny' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY,
            'accessModeAuth' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY,
            'captchaKey' => $settings->get('system.security.captcha.enabled')->getValue(DataType::BOOL) ? $captcha->key() : null
        ]);
    }

    /**
     * Handles a repeat send user activation request.
     *
     * @param RepeatActivationRequest $request
     * @param RepeatActivationHandler $handler
     *
     * @return JsonResponse
     */
    public function repeat(RepeatActivationRequest $request, RepeatActivationHandler $handler): JsonResponse
    {
        try {
            $handler->handle($request->get('email'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.frontend.auth.activation.repeat')));
        } catch (UserDoesNotExistException $e) {
            return (new JsonResponse('user_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.frontend.auth.activation.user_not_found')));
        } catch (AlreadyActivatedException $e) {
            return (new JsonResponse('already_activated'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Error(__('msg.frontend.auth.activation.already')));
        }
    }

    /**
     * Processes the complete activation request. This action will be processed when the user
     * clicks on the link to activate the account from the email.
     *
     * @param Request $request
     * @param CompleteActivationHandler $handler
     * @param Notificator $notificator
     * @param Repository $config
     * @param Settings $settings
     * @param Redirector $redirector
     *
     * @return RedirectResponse
     */
    public function complete(
        Request $request,
        CompleteActivationHandler $handler,
        Notificator $notificator,
        Repository $config,
        Settings $settings,
        Redirector $redirector): RedirectResponse
    {
        if ($handler->handle($request->route('code'))) {
            $notificator->notify(new Success(__('msg.frontend.auth.activation.success')));
        } else {
            $notificator->notify(new Error(__('msg.frontend.auth.activation.fail')));
        }

        if ($settings->get('auth.register.custom_redirect.enabled')->getValue(DataType::BOOL)) {
            return $redirector->to($settings->get('auth.register.custom_redirect.url')->getValue());
        }

        return $redirector->to($config->get('app.url'));
    }
}
