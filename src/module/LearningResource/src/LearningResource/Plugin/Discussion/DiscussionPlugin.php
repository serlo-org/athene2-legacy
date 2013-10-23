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
namespace LearningResource\Plugin\Discussion;

use Entity\Plugin\AbstractPlugin;
use Discussion\Form\DiscussionForm;

class DiscussionPlugin extends AbstractPlugin
{
    use\Discussion\DiscussionManagerAwareTrait;

    protected $form;

    protected function getDefaultConfig()
    {
        return array(
            'templates' => array(
                'widget' => 'learning-resource/plugin/discussion/widget',
                'discussions' => 'learning-resource/plugin/discussion/discussions'
            )
        );
    }

    public function getDiscussions($archived)
    {
        return $this->getDiscussionManager()->findDiscussionsOn($this->getEntityService()
            ->getEntity()
            ->getUuidEntity(), $archived);
    }

    public function getTemplate($template)
    {
        return $this->getOption('templates')[$template];
    }

    public function getForm()
    {
        if (! is_object($this->form)) {
            $this->form = new DiscussionForm();
        }
        return $this->form;
    }
}