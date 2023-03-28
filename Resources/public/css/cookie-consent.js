document.addEventListener("DOMContentLoaded", function() {
    var cookieConsent = document.querySelector('.JS--Wrapper-cookieConsent');
    var cookieConsentForm = document.querySelector('.JS--Wrapper-cookieConsentForm');
    var cookieConsentFormBtn = document.querySelectorAll('.JS--Button-allowCookies');
    var acceptAllCookiesBtn = document.querySelector('.JS--Button-acceptAllCookies');
    var onlySelectedBtn = document.querySelector('.JS--Button-onlySelected');
    var showCookieSettingsBtn = document.querySelector('.JS--Button-showSettings');
    var hideCookieSettingsBtn = document.querySelector('.JS--Button-hideSettings');
    var cookieConsentCategoryDetails = document.querySelector('.JS--Wrapper-categoryGroup');
    var cookieConsentCategoryDetailsToggle = document.querySelectorAll('.JS--Button-toggleDetails');

    if (cookieConsentForm) {
        // Submit form via ajax
        for (var i = 0; i < cookieConsentFormBtn.length; i++) {
            var btn = cookieConsentFormBtn[i];
            btn.addEventListener('click', function (event) {
                event.preventDefault();

                var formAction = cookieConsentForm.action ? cookieConsentForm.action : location.href;
                var xhr = new XMLHttpRequest();

                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        cookieConsent.style.display = 'none';
                        var buttonEvent = new CustomEvent('CConsent-formSubmitedSuccessful', {
                            detail: event.target
                        });
                        document.dispatchEvent(buttonEvent);
                    }
                };
                xhr.open('POST', formAction);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send(serializeForm(cookieConsentForm, event.target));

                // // Clear all styles from body to prevent the white margin at the end of the page
                // document.body.style.marginBottom = null;
                // document.body.style.marginTop  = null;
            }, false);
        }
    }

    if (cookieConsentCategoryDetails && cookieConsentCategoryDetailsToggle) {
        cookieConsentCategoryDetailsToggle.forEach(function (el) {
           el.addEventListener('click', function() {
               var detailsIsHidden = cookieConsentCategoryDetails.style.display !== 'block';
               cookieConsentCategoryDetails.style.display = detailsIsHidden ? 'block' : 'none';
               acceptAllCookiesBtn.style.display = detailsIsHidden ? 'none' : 'block';  // Show onlySelectedBtn only when categories are shown
               onlySelectedBtn.style.display = detailsIsHidden ? 'block' : 'none';  // Show onlySelectedBtn only when categories are shown
               showCookieSettingsBtn.style.display = detailsIsHidden ? 'none' : 'block';  // Show onlySelectedBtn only when categories are shown
               hideCookieSettingsBtn.style.display = detailsIsHidden ? 'block' : 'none';  // Show onlySelectedBtn only when categories are shown

           });
        });
    }
});

function serializeForm(form, clickedButton) {
    var serialized = [];

    for (var i = 0; i < form.elements.length; i++) {
        console.log(form.elements.length);
        var field = form.elements[i];

        if ((field.type !== 'checkbox' && field.type !== 'radio' && field.type !== 'button') || field.checked) {
            serialized.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value));
        }
    }

    serialized.push(encodeURIComponent(clickedButton.getAttribute('name')) + "=");

    return serialized.join('&');
}

if ( typeof window.CustomEvent !== "function" ) {
    function CustomEvent(event, params) {
        params = params || {bubbles: false, cancelable: false, detail: undefined};
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
        return evt;
    }

    CustomEvent.prototype = window.Event.prototype;

    window.CustomEvent = CustomEvent;
}
