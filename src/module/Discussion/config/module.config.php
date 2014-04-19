<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org]
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c] 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/]
 */
namespace Discussion;

return [
    'zfc_rbac'        => [
        'assertion_map' => [
            'discussion.create'          => 'Authorization\Assertion\RequestInstanceAssertion',
            'discussion.trash'           => 'Authorization\Assertion\InstanceAssertion',
            'discussion.restore'         => 'Authorization\Assertion\InstanceAssertion',
            'discussion.purge'           => 'Authorization\Assertion\InstanceAssertion',
            'discussion.vote'            => 'Authorization\Assertion\InstanceAssertion',
            'discussion.archive'         => 'Authorization\Assertion\InstanceAssertion',
            'discussion.comment.trash'   => 'Authorization\Assertion\InstanceAssertion',
            'discussion.comment.restore' => 'Authorization\Assertion\InstanceAssertion',
            'discussion.comment.purge'   => 'Authorization\Assertion\InstanceAssertion',
            'discussion.comment.create'  => 'Authorization\Assertion\InstanceAssertion',
        ]
    ],
    'uuid'            => [
        'permissions' => [
            'Discussion\Entity\Comment' => [
                'trash'   => 'discussion.comment.trash',
                'restore' => 'discussion.comment.restore',
                'purge'   => 'discussion.comment.purge'
            ],
        ]
    ],
    'view_helpers'    => [
        'factories' => [
            'discussion' => __NAMESPACE__ . '\Factory\DiscussionHelperFactory'
        ]
    ],
    'service_manager' => [
        'factories' => [
            __NAMESPACE__ . '\DiscussionManager' => __NAMESPACE__ . '\Factory\DiscussionManagerFactory'
        ]
    ],
    'taxonomy'        => [
        'types' => [
            'forum' => [
                'allowed_associations' => [
                    'Discussion\Entity\CommentInterface'
                ],
                'allowed_parents'      => [
                    'forum',
                    'root'
                ],
                'rootable'             => false
            ]
        ]
    ],
    'class_resolver'  => [
        'Discussion\Entity\CommentInterface' => 'Discussion\Entity\Comment',
        'Discussion\Entity\VoteInterface'    => 'Discussion\Entity\Vote'
    ],
    'router'          => [
        'routes' => [
            'discussion' => [
                'type'          => 'Zend\Mvc\Router\Http\Segment',
                'options'       => [
                    'route' => ''
                ],
                'child_routes'  => [
                    'view'        => [
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'    => '/discussion/:id',
                            'defaults' => [
                                'controller' => 'Discussion\Controller\DiscussionController',
                                'action'     => 'show'
                            ]
                        ]
                    ],
                    'discussions' => [
                        'type'         => 'Zend\Mvc\Router\Http\Segment',
                        'options'      => [
                            'route'    => '/discussions',
                            'defaults' => [
                                'controller' => 'Discussion\Controller\DiscussionsController',
                                'action'     => 'index'
                            ]
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'get' => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/:id',
                                    'defaults' => [
                                        'controller' => 'Discussion\Controller\DiscussionController',
                                        'action'     => 'show'
                                    ]
                                ]
                            ],
                        ]
                    ],
                    'discussion'  => [
                        'type'         => 'Zend\Mvc\Router\Http\Segment',
                        'options'      => [
                            'route'    => '/discussion',
                            'defaults' => []
                        ],
                        'child_routes' => [
                            'start'   => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/start/:on/:forum',
                                    'defaults' => [
                                        'controller' => 'Discussion\Controller\DiscussionController',
                                        'action'     => 'start'
                                    ]
                                ]
                            ],
                            'comment' => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/comment/:discussion',
                                    'defaults' => [
                                        'controller' => 'Discussion\Controller\DiscussionController',
                                        'action'     => 'comment'
                                    ]
                                ]
                            ],
                            'vote'    => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'       => '/vote/:vote/:comment',
                                    'defaults'    => [
                                        'controller' => 'Discussion\Controller\DiscussionController',
                                        'action'     => 'vote'
                                    ],
                                    'constraints' => [
                                        'vote' => 'up|down'
                                    ]
                                ]
                            ],
                            'trash'   => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/trash/:comment',
                                    'defaults' => [
                                        'controller' => 'Discussion\Controller\DiscussionController',
                                        'action'     => 'trash'
                                    ]
                                ]
                            ],
                            'archive' => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'    => '/archive/:comment',
                                    'defaults' => [
                                        'controller' => 'Discussion\Controller\DiscussionController',
                                        'action'     => 'archive'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'di'              => [
        'allowed_controllers' => [
            'Discussion\Controller\DiscussionController',
            'Discussion\Controller\DiscussionsController'
        ],
        'definition'          => [
            'class' => [
                'Discussion\Form\DiscussionForm'              => [],
                'Discussion\Form\CommentForm'                 => [],
                'Discussion\Controller\DiscussionsController' => [
                    'setDiscussionManager' => [
                        'required' => true
                    ],
                    'setInstanceManager'   => [
                        'required' => true
                    ],
                    'setTaxonomyManager'   => [
                        'required' => true
                    ],
                    'setUserManager'       => [
                        'required' => true
                    ]
                ],
                'Discussion\Controller\DiscussionController'  => [
                    'setDiscussionManager' => [
                        'required' => true
                    ],
                    'setUuidManager'       => [
                        'required' => true
                    ],
                    'setInstanceManager'   => [
                        'required' => true
                    ],
                    'setUserManager'       => [
                        'required' => true
                    ],
                ]
            ]
        ],
        'instance'            => [
            'preferences' => [
                'Discussion\DiscussionManagerInterface' => 'Discussion\DiscussionManager'
            ]
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