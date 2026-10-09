/*
 * Cookie Consent: önálló JS, nem kell hozzá jQuery vagy Bootstrap.
 * Mentés ajaxszal, beállítások ablak a böngésző <dialog> elemével.
 * Sikeres mentés után a document-en "CConsent-formSubmitedSuccessful" esemény jön (detail: a megnyomott gomb).
 */
(function () {
    function init() {
        var wrapper = document.querySelector('[data-cc-wrapper]');
        if (!wrapper || wrapper.dataset.ccReady) {
            return;
        }
        wrapper.dataset.ccReady = '1';

        var form = wrapper.querySelector('form');
        var dialog = wrapper.querySelector('[data-cc-dialog]');
        var scrollY = 0;

        // Beállítások ablak; iOS-en a body overflow:hidden nem állítja meg a háttér görgetését, ezért a body-t rögzítjük
        function openDialog(event) {
            event.preventDefault();
            if (!dialog || dialog.open) {
                return;
            }
            scrollY = window.scrollY;
            document.body.style.position = 'fixed';
            document.body.style.top = -scrollY + 'px';
            document.body.style.left = '0';
            document.body.style.right = '0';
            dialog.showModal();
            dialog.focus();
        }

        function closeDialog() {
            if (dialog && dialog.open) {
                dialog.close();
            }
        }

        if (dialog) {
            dialog.addEventListener('close', function () {
                document.body.style.position = '';
                document.body.style.top = '';
                document.body.style.left = '';
                document.body.style.right = '';
                window.scrollTo(0, scrollY);
            });
            // Kattintás a sötét háttérre: bezárás
            dialog.addEventListener('click', function (event) {
                if (event.target === dialog) {
                    closeDialog();
                }
            });
        }

        wrapper.querySelectorAll('[data-cc-open]').forEach(function (el) {
            el.addEventListener('click', openDialog);
        });
        wrapper.querySelectorAll('[data-cc-close]').forEach(function (el) {
            el.addEventListener('click', closeDialog);
        });

        // Mentés ajaxszal; utána a sáv és az ablak eltűnik
        wrapper.querySelectorAll('[data-cc-submit]').forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();

                var xhr = new XMLHttpRequest();
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        closeDialog();
                        wrapper.style.display = 'none';
                        document.dispatchEvent(new CustomEvent('CConsent-formSubmitedSuccessful', {detail: btn}));
                    }
                };
                xhr.open('POST', form.getAttribute('action') || location.href);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send(serializeForm(form, btn));
            });
        });
    }

    // Csak a bekapcsolt kategóriák és a megnyomott gomb neve megy el (a többi gombé nem)
    function serializeForm(form, clickedButton) {
        var serialized = [];
        for (var i = 0; i < form.elements.length; i++) {
            var field = form.elements[i];
            if (!field.name || field.disabled || field.type === 'submit' || field.type === 'button') {
                continue;
            }
            if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
                serialized.push(encodeURIComponent(field.name) + '=' + encodeURIComponent(field.value));
            }
        }
        serialized.push(encodeURIComponent(clickedButton.getAttribute('name')) + '=');
        return serialized.join('&');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
