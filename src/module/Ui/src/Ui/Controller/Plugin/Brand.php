<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Ui\Controller\Plugin;

use Instance\Manager\InstanceManagerInterface;
use Ui\Options\BrandHelperOptions;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Helper\AbstractHelper;

class Brand extends AbstractPlugin
{
    /**
     * @var BrandHelperOptions
     */
    protected $options;

    /**
     * @var InstanceManagerInterface
     */
    protected $instanceManager;

    /**
     * @param BrandHelperOptions       $brandHelperOptions
     * @param InstanceManagerInterface $instanceManager
     */
    public function __construct(BrandHelperOptions $brandHelperOptions, InstanceManagerInterface $instanceManager)
    {
        $key                   = $instanceManager->getInstanceFromRequest()->getName();
        $this->instanceManager = $instanceManager;
        $this->options         = $brandHelperOptions->getInstance($key);
    }

    /**
     * @return $this
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @param bool $stripTags
     * @return string
     */
    public function getBrand($stripTags = false)
    {
        if ($stripTags) {
            return strip_tags($this->options->getName());
        }

        return $this->options->getName();
    }

    /**
     * @return string
     */
    public function getHeadTitle()
    {
        return $this->options->getHeadTitle();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->options->getDescription();
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->options->getLogo();
    }

    /**
     * @param bool $stripTags
     * @return string
     */
    public function getSlogan($stripTags = false)
    {
        if ($stripTags) {
            return strip_tags($this->options->getSlogan());
        }

        return $this->options->getSlogan();
    }
}
