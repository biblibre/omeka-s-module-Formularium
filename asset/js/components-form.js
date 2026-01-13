(function () {
    'use strict';

    $(document).ready(function() {
        // Initiate the components elements on load.
        $('.components-form-element').each(function() {
            const componentsFormElement = this;
            const components = this.querySelector('.components-components');
            // Enable component sorting.
            new Sortable(components, {
                draggable: '.components-component',
                handle: '.sortable-handle',
                animation: 150,
                easing: "cubic-bezier(1, 0, 0, 1)",
            });

            componentsFormElement.closest('form').addEventListener('submit', function (ev) {
                renameFormElements(componentsFormElement);
                // FIXME Deleted required element trigger browser validation and the form wont submit
                componentsFormElement.querySelectorAll('.components-component.delete').forEach(function (el) {
                    el.querySelectorAll('input,select,textarea,button').forEach(el => {
                        el.setAttribute('disabled', true);
                    });
                })
            });
        });

        $('.components-add-button').on('click', function () {
            Omeka.openSidebar($('#component-selector'));
        });
    });

    // Handle component edit button.
    $(document).on('click', '.components-component-edit-button', function(e) {
        e.preventDefault();
        this.closest('.components-component').classList.toggle('edit');
    });

    // Handle component remove button.
    $(document).on('click', '.components-component-remove-button', function(e) {
        e.preventDefault();
        this.closest('.components-component').classList.add('delete');
    });

    // Handle component restore button.
    $(document).on('click', '.components-component-restore-button', function(e) {
        e.preventDefault();
        this.closest('.components-component').classList.remove('delete');
    });

    $(document).on('change keyup focusout', '.components-component-internal-label-input', function(e) {
        const input = e.target;
        const span = input.closest('.components-component').querySelector('.components-component-internal-label');
        if (span) {
            span.textContent = input.value;
        }
    });

    $(document).on('click', '#component-selector .option', function (ev) {
        const template = document.createElement('template');
        template.innerHTML = ev.target.dataset.template;
        const fragment = template.content.cloneNode(true);
        const component = fragment.querySelector('.components-component');
        component.classList.add('edit');
        document.querySelector('.components-components').append(component);

        component.dispatchEvent(new CustomEvent('formularium:component-added', { bubbles: true }));

        Omeka.closeSidebar($(ev.target).closest('.sidebar'));

        const internalLabelInput = component.querySelector('[name="internal_label"]');
        internalLabelInput.focus();
        internalLabelInput.select();
    });


    function renameFormElements(formElement) {
        const elementName = formElement.dataset.name;
        const components = formElement.querySelectorAll('.components-component:not(.delete)');
        components.forEach(function (component, i) {
            for (const el of component.querySelectorAll('input,select,textarea,button')) {
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
