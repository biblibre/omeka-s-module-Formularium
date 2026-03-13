(function () {
    'use strict';

    const byteToHex = [];
    for (let i = 0; i < 256; ++i) {
        byteToHex[i] = (i + 0x100).toString(16).substr(1);
    }

    // CKEditor needs a unique id for each textarea
    function getRandomId () {

        const bytes = new Uint8Array(4);
        crypto.getRandomValues(bytes);
        const randomString = byteToHex[bytes[0]] + byteToHex[bytes[1]] + byteToHex[bytes[2]] + byteToHex[bytes[3]];

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
