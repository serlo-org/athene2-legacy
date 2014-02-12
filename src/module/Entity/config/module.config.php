<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author         Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license        LGPL-3.0
 * @license        http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link           https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright      Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Entity;

return [
    'zfc_rbac'        => [
        'assertion_map' => [
            //'entity.license.update' => 'Authorization\Assertion\TenantAssertion',
        ]
    ],
    'class_resolver'  => [
        'Entity\Entity\EntityInterface' => 'Entity\Entity\Entity',
        'Entity\Entity\TypeInterface'   => 'Entity\Entity\Type'
    ],
    'di'              => [
        'allowed_controllers' => [
            __NAMESPACE__ . '\Controller\EntityController',
            __NAMESPACE__ . '\Controller\RepositoryController',
            __NAMESPACE__ . '\Controller\PageController',
            __NAMESPACE__ . '\Controller\TaxonomyController',
            __NAMESPACE__ . '\Controller\LinkController',
            __NAMESPACE__ . '\Controller\LicenseController'
        ],
        'definition'          => [
            'class' => [
                __NAMESPACE__ . '\Controller\TaxonomyController'   => [
                    'setEntityManager'   => [
                        'required' => true
                    ],
                    'setTaxonomyManager' => [
                        'required' => true
                    ],
                    'setInstanceManager' => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Controller\LinkController'       => [
                    'setEntityManager' => [
                        'required' => true
                    ],
                    'setLinkService'   => [
                        'required' => true
                    ],
                    'setModuleOptions' => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Controller\LicenseController'    => [
                    'setEntityManager'   => [
                        'required' => true
                    ],
                    'setInstanceManager' => [
                        'required' => true
                    ],
                    'setLicenseManager'  => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Controller\EntityController'     => [
                    'setEntityManager'   => [
                        'required' => true
                    ],
                    'setInstanceManager' => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Controller\RepositoryController' => [
                    'setEntityManager'        => [
                        'required' => true
                    ],
                    'setInstanceManager'      => [
                        'required' => true
                    ],
                    'setUserManager'          => [
                        'required' => true
                    ],
                    'setRepositoryManager'    => [
                        'required' => true
                    ],
                    'setModuleOptions'        => [
                        'required' => true
                    ],
                    'setAuthorizationService' => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Controller\PageController'       => [
                    'setAliasManager'  => [
                        'required' => true
                    ],
                    'setEntityManager' => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Manager\EntityManager'           => [
                    'setUuidManager'          => [
                        'required' => true
                    ],
                    'setObjectManager'        => [
                        'required' => true
                    ],
                    'setClassResolver'        => [
                        'required' => true
                    ],
                    'setTypeManager'          => [
                        'required' => true
                    ],
                    'setAuthorizationService' => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Provider\TokenProvider'          => []
            ]
        ],
        'instance'            => [
            'preferences' => [
                'Entity\Manager\EntityManagerInterface' => 'Entity\Manager\EntityManager'
            ]
        ]
    ],
    'service_manager' => [
        'factories' => [
            __NAMESPACE__ . '\Options\ModuleOptions' => __NAMESPACE__ . '\Factory\ModuleOptionsFactory'
        ]
    ],
    'view_helpers'    => [
        'factories' => [
            'entity' => __NAMESPACE__ . '\Factory\EntityHelperFactory'
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
    ]
];
