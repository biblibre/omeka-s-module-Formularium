(function () {
    'use strict';

    // CKEditor needs a unique id for each textarea
    function getRandomId () {
        const u8array = new Uint8Array(4);
        const randomString = crypto.getRandomValues(u8array).toHex();

        return `formularium-html-${randomString}`;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.formularium-form-component-type-html textarea').forEach(function (el) {
            el.setAttribute('id', getRandomId());
            CKEDITOR.inline(el);
        });

        document.querySelectorAll('.components-components').forEach(el => {
            el.addEventListener('formularium:component-added', function (ev) {
                if (ev.target.classList.contains('formularium-form-component-type-html')) {
                    const textarea = ev.target.querySelector('textarea');
                    textarea.setAttribute('id', getRandomId());
                    CKEDITOR.inline(textarea);
                }
            });
        });
    });
})()
