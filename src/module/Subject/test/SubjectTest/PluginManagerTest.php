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
namespace SubjectTest;

use Subject\Plugin;
use Zend\ServiceManager\ServiceLocatorInterface;

class PluginManagerTest extends \PHPUnit_Framework_TestCase
{

    protected $pluginManager, $plugin;

    public function setUp()
    {
        parent::setUp();
        
        $this->plugin = $plugin = new Fake\PluginFake();
        
        $config = new \Zend\ServiceManager\Config(array(
            'factories' => array(
                'foobar' => function (ServiceLocatorInterface $sm) use($plugin)
                {
                    return $plugin;
                }
            )
        ));
        
        $this->pluginManager = new Plugin\PluginManager($config);
        $this->pluginManager->setSubjectService($this->getMock('Subject\Service\SubjectService'));
    }

    public function testGet()
    {
        $options = array(
            'foo' => 'bar'
        );
        
        $this->pluginManager->setPluginOptions($options);
        
        $this->assertSame($this->plugin, $this->pluginManager->get('foobar'));
    }
}