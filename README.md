# Cookie Consent bundle for Symfony
Symfony bundle to append Cookie Consent to your website to comply to AVG/GDPR for cookies.

## Installation

### Step 1: Download using composer

[comment]: <> (In a Symfony application run this command to install and integrate Cookie Consent bundle in your application:)

[comment]: <> (```bash)

[comment]: <> (composer require stulipan/cookie-consent-bundle)

[comment]: <> (```)

Open your project's terminal or command prompt and navigate to the root directory of your Symfony project.

Open the composer.json file of your Symfony project in a text editor.

Look for the "require" section in the composer.json file. Add an entry for the bundle you want to install, specifying the GitHub repository and the version or branch you want to use. For example:

```bash
"require": {
    // ...
    "stulipan/cookie-consent-bundle" : "dev-master"
},
"repositories": [
    {
    "type": "vcs",
    "url": "https://github.com/stulipan/CookieConsentBundle.git"
    }
]
```

Save the composer.json file.

Run the following command in your project's terminal or command prompt to install the bundle:

```bash
composer update
```

Composer will fetch the bundle from the GitHub repository and install it along with its dependencies.

Once the installation is complete, the bundle should be available for use in your Symfony project. You then need to configure and register the bundle as described bellow.

### Step 2: Enable the bundle
When not using symfony flex, enable the bundle in the kernel manually:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Stulipan\CookieConsentBundle\CookieConsentBundle(),
        // ...
    );
}
```

### Step 3: Enable the routing
When not using symfony flex, enable the bundles routing manually:
```yaml
# config/routes.yaml
cookie_consent:
  resource: "@CookieConsentBundle/Resources/config/routing.yaml"
```

### Step 4: Configure to your needs
Configure your Cookie Consent with the following possible settings. 
```yaml
# config/packages/cookie_consent.yaml

cookie_consent:
    categories:               # Below are the default supported categories
        - 'statistics'
        - 'personalization'
        - 'marketing'
    use_logger: false         # Logs user actions to database. [Under development] 
    position: 'bottom'        # Values: 'top', 'bottom' (default: bottom)
    simplified: true          # When set to true the user can only deny or accept all cookies at once
    http_only: true           # Sets HttpOnly on cookies
    #   form_action: 'cookie-consent-success' #$routeName # When set, xhr-Requests will only be sent to this route. Take care of having the route available.
    csrf_protection: true     # The cookie consent form is csrf protected or not
    privacy_policy_url: '/hu/policies/privacy-policy'       # The Privacy or Cookie Policy URL
```

## Usage
### Twig implementation
Load the cookie consent in Twig via render_esi ( to prevent caching ) at any place you like:
```twig
{{ render_esi(path('cookie_consent.show')) }}
{{ render_esi(path('cookie_consent.show_if_cookie_consent_not_set')) }}
```

If you want to load the cookie consent with a specific locale you can pass the locale as a parameter:
```twig
{{ render_esi(path('cookie_consent.show', { 'locale' : 'en' })) }}
{{ render_esi(path('cookie_consent.show_if_cookie_consent_not_set', { 'locale' : app.request.locale })) }}
```

### Cookies
When a user submits the form the preferences are saved as cookies. The cookies have a lifetime of 1 year. The following cookies are saved:
- **CConsent_Date**: date of submit
- **CConsent_Key**: Generated key as identifier to the submitted Cookie Consent of the user
- **CCategory_[CATEGORY]**: selected value of user (*true* or *false*)

[comment]: <> (### Logging)

[comment]: <> (AVG/GDPR requires all given cookie preferences of users to be explainable by the webmasters. For this we log all cookie preferences to the database. IP addresses are anonymized. This option can be disabled in the config.)

[comment]: <> (![Database logging]&#40;https://raw.githubusercontent.com/ConnectHolland/cookie-consent-bundle/master/Resources/doc/log.png&#41;)

### Screenshots
![Simplified](https://raw.githubusercontent.com/stulipan/CookieConsentBundle/master/Resources/doc/simplified.png)
![Not simplified](https://raw.githubusercontent.com/stulipan/CookieConsentBundle/master/Resources/doc/not_simplified_01.png)
![Not simplified](https://raw.githubusercontent.com/stulipan/CookieConsentBundle/master/Resources/doc/not_simplified_02.png)

### TwigExtension
The following TwigExtension functions are available:

**cookieConsent_isCategoryAllowedByUser**
check if user has given it's permission for certain cookie categories
```twig
{% if cookieConsent_isCategoryAllowedByUser('statistics') == true %}
    ...
{% endif %}
```

**cookieConsent_isCookieConsentSavedByUser**
check if user has saved any cookie preferences
```twig
{% if cookieConsent_isCookieConsentSavedByUser() == true %}
    ...
{% endif %}
```


## Customization
### Categories
You can add or remove any category by changing the config and making sure there are translations available for these categories.

### Translations
All texts can be altered via Symfony translations by overwriting the CHCookieConsentBundle translation files.

### Styling
CHCookieConsentBundle comes with a default styling. A sass file is available in Resources/assets/css/cookie_consent.scss and a build css file is available in Resources/public/css/cookie_consent.css. Colors can easily be adjusted by setting the variables available in the sass file.

To install these assets run:
```bash
bin/console assets:install
```

And include the styling in your template:
```twig
{% include "@CookieConsent/cookie_consent_styling.html.twig" %}
```

### Javascript
By loading Resources/public/js/cookie_consent.js the cookie consent will be submitted via ajax and the cookie consent will be shown on top of your website while pushing down the rest of the website.

### Events
When a form button is clicked, the event of cookie-consent-form-submit-successful is created. Use the following code to listen to the event and add your custom functionality.
```javascript
document.addEventListener('CConsent-formSubmitedSuccessful', function (e) {
    // ... your functionality
    // ... e.detail is available to see which button is clicked.
}, false);
```

### Template Themes
You can override the templates by placing templates inside your project (except for Symfony 5 projects):

```twig
# app/Resources/CookieConsentBundle/views/cookie_consent.html.twig
{% extends '@!CookieConsent/cookie_consent.html.twig' %}

{% block title %}
    Your custom title
{% endblock %}
```

#### Template override for Symfony 5 projects
You can override the templates by placing templaces inside you project as below. Be careful, it is important to place templates at this location: "app/templates/bundles/CookieConsentBundle/" . 
```twig
# app/templates/bundles/CookieConsentBundle/cookie_consent.html.twig
{% extends '@!CookieConsent/cookie_consent.html.twig' %}

{% block intro %}
    Your custom intro
{% endblock %}
```
