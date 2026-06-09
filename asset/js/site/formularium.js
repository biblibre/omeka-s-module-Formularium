(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.formularium-resource-page-block-layout-formularium-form__collapse-button').forEach(el => {
            el.addEventListener('click', ev => {
                ev.preventDefault();
                const target = document.getElementById(ev.target.dataset.target);
                target?.classList.toggle('formularium-show');
            });
        });
    });
})();
