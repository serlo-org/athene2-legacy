<?php
/**
 * 
 * @author Aeneas Rekkas (aeneas.rekkas@serlo.org]
 * @copyright 2013 by www.serlo.org
 * @license LGPL
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL]
 */
namespace Link;

return [
    'di' => [
        'definition' => [
            'class' => [
                'Link\Service\LinkService' => [
                    'setObjectManager' => [
                        'required' => true
                    ],
                    'setTypeManager' => [
                        'required' => true
                    ]
                ]
            ]
        ],
        'instance' => [
            'preferences' => [
                'Link\Service\LinkServiceInterface' => 'Link\Service\LinkService'
            ]
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view'
        ]
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                ]
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ]
];