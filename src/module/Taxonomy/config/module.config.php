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
namespace Taxonomy;

return [
    'zfc_rbac'         => [
        'assertion_map' => [
            'taxonomy.get'                  => 'Authorization\Assertion\InstanceAssertion',
            'taxonomy.purge'                => 'Authorization\Assertion\InstanceAssertion',
            'taxonomy.create'               => 'Authorization\Assertion\RequestInstanceAssertion',
            'taxonomy.term.get'             => 'Authorization\Assertion\InstanceAssertion',
            'taxonomy.term.create'          => 'Authorization\Assertion\RequestInstanceAssertion',
            'taxonomy.term.update'          => 'Authorization\Assertion\InstanceAssertion',
            'taxonomy.term.trash'           => 'Authorization\Assertion\InstanceAssertion',
            'taxonomy.term.restore'         => 'Authorization\Assertion\InstanceAssertion',
            'taxonomy.term.sort'            => 'Authorization\Assertion\InstanceAssertion',
            'taxonomy.term.purge'           => 'Authorization\Assertion\InstanceAssertion',
            'taxonomy.term.associate'       => 'Authorization\Assertion\InstanceAssertion',
            'taxonomy.term.dissociate'      => 'Authorization\Assertion\InstanceAssertion',
            'taxonomy.term.associated.sort' => 'Authorization\Assertion\InstanceAssertion',
        ]
    ],
    'term_router'      => [
        'routes' => []
    ],
    'class_resolver'   => [
        __NAMESPACE__ . '\Entity\TaxonomyTypeInterface' => __NAMESPACE__ . '\Entity\TaxonomyType',
        __NAMESPACE__ . '\Entity\TaxonomyInterface'     => __NAMESPACE__ . '\Entity\Taxonomy',
        __NAMESPACE__ . '\Entity\TaxonomyTermInterface' => __NAMESPACE__ . '\Entity\TaxonomyTerm'
    ],
    'view_helpers'     => [
        'factories' => [
            'taxonomy' => __NAMESPACE__ . '\Factory\TaxonomyHelperFactory'
        ]
    ],
    'hydrator_plugins' => [
        'factories' => [
            __NAMESPACE__ . '\Hydrator\TaxonomyTermHydratorPlugin' => __NAMESPACE__ . '\Factory\TaxonomyTermHydratorPluginFactory'
        ]
    ],
    'taxonomy'         => [
        'types' => [
            'root' => [
                'allowed_parents' => [],
                'rootable'        => true
            ]
        ]
    ],
    'router'           => [
        'routes' => [
            'taxonomy' => [
                'type'         => 'literal',
                'options'      => [
                    'route' => '/taxonomy'
                ],
                'child_routes' => [
                    'taxonomy' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/:action/:id',
                            'defaults' => [
                                'controller' => __NAMESPACE__ . '\Controller\TaxonomyController'
                            ]
                        ]
                    ],
                    'term'     => [
                        'type'         => 'literal',
                        'options'      => [
                            'route'    => '/term',
                            'defaults' => [
                                'controller' => __NAMESPACE__ . '\Controller\TermController'
                            ],
                        ],
                        'child_routes' => [
                            'get'             => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'      => '/get/:term',
                                    'constrains' => ['term' => '[0-9]+'],
                                    'defaults'   => [
                                        'controller' => __NAMESPACE__ . '\Controller\GetController',
                                        'action'     => 'index'
                                    ]
                                ]
                            ],
                            'update'          => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/update/:term',
                                    'defaults' => [
                                        'action' => 'update'
                                    ]
                                ],
                            ],
                            'create'          => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/create/:taxonomy/:parent',
                                    'defaults' => [
                                        'action' => 'create',
                                    ]
                                ]
                            ],
                            'order'           => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/order/:term',
                                    'defaults' => [
                                        'action' => 'order'
                                    ]
                                ]
                            ],
                            'organize'        => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/organize/:term',
                                    'defaults' => [
                                        'action' => 'organize'
                                    ]
                                ]
                            ],
                            'organize-all'    => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/organize-all',
                                    'defaults' => [
                                        'action' => 'organize'
                                    ]
                                ]
                            ],
                            'sort-associated' => [
                                'type'    => 'segment',
                                'options' => [
                                    'route'    => '/sort/:association/:term',
                                    'defaults' => [
                                        'action' => 'orderAssociated'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'service_manager'  => [
        'factories' => [
            __NAMESPACE__ . '\Options\ModuleOptions'       => __NAMESPACE__ . '\Factory\ModuleOptionsFactory',
            __NAMESPACE__ . '\Manager\TaxonomyManager'     => __NAMESPACE__ . '\Factory\TaxonomyManagerFactory',
            __NAMESPACE__ . '\Provider\NavigationProvider' => __NAMESPACE__ . '\Factory\NavigationProviderFactory',
        ]
    ],
    'di'               => [
        'allowed_controllers' => [
            __NAMESPACE__ . '\Controller\TermController',
            __NAMESPACE__ . '\Controller\GetController'
        ],
        'definition'          => [
            'class' => [
                __NAMESPACE__ . '\Listener\EntityManagerListener' => [
                    'setTaxonomyManager' => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Form\TermForm'                  => [],
                __NAMESPACE__ . '\Controller\GetController'       => [],
                __NAMESPACE__ . '\Controller\TermController'      => [],
                __NAMESPACE__ . '\Hydrator\TaxonomyTermHydrator'  => []
            ]
        ],
        'instance'            => [
            'preferences' => [
                __NAMESPACE__ . '\Manager\TaxonomyManagerInterface' => __NAMESPACE__ . '\Manager\TaxonomyManager'
            ]
        ]
    ],
    'uuid'             => [
        'permissions' => [
            'Taxonomy\Entity\TaxonomyTerm' => [
                'trash'   => 'taxonomy.term.trash',
                'restore' => 'taxonomy.term.restore',
                'purge'   => 'taxonomy.term.purge'
            ],
        ]
    ],
    'doctrine'         => [
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
    ]
];