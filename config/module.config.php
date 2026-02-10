<?php

namespace Formularium;

return [
    'api_adapters' => [
        'invokables' => [
            'formularium_forms' => Api\Adapter\FormAdapter::class,
            'formularium_form_submissions' => Api\Adapter\FormSubmissionAdapter::class,
            'formularium_form_submission_files' => Api\Adapter\FormSubmissionFileAdapter::class,
        ],
    ],
    'block_layouts' => [
        'invokables' => [
            'formularium' => Site\BlockLayout\Formularium::class,
        ],
    ],
    'browse_defaults' => [
        'admin' => [
            'formularium_forms' => [
                'sort_by' => 'name',
                'sort_order' => 'asc',
            ],
            'formularium_form_submissions' => [
                'sort_by' => 'submitted',
                'sort_order' => 'desc',
            ],
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'formularium' => Service\Controller\Plugin\FormulariumFactory::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Formularium\Controller\Admin\Index' => Controller\Admin\IndexController::class,
            'Formularium\Controller\Admin\Form' => Controller\Admin\FormController::class,
            'Formularium\Controller\Admin\FormSubmission' => Controller\Admin\FormSubmissionController::class,
            'Formularium\Controller\Site\Form' => Controller\Site\FormController::class,
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
    ],
    'form_elements' => [
        'invokables' => [
            'Formularium\Form\Element\Components' => Form\Element\Components::class,
            'Formularium\Form\Element\Actions' => Form\Element\Actions::class,
        ],
        'factories' => [
            'Formularium\Form\FormForm' => Service\Form\FormFormFactory::class,
            'Formularium\Form\FormulariumForm' => Service\Form\FormulariumFormFactory::class,
        ],
    ],
    'formularium_form_action_types' => [
        'factories' => [
            'email' => Service\FormActionType\EmailFactory::class,
        ],
    ],
    'formularium_form_component_types' => [
        'invokables' => [
            'checkbox' => FormComponentType\Checkbox::class,
            'recaptcha' => FormComponentType\Recaptcha::class,
            'text_input' => FormComponentType\TextInput::class,
            'textarea' => FormComponentType\Textarea::class,
        ],
        'factories' => [
            'file_input' => Service\FormComponentType\FileInputFactory::class,
            'html' => Service\FormComponentType\HtmlFactory::class,
            'select' => Service\FormComponentType\SelectFactory::class,
            'user_email' => Service\FormComponentType\UserEmailFactory::class,
        ],
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Formularium',
                'route' => 'admin/formularium',
                'resource' => 'Formularium\Controller\Admin\Index',
                'privilege' => 'index',
                'class' => 'o-icon-module',
                'pages' => [
                    [
                        'label' => 'Forms',
                        'route' => 'admin/formularium/form',
                        'resource' => 'Formularium\Controller\Admin\Form',
                        'privilege' => 'browse',
                        'pages' => [
                            [
                                'route' => 'admin/formularium/form-id',
                                'visible' => false,
                            ],
                        ],
                    ],
                    [
                        'label' => 'Submissions',
                        'route' => 'admin/formularium/form-submission',
                        'resource' => 'Formularium\Controller\Admin\FormSubmission',
                        'privilege' => 'browse',
                        'pages' => [
                            [
                                'route' => 'admin/formularium/form-submission-id',
                                'visible' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'formularium' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/formularium',
                            'defaults' => [
                                '__NAMESPACE__' => 'Formularium\Controller\Admin',
                                'controller' => 'index',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'form' => [
                                'type' => \Laminas\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/form[/:action]',
                                    'constraints' => [
                                        'action' => '[a-z][a-z-]*',
                                    ],
                                    'defaults' => [
                                        'controller' => 'form',
                                        'action' => 'browse',
                                    ],
                                ],
                            ],
                            'form-id' => [
                                'type' => \Laminas\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/form/:id[/:action]',
                                    'constraints' => [
                                        'id' => '\d+',
                                        'action' => '[a-z][a-z-]*',
                                    ],
                                    'defaults' => [
                                        'controller' => 'form',
                                        'action' => 'show',
                                    ],
                                ],
                            ],
                            'form-submission' => [
                                'type' => \Laminas\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/form-submission[/:action]',
                                    'constraints' => [
                                        'action' => '[a-z][a-z-]*',
                                    ],
                                    'defaults' => [
                                        'controller' => 'form-submission',
                                        'action' => 'browse',
                                    ],
                                ],
                            ],
                            'form-submission-id' => [
                                'type' => \Laminas\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/form-submission/:id[/:action]',
                                    'constraints' => [
                                        'id' => '\d+',
                                        'action' => '[a-z][a-z-]*',
                                    ],
                                    'defaults' => [
                                        'controller' => 'form-submission',
                                        'action' => 'show',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'site' => [
                'child_routes' => [
                    'formularium' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/formularium',
                            'defaults' => [
                                '__NAMESPACE__' => 'Formularium\Controller\Site',
                            ],
                        ],
                        'child_routes' => [
                            'form-id' => [
                                'type' => \Laminas\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/form/:id[/:action]',
                                    'constraints' => [
                                        'id' => '\d+',
                                        'action' => '[a-z][a-z-]*',
                                    ],
                                    'defaults' => [
                                        'controller' => 'form',
                                        'action' => 'render',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Formularium\FormComponentTypeManager' => Service\FormComponentType\FormComponentTypeManagerFactory::class,
            'Formularium\FormActionTypeManager' => Service\FormActionType\FormActionTypeManagerFactory::class,
        ],
    ],
    'sort_defaults' => [
        'admin' => [
            'formularium_forms' => [
                'name' => 'Name', // @translate
            ],
            'formularium_form_submissions' => [
                'submitted' => 'Submission date', // @translate
                'handled' => 'Handling date', // @translate
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'formulariumFormComponents' => Form\View\Helper\FormComponents::class,
            'formulariumFormActions' => Form\View\Helper\FormActions::class,
        ],
        'factories' => [
            'formularium' => Service\View\Helper\FormulariumFactory::class,
        ],
        'delegators' => [
            'Laminas\Form\View\Helper\FormElement' => [
                Service\Delegator\FormElementDelegatorFactory::class,
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
];
