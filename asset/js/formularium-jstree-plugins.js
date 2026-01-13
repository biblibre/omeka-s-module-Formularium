$.jstree.plugins.formulariumeditcomponent = function(options, parent) {
    const container = $('<div>', {
        class: 'jstree-formulariumeditcomponent-container'
    });

    const editIcon = $('<i>', {
        class: 'jstree-icon jstree-formulariumeditcomponent-edit',
        attr: {
            role: 'presentation',
            'title': Omeka.jsTranslate('Edit component'),
            'aria-label': Omeka.jsTranslate('Edit component')
        },
    });

    // Toggle edit component container.
    this.toggleComponentEdit = function(node) {
        var container = node.children('.jstree-formulariumeditcomponent-container');
        node.toggleClass('jstree-formulariumeditcomponent-editmode');
        container.slideToggle();
    };

    this.bind = function() {
        parent.bind.call(this);
        // Toggle edit component container when icon is clicked.
        this.element.on(
            'click.jstree',
            '.jstree-formulariumeditcomponent-edit',
            $.proxy(function(e) {
                this.toggleComponentEdit($(e.currentTarget).closest('.jstree-node'));
            }, this)
        );

        // Add a component to the tree.
        $('#formularium-components').on(
            'click',
            'button.option',
            $.proxy(function(e) {
                var component = $(e.currentTarget);
                var nodeId = this.create_node('#', {
                    text: component.text(),
                    data: {
                        type: component.data('type'),
                        data: {}
                    }
                });
                this.toggleComponentEdit($('#' + nodeId));
            }, this)
        );

        // Prepare the components tree data for submission.
        // XXX Maybe not needed
        $('#formularium-form').on(
            'submit',
            $.proxy(function(e) {
                var instance = this;
                $('#component-tree :input[data-name]').each(function(index, element) {
                    var nodeObj = instance.get_node(element);
                    var element = $(element);
                    nodeObj.data['data'][element.data('name')] = element.val()
                });
                $('<input>', {
                    'type': 'hidden',
                    'name': 'jstree',
                    'val': JSON.stringify(instance.get_json())
                }).appendTo('#formularium-form');
            }, this)
        );

        // Open closed nodes if their inputs have validation errors
        document.body.addEventListener('invalid', $.proxy(function (e) {
            var target = $(e.target);
            if (!target.is(':input')) {
                return;
            }
            var node = target.closest('.jstree-node');
            if (node.length && !node.hasClass('jstree-formulariumeditcomponent-editmode')) {
                this.toggleComponentEdit(node);
            }
        }, this), true);
    };

    this.redraw_node = function(node, deep, is_callback, force_render) {
        node = parent.redraw_node.apply(this, arguments);
        if (node) {
            var nodeObj = this.get_node(node);
            if (typeof nodeObj.formulariumeditcomponent_container === 'undefined') {
                // The container has not been drawn. Draw it and its contents.
                nodeObj.formulariumeditcomponent_container = container.clone();
                $.post($('#component-tree').data('component-form-url'), nodeObj.data)
                    .done(function(data) {
                        nodeObj.formulariumeditcomponent_container.append(data);
                    });
            }
            var nodeJq = $(node);
            if (nodeObj.formulariumeditcomponent_container.hasClass('jstree-formulariumeditcomponent-editmode')) {
                // Node should retain the editmode class.
                nodeJq.addClass('jstree-formulariumeditcomponent-editmode');
            }
            var anchor = nodeJq.children('.jstree-anchor');
            anchor.append(editIcon.clone());
            nodeJq.children('.jstree-anchor').after(nodeObj.formulariumeditcomponent_container);
        }
        return node;
    };
};
