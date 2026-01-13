(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.formularium-form-component-type-html textarea').forEach(function (el) {
            CKEDITOR.inline(el);
        });

        document.querySelectorAll('.components-components').forEach(el => {
            el.addEventListener('formularium:component-added', function (ev) {
                if (ev.target.classList.contains('formularium-form-component-type-html')) {
                    const textarea = ev.target.querySelector('textarea');
                    CKEDITOR.inline(textarea);
                }
            });
        });
    });
})()
