<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Mailman;

use Zend\Mail\Transport\SmtpOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

return [
    'mailman'         => [
        'adapters' => [
            'Mailman\Adapter\ZendMailAdapter'
        ]
    ],
    'service_manager' => [
        'factories' => [
            __NAMESPACE__ . '\Mailman'                                   => __NAMESPACE__ . '\Factory\MailmanFactory',
            __NAMESPACE__ . '\Options\ModuleOptions'                     => __NAMESPACE__ . '\Factory\ModuleOptionsFactory',
            __NAMESPACE__ . '\Adapter\ZendMailAdapter'                   => __NAMESPACE__ . '\Factory\ZendMailAdapterFactory',
            __NAMESPACE__ . '\Listener\AuthenticationControllerListener' => __NAMESPACE__ . '\Factory\AuthenticationControllerListenerFactory',
            __NAMESPACE__ . '\Listener\UserControllerListener'           => __NAMESPACE__ . '\Factory\UserControllerListenerFactory',
            __NAMESPACE__ . '\Listener\NotificationWorkerListener'       => __NAMESPACE__ . '\Factory\NotificationWorkerListenerFactory',
            'Zend\Mail\Transport\SmtpOptions'                            => function (ServiceLocatorInterface $sm) {
                    $config = $sm->get('config')['smtp_options'];
                    return new SmtpOptions($config);
                },
        ]
    ],
    'smtp_options'    => [
        'name'              => 'localhost.localdomain',
        'host'              => 'localhost',
        'connection_class'  => 'smtp',
        'connection_config' => [
            'username' => 'postmaster',
            'password' => ''
        ]
    ],
    'di'              => [
        'instance' => [
            'preferences' => [
                'Mailman\MailmanInterface' => 'Mailman\Mailman'
            ]
        ]
    ],
];
