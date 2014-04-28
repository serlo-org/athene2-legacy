<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org]
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright   Copyright (c] 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/]
 */
namespace Flag;

return [
    'zfc_rbac'        => [
        'assertion_map' => [
            'flag.create' => 'Authorization\Assertion\RequestInstanceAssertion',
            'flag.get'    => 'Authorization\Assertion\InstanceAssertion',
            'flag.remove' => 'Authorization\Assertion\InstanceAssertion',
        ]
    ],
    'flag'            => [
        'types' => [
            'spam',
            'offensive',
            'other'
        ]
    ],
    'doctrine'        => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                ]
            ],
            'orm_default'             => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
    'service_manager' => [
        'factories' => [
            __NAMESPACE__ . '\Options\ModuleOptions' => __NAMESPACE__ . '\Factory\ModuleOptionsFactory'
        ]
    ],
    'di'              => [
        'allowed_controllers' => [
            __NAMESPACE__ . '\Controller\FlagController'
        ],
        'definition'          => [
            'class' => [
                __NAMESPACE__ . '\Manager\FlagManager'       => [],
                __NAMESPACE__ . '\Controller\FlagController' => []
            ]
        ],
        'instance'            => [
            'preferences' => [
                __NAMESPACE__ . '\Manager\FlagManagerInterface' => __NAMESPACE__ . '\Manager\FlagManager'
            ]
        ]
    ],
    'class_resolver'  => [
        __NAMESPACE__ . '\Entity\FlagInterface'         => __NAMESPACE__ . '\Entity\Flag',
        __NAMESPACE__ . '\Entity\TypeInterface'         => __NAMESPACE__ . '\Entity\Type',
        __NAMESPACE__ . '\Service\FlagServiceInterface' => __NAMESPACE__ . '\Service\FlagService'
    ],
    'router'          => [
        'routes' => [
            'flag' => [
                'type'         => 'Zend\Mvc\Router\Http\Segment',
                'options'      => [
                    'route'    => '/flag',
                    'defaults' => [
                        'controller' => __NAMESPACE__ . '\Controller\FlagController'
                    ]
                ],
                'child_routes' => [
                    'manage' => [
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/manage[/:type]',
                            'defaults' => [
                                'action' => 'manage'
                            ]
                        ]
                    ],
                    'add'    => [
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/add/:id',
                            'defaults' => [
                                'action' => 'add'
                            ]
                        ]
                    ],
                    'detail' => [
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/detail/:id',
                            'defaults' => [
                                'action' => 'detail'
                            ]
                        ]
                    ],
                    'remove' => [
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/remove/:id',
                            'defaults' => [
                                'action' => 'remove'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];