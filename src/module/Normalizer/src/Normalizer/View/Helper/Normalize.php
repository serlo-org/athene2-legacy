<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Normalizer\View\Helper;

use Common\Filter\PreviewFilter;
use Markdown\View\Helper\MarkdownHelper;
use Normalizer\NormalizerAwareTrait;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\HeadMeta;
use Zend\View\Helper\HeadTitle;

class Normalize extends AbstractHelper
{
    use NormalizerAwareTrait;

    public function __invoke($object = null)
    {
        if ($object === null) {
            return $this;
        }
        return $this->normalize($object);
    }

    public function headMeta($object)
    {
        /* @var $meta HeadMeta */
        $meta       = $this->getView()->plugin('headMeta');
        $normalized = $this->normalize($object);
        $title      = $normalized->getTitle();
        $keywords   = $normalized->getMetadata()->getKeywords();
        $preview    = $this->toPreview($object);
        $meta->setProperty('og:title', $title);
        $meta->appendName('description', $preview);
        $meta->appendName('keywords', implode(', ', $keywords));

        return $this;
    }

    public function headTitle($object)
    {
        /* @var $headTitle HeadTitle */
        $headTitle  = $this->getView()->plugin('headTitle');
        $normalized = $this->normalize($object);
        $title      = $normalized->getTitle();
        $headTitle($title);

        return $this;

    }

    public function possible($object)
    {
        try {
            $this->normalize($object);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function toAnchor($object, $forceCanonical = false)
    {
        $normalized = $this->normalize($object);
        return '<a href="' . $this->toUrl($object, $forceCanonical) . '">' . $normalized->getTitle() . '</a>';
    }

    public function toAuthor($object)
    {
        return $this->normalize($object)->getMetadata()->getAuthor();
    }

    public function toCreationDate($object)
    {
        return $this->normalize($object)->getMetadata()->getCreationDate();
    }

    public function toLastModified($object)
    {
        return $this->normalize($object)->getMetadata()->getLastModified();
    }

    public function toPreview($object)
    {
        /* @var $markdown MarkdownHelper */
        $markdown   = $this->getView()->plugin('markdown');
        $normalized = $this->normalize($object);
        $filter     = new PreviewFilter(152);
        $content    = $normalized->getContent();
        $content    = $markdown->toHtml($content);
        $preview    = $filter->filter($content);
        return $preview;
    }

    public function toTitle($object)
    {
        return $this->normalize($object)->getTitle();
    }

    public function toType($object)
    {
        return $this->normalize($object)->getType();
    }

    public function toUrl($object, $forceCanonical = false)
    {
        $normalized = $this->normalize($object);
        return $this->getView()->url(
            $normalized->getRouteName(),
            $normalized->getRouteParams(),
            ['force_canonical' => $forceCanonical]
        );
    }

    protected function normalize($object)
    {
        return $this->getNormalizer()->normalize($object);
    }
}
