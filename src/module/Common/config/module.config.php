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
namespace Common;

return array(
    'view_helpers' => array(
        'invokables' => array(
            'pageHeader' => __NAMESPACE__ . '\View\Helper\PageHeader',
            'modal' => __NAMESPACE__ . '\View\Helper\Modal',
            'renderTitle' => __NAMESPACE__ . '\View\Helper\Title',
            'dateFormat' => __NAMESPACE__ . '\View\Helper\DateFormat'
        )
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'referer' => 'Common\Controller\Plugin\RefererProvider',
            'redirect' => 'Common\Controller\Plugin\RedirectHelper'
        )
    ),
);