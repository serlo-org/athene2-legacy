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
namespace Contexter;

return [
    'zfc_rbac' => [
        'assertion_map' => [
            'blog.post.create' => 'Authorization\Assertion\LanguageAssertion',
            'blog.post.update' => 'Authorization\Assertion\LanguageAssertion',
            'blog.post.trash' => 'Authorization\Assertion\LanguageAssertion',
            'blog.post.delete' => 'Authorization\Assertion\LanguageAssertion',
            'blog.posts.view_all' => 'Authorization\Assertion\LanguageAssertion'
        ]
    ],
    'Manager\ContextManager' => [
        'router' => [
            'adapters' => [
                [
                    'adapter' => __NAMESPACE__ . '\Adapter\EntityPluginControllerAdapter',
                    'controllers' => [
                        [
                            'controller' => 'Entity\Plugin\Repository\Controller\RepositoryController',
                            'action' => 'addRevision'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'view_helpers' => [
        'factories' => [
            'contexter' => __NAMESPACE__ . '\Factory\ContexterHelperFactory'
        ]
    ],
    'class_resolver' => [
        'Contexter\Entity\ContextInterface' => 'Contexter\Entity\Context',
        'Contexter\Entity\TypeInterface' => 'Contexter\Entity\Type',
        'Contexter\Entity\RouteInterface' => 'Contexter\Entity\Route',
        'Contexter\Entity\RouteParameterInterface' => 'Contexter\Entity\RouteParameter'
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
    ],
];
