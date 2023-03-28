<?php

declare(strict_types=1);

namespace Stulipan\CookieConsentBundle\Controller;

use Stulipan\CookieConsentBundle\Cookie\CookieChecker;
use Stulipan\CookieConsentBundle\Form\CookieConsentType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class CookieConsentController
{
    /**
     * @var Environment
     */
    private $twigEnvironment;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var CookieChecker
     */
    private $cookieChecker;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $cookieConsentPosition;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var bool
     */
    private $cookieConsentSimplified;

    /**
     * @var string|null
     */
    private $formAction;

    /**
     * @var string|null
     */
    private $privacyPolicyUrl;

    public function __construct(Environment $twigEnvironment, FormFactoryInterface $formFactory, CookieChecker $cookieChecker, RouterInterface $router,
                                string $cookieConsentPosition, TranslatorInterface $translator, bool $cookieConsentSimplified = false,
                                string $formAction = null, string $privacyPolicyUrl = null)
    {
        $this->twigEnvironment         = $twigEnvironment;
        $this->formFactory             = $formFactory;
        $this->cookieChecker           = $cookieChecker;
        $this->router                  = $router;
        $this->cookieConsentPosition   = $cookieConsentPosition;
        $this->translator              = $translator;
        $this->cookieConsentSimplified = $cookieConsentSimplified;
        $this->formAction              = $formAction;
        $this->privacyPolicyUrl        = $privacyPolicyUrl;
    }

    /**
     * Show cookie consent.
     *
     * @Route("/cookie_consent", name="cookie_consent.show")
     */
    public function show(Request $request): Response
    {
        $this->setLocale($request);

        $response = new Response(
            $this->twigEnvironment->render('@CookieConsent/cookie_consent.html.twig', [
                'form'       => $this->createCookieConsentForm()->createView(),
                'position'   => $this->cookieConsentPosition,
                'simplified' => $this->cookieConsentSimplified,
                'privacyPolicyUrl' => $this->privacyPolicyUrl,
            ])
        );

        // Cache in ESI should not be shared
        $response->setPrivate();
        $response->setMaxAge(0);

        return $response;
    }

    /**
     * Show cookie consent.
     *
     * @Route("/cookie_consent_alt", name="cookie_consent.show_if_cookie_consent_not_set")
     */
    public function showIfCookieConsentNotSet(Request $request): Response
    {
        if ($this->cookieChecker->isCookieConsentSavedByUser() === false) {
            return $this->show($request);
        }

        return new Response();
    }

    /**
     * Create cookie consent form.
     */
    protected function createCookieConsentForm(): FormInterface
    {
        if ($this->formAction === null) {
            $form = $this->formFactory->create(CookieConsentType::class);
        } else {
            $form = $this->formFactory->create(
                CookieConsentType::class,
                null,
                [
                    'action' => $this->router->generate($this->formAction),
                ]
            );
        }

        return $form;
    }

    /**
     * Set locale if available as GET parameter.
     */
    protected function setLocale(Request $request)
    {
        $locale = $request->get('locale');
        if (empty($locale) === false) {
            $this->translator->setLocale($locale);
            $request->setLocale($locale);
        }
    }

}
