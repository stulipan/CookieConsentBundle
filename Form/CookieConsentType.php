<?php

declare(strict_types=1);

namespace Stulipan\CookieConsentBundle\Form;

use Stulipan\CookieConsentBundle\Cookie\CookieChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CookieConsentType extends AbstractType
{
    /**
     * @var CookieChecker
     */
    protected $cookieChecker;

    /**
     * @var array
     */
    protected $cookieCategories;

    /**
     * @var bool
     */
    protected $cookieConsentSimplified;

    /**
     * @var bool
     */
    protected $csrfProtection;

    public function __construct(CookieChecker $cookieChecker, array $cookieCategories, bool $cookieConsentSimplified = false, bool $csrfProtection = true)
    {
        $this->cookieChecker           = $cookieChecker;
        $this->cookieCategories        = $cookieCategories; // $cookieConsentSimplified ? [] : $cookieCategories; // if isSimplified then we have no categories
        $this->cookieConsentSimplified = $cookieConsentSimplified;
        $this->csrfProtection          = $csrfProtection;
    }

    /**
     * Build the cookie consent form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // if isSimplified then we have no categories
//        if ($this->cookieConsentSimplified === false) {
            foreach ($this->cookieCategories as $category) {
                $builder->add($category, CheckboxType::class, [
                    'data' => $this->cookieChecker->isCategoryAllowedByUser($category) ? true : false,
                ]);
                $builder->get($category)->addModelTransformer(new CallbackTransformer(
                    function ($string): bool {
                        // this is used to render the form field
                        return 'true' == $string ? true : false;
                    },
                    function ($bool): string {
                        // it transforms the submitted value back into the format you'll use in your code
                        return $bool ? 'true' : 'false';
                    }
                ));
            }
//        }

        if ($this->cookieConsentSimplified === false) {
            // Accept all cookies button
            $builder->add('use_all_cookies', SubmitType::class, [
                'label' => 'cookie_consent_translation.use_all_cookies_button',
                'attr' => [
                    'class' => 'btn btn-success JS--Button-allowCookies JS--Button-acceptAllCookies vertical-col '
                ]
            ]);
            // Accept cookies (only those selected)
            $builder->add('use_only_selected', SubmitType::class, [
                'label' => 'cookie_consent_translation.save_selected_button',
                'attr' => [
                    'class' => 'btn btn-success JS--Button-allowCookies JS--Button-onlySelected vertical-col '
                ]
            ]);
            // Cookie settings button
            $builder->add('show_cookie_settings', SubmitType::class, [
                'label' => 'cookie_consent_translation.show_cookie_settings_button',
                'attr' => [
                    'class' => 'btn btn-secondary JS--Button-toggleDetails JS--Button-showSettings vertical-col '
                ]
            ]);
            // Hide cookie settings button
            $builder->add('hide_cookie_settings', SubmitType::class, [
                'label' => 'cookie_consent_translation.hide_cookie_settings_button',
                'attr' => [
                    'class' => 'btn btn-secondary JS--Button-toggleDetails JS--Button-hideSettings vertical-col '
                ]
            ]);
        } else {
            $builder->add('use_all_cookies', SubmitType::class, [
                'label' => 'cookie_consent_translation.use_all_cookies_button',
                'attr' => [
                    'class' => 'btn btn-success ch-cookie-consent__btnX ch-cookie-consent__btn--secondaryX JS--Button-allowCookies JS--Button-acceptAllCookies'
                ]
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (isset($data['use_all_cookies'])) {
                foreach ($this->cookieCategories as $category) {
                    $data[$category] = 'true';
                }
            }

            $event->setData($data);
        });

    }

    /**
     * Default options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'CookieConsentBundle',
            'csrf_protection' => $this->csrfProtection,
        ]);
    }
}
