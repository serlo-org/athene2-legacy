<?php
/**
 * 
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author	    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license	    LGPL-3.0
 * @license	    http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link		https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright	Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Markdown\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Markdown\Exception;

class MarkdownHelper extends AbstractHelper
{
    use\Markdown\Service\RenderServiceAwareTrait;
    use\Markdown\Service\CacheServiceAwareTrait;

    public function toHtml($content, $object = null, $field = null)
    {
        if ($object !== null && $field !== null) {
            try {
                return $this->getCacheService()->getCache($object, $field);
            } catch (Exception\RuntimeException $e) {}
        }
        return $this->getRenderService()->render($content);
    }
}