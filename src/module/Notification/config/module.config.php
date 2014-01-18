<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Notification;

use Notification\View\Helper\Notification;

return array(
    'view_helpers'   => array(
        'factories' => array(
            'notifications' => function ($sm) {
                    $helper = new Notification();
                    $helper->setUserManager(
                        $sm->getServiceLocator()->get('User\Manager\UserManager')
                    );
                    $helper->setNotificationManager(
                        $sm->getServiceLocator()->get('Notification\NotificationManager')
                    );

                    return $helper;
                },
        )
    ),
    'class_resolver' => array(
        'Notification\Entity\NotificationEventInterface' => 'Notification\Entity\NotificationEvent',
        'Notification\Entity\NotificationInterface'      => 'Notification\Entity\Notification',
        'Notification\Entity\SubscriptionInterface'      => 'Notification\Entity\Subscription'
    ),
    'di'             => array(
        'allowed_controllers' => array(
            __NAMESPACE__ . '\Controller\WorkerController'
        ),
        'definition'          => array(
            'class' => array(
                __NAMESPACE__ . '\Listener\DiscussionControllerListener' => array(
                    'setSubscriptionManager' => array(
                        'required' => true
                    )
                ),
                __NAMESPACE__ . '\Listener\RepositoryManagerListener'    => array(
                    'setSubscriptionManager' => array(
                        'required' => true
                    ),
                    'setUserManager'         => array(
                        'required' => true
                    )
                ),
                __NAMESPACE__ . '\Notification\SubscriptionManager'      => array(
                    'setClassResolver' => array(
                        'required' => true
                    ),
                    'setObjectManager' => array(
                        'required' => true
                    )
                ),
                __NAMESPACE__ . '\NotificationManager'                   => array(
                    'setClassResolver'  => array(
                        'required' => true
                    ),
                    'setObjectManager'  => array(
                        'required' => true
                    ),
                    'setServiceLocator' => array(
                        'required' => true
                    )
                ),
                __NAMESPACE__ . '\NotificationWorker'                    => array(
                    'setUserManager'         => array(
                        'required' => true
                    ),
                    'setObjectManager'       => array(
                        'required' => true
                    ),
                    'setSubscriptionManager' => array(
                        'required' => true
                    ),
                    'setNotificationManager' => array(
                        'required' => true
                    ),
                    'setClassResolver'       => array(
                        'required' => true
                    )
                ),
                __NAMESPACE__ . '\Controller\WorkerController'           => array(
                    'setNotificationWorker' => array(
                        'required' => true
                    )
                ),
            )
        ),
        'instance'            => array(
            'preferences' => array(
                __NAMESPACE__ . '\SubscriptionManagerInterface' => __NAMESPACE__ . '\SubscriptionManager',
                __NAMESPACE__ . '\NotificationManagerInterface' => __NAMESPACE__ . '\NotificationManager'
            )
        )
    ),
    'router'         => array(
        'routes' => array(
            'notification' => array(
                'type'          => 'Zend\Mvc\Router\Http\Segment',
                'may_terminate' => true,
                'options'       => array(
                    'route' => '/notification'
                ),
                'child_routes'  => array(
                    'worker' => array(
                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                        'may_terminate' => true,
                        'options'       => array(
                            'route'    => '/worker',
                            'defaults' => array(
                                'controller' => __NAMESPACE__ . '\Notification\Controller\WorkerController',
                                'action'     => 'run'
                            )
                        )
                    )
                )
            ),

        )
    ),
    'doctrine'       => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                )
            ),
            'orm_default'             => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    )
);
