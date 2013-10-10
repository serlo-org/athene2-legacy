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
namespace Subject\Hydrator;

class Navigation
{
    use \Zend\ServiceManager\ServiceLocatorAwareTrait, \Subject\Manager\SubjectManagerAwareTrait, \Language\Manager\LanguageManagerAwareTrait;

    protected $path;

    public function setPath($path){
        $this->path = $path;
    }
    
    public function inject($config){
        $language = $this->getLanguageManager()->getLanguageFromRequest();
        $subjects = $this->getSubjectManager()->findSubjectsByLanguage($language);
        foreach ($subjects as $subject) {
            $config = array_merge_recursive($config, include $this->path . $language->getCode() . '/' . strtolower($subject->getName()) . '.config.php');
        }
        return $config;
    }
}