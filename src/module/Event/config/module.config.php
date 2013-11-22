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
namespace Event;

return array(
    'class_resolver' => array(
        'Event\Entity\EventLogInterface' => 'Event\Entity\EventLog',
        'Event\Entity\EventInterface' => 'Event\Entity\Event',
        'Event\Entity\EventParameterInterface' => 'Event\Entity\EventParameter',
        'Event\Entity\EventParameterNameInterface' => 'Event\Entity\EventParameterName',
    ),
    'di' => array(
        'definition' => array(
            'class' => array(
                __NAMESPACE__ . '\EventManager' => array(
                    'setClassResolver' => array(
                        'required' => true
                    ),
                    'setObjectManager' => array(
                        'required' => true
                    )
                ),
                __NAMESPACE__ . '\Listener\UserControllerListener' => array(
                    'setEventManager' => array(
                        'required' => true
                    )
                ),
                __NAMESPACE__ . '\Listener\DiscussionControllerListener' => array(
                    'setEventManager' => array(
                        'required' => true
                    )
                )
            )
        ),
        'instance' => array(
            'preferences' => array(
                __NAMESPACE__ . '\EventManagerInterface' => __NAMESPACE__ . '\EventManager'
            )
        )
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                )
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    )
);