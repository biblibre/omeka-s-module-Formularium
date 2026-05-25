(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.actions-actions').forEach(el => {
            el.addEventListener('formularium:action-added', function(ev) {
                if (ev.target.classList.contains("formularium-form-action-type-create-user")) {
                    var chosen = $(ev.target).find(".chosen-select");
                    chosen.chosen();
                    chosen.trigger("chosen:updated");
                }
            });
        });
    });
})();
