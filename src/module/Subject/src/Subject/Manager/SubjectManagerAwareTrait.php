<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Subject\Manager;

trait SubjectManagerAwareTrait
{

    /**
     * @var SubjectManagerInterface
     */
    protected $subjectManager;

    /**
     * @param SubjectManagerInterface $subjectManager
     * @return self
     */
    public function setSubjectManager(SubjectManagerInterface $subjectManager)
    {
        $this->subjectManager = $subjectManager;
        return $this;
    }

    /**
     * @return SubjectManagerInterface
     */
    public function getSubjectManager()
    {
        return $this->subjectManager;
    }
}
