<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Notification;

return [
    'view_helpers'    => [
        'factories' => [
            'notifications' => __NAMESPACE__ . '\Factory\NotificationHelperFactory',
            'subscribe'     => __NAMESPACE__ . '\Factory\SubscribeFactory'
        ]
    ],
    'zfctwig'         => [
        'helper_manager' => [
            'factories' => [
                'subscribe' => __NAMESPACE__ . '\Factory\SubscribeFactory'
            ]
        ]
    ],
    'router'          => [
        'routes' => [
            'notification'  => [
                'type'          => 'literal',
                'options'       => [
                    'route'    => '/notification',
                    'defaults' => [
                        'controller' => 'Notification\Controller\NotificationController',
                    ]
                ],
                'may_terminate' => false,
                'child_routes'  => [
                    'read' => [
                        'type'    => 'literal',
                        'options' => [
                            'route'    => '/read',
                            'defaults' => [
                                'action' => 'read'
                            ]
                        ]
                    ],
                ]
            ],
            'subscription'  => [
                'type'          => 'segment',
                'options'       => [
                    'route'    => '',
                    'defaults' => [
                        'controller' => 'Notification\Controller\SubscriptionController',
                    ]
                ],
                'may_terminate' => false,
                'child_routes'  => [
                    'subscribe'   => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/subscribe/:object/:email',
                            'defaults' => [
                                'action' => 'subscribe'
                            ]
                        ]
                    ],
                    'unsubscribe' => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/unsubscribe/:object',
                            'defaults' => [
                                'action' => 'unsubscribe'
                            ]
                        ]
                    ],
                    'update'      => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/subscription/update/:object/:email',
                            'defaults' => [
                                'action' => 'update'
                            ]
                        ]
                    ]
                ],
            ],
            'subscriptions' => [
                'type'         => 'literal',
                'options'      => [
                    'route'    => '/subscriptions',
                    'defaults' => [
                        'controller' => 'Notification\Controller\SubscriptionController',
                    ]
                ],
                'child_routes' => [
                    'manage' => [
                        'type'    => 'literal',
                        'options' => [
                            'route'    => '/manage',
                            'defaults' => [
                                'action' => 'manage'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'service_manager' => [
        'factories' => [
            __NAMESPACE__ . '\NotificationManager' => __NAMESPACE__ . '\Factory\NotificationManagerFactory',
            __NAMESPACE__ . '\Storage\Storage' => __NAMESPACE__ . '\Factory\NotificationStorageFactory'
        ]
    ],
    'class_resolver'  => [
        __NAMESPACE__ . '\Entity\NotificationEventInterface' => __NAMESPACE__ . '\Entity\NotificationEvent',
        __NAMESPACE__ . '\Entity\NotificationInterface'      => __NAMESPACE__ . '\Entity\Notification',
        __NAMESPACE__ . '\Entity\SubscriptionInterface'      => __NAMESPACE__ . '\Entity\Subscription'
    ],
    'di'              => [
        'allowed_controllers' => [
            __NAMESPACE__ . '\Controller\WorkerController',
            __NAMESPACE__ . '\Controller\NotificationController',
            __NAMESPACE__ . '\Controller\SubscriptionController'
        ],
        'definition'          => [
            'class' => [
                __NAMESPACE__ . '\Listener\DiscussionManagerListener' => [
                    'setSubscriptionManager' => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Listener\RepositoryManagerListener' => [
                    'setSubscriptionManager' => [
                        'required' => true
                    ],
                    'setUserManager'         => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\SubscriptionManager'                => [
                    'setClassResolver' => [
                        'required' => true
                    ],
                    'setObjectManager' => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\NotificationWorker'                 => [
                    'setUserManager'         => [
                        'required' => true
                    ],
                    'setObjectManager'       => [
                        'required' => true
                    ],
                    'setSubscriptionManager' => [
                        'required' => true
                    ],
                    'setNotificationManager' => [
                        'required' => true
                    ],
                    'setClassResolver'       => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Controller\WorkerController'        => [
                    'setNotificationWorker' => [
                        'required' => true
                    ]
                ],
                __NAMESPACE__ . '\Controller\NotificationController'  => [],
            ]
        ],
        'instance'            => [
            'preferences' => [
                __NAMESPACE__ . '\SubscriptionManagerInterface' => __NAMESPACE__ . '\SubscriptionManager',
                __NAMESPACE__ . '\NotificationManagerInterface' => __NAMESPACE__ . '\NotificationManager'
            ]
        ]
    ],
    'console'         => [
        'router' => [
            'routes' => [
                'notification-worker' => [
                    'options' => [
                        'route'    => 'notification worker',
                        'defaults' => [
                            'controller' => __NAMESPACE__ . '\Controller\WorkerController',
                            'action'     => 'run'
                        ]
                    ]
                ],
            ]
        ],
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
