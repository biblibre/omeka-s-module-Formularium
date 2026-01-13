(function () {
    'use strict';

    $(document).ready(function() {
        // Initiate the actions elements on load.
        $('.actions-form-element').each(function() {
            const actionsFormElement = this;
            const actions = this.querySelector('.actions-actions');
            // Enable action sorting.
            new Sortable(actions, {
                draggable: '.actions-action',
                handle: '.sortable-handle',
                animation: 150,
                easing: "cubic-bezier(1, 0, 0, 1)",
            });

            actionsFormElement.closest('form').addEventListener('submit', function (ev) {
                renameFormElements(actionsFormElement);
                for (const deletedAction of actionsFormElement.querySelectorAll('.actions-action.delete')) {
                    for (const el of deletedAction.querySelectorAll('input,select,textarea,button')) {
                        el.setAttribute('disabled', true);
                    }
                }
            });
        });

        $('.actions-add-button').on('click', function () {
            Omeka.openSidebar($('#action-selector'));
        });
    });

    // Handle action edit button.
    $(document).on('click', '.actions-action-edit-button', function(e) {
        e.preventDefault();
        this.closest('.actions-action').classList.toggle('edit');
    });

    // Handle action remove button.
    $(document).on('click', '.actions-action-remove-button', function(e) {
        e.preventDefault();
        this.closest('.actions-action').classList.add('delete');
    });

    // Handle action restore button.
    $(document).on('click', '.actions-action-restore-button', function(e) {
        e.preventDefault();
        this.closest('.actions-action').classList.remove('delete');
    });

    $(document).on('change keyup focusout', '.actions-action-internal-label-input', function(e) {
        const input = e.target;
        const span = input.closest('.actions-action').querySelector('.actions-action-internal-label');
        if (span) {
            span.textContent = input.value;
        }
    });

    $(document).on('click', '#action-selector .option', function (ev) {
        const template = document.createElement('template');
        template.innerHTML = ev.target.dataset.template;
        const fragment = template.content.cloneNode(true);
        const action = fragment.querySelector('.actions-action');
        action.classList.add('edit');
        document.querySelector('.actions-actions').append(action);

        Omeka.closeSidebar($(ev.target).closest('.sidebar'));

        const internalLabelInput = action.querySelector('[name="internal_label"]');
        internalLabelInput.focus();
        internalLabelInput.select();
    });


    function renameFormElements(formElement) {
        const elementName = formElement.dataset.name;
        const actions = formElement.querySelectorAll('.actions-action:not(.delete)');
        actions.forEach(function (action, i) {
            for (const el of action.querySelectorAll('input,select,textarea,button')) {
                if (el.name.length === 0) {
                    continue;
                }

                let localName = el.name;
                if (localName.startsWith(elementName)) {
                    localName = localName.substring(elementName.length);
                    localName = localName.replace(/^\[\d+\]/, '');
                }
                if (!localName.startsWith('[')) {
                    localName = '[' + localName.replace('[', '][');
                }
                if (!localName.endsWith(']')) {
                    localName += ']';
                }
                el.name = `${elementName}[${i}]${localName}`;
            }
        });
    }

})();
