<?php
/**
 * 
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author	Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license	LGPL-3.0
 * @license	http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link		https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
return array(
    'subject' => array(
        'plugins' => array(
            'invokables' => array(
                'topic' => 'Application\Subject\Plugin\Topic\TopicPlugin'
            )
        ),
        'instances' => array(
            'math' => array(
                'plugins' => array(
                    array(
                        'name' => 'topic',
                    )
                )
            )
        ),
    ),
    'router' => array(
        'routes' => array(
            'subject' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'may_terminate' => true,
                'options' => array(
                    'route' => '/{subject}[/:subject]',
                    'defaults' => array(
                        'controller' => 'Application\Subject\Provider\Home\Controller\HomeController',
                        'action' => 'index'
                    )
                ),
                'child_routes' => array(
                    'plugin' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => ''
                        ),
                        'child_routes' => array(
                            'topic' => array(
                                'may_terminate' => true,
                                'type' => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route' => '/{topic}/:path',
                                    'defaults' => array(
                                        'controller' => 'Application\Subject\Plugin\Topic\Controller\TopicController',
                                        'action' => 'index'
                                    ),
                                    'constraints' => array(
                                        'path' => '(.)+'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    ),
    'di' => array(
        'allowed_controllers' => array(
            'Application\Subject\Plugin\Topic\Controller\TopicController'
        // 'Application\Subject\DefaultSubject\Controller\TextExerciseController'
                ),
        'definition' => array(
            'class' => array(
                'Application\Subject\Plugin\Topic\Controller\TopicController' => array(
                    'setSubjectManager' => array(
                        'required' => 'true'
                    )
                )
            )
        ),
        'instances' => array(
            'Subject\SubjectManagerInterface' => 'Subject\SubjectManager'
        )
    )
);