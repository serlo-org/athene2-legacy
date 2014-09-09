<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Mailman\Listener;

use Common\Listener\AbstractSharedListenerAggregate;
use Mailman\MailmanAwareTrait;
use Mailman\MailmanInterface;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Renderer\RendererInterface;

abstract class AbstractListener extends AbstractSharedListenerAggregate
{
    use MailmanAwareTrait;
    use TranslatorAwareTrait;

    /**
     * @var PhpRenderer
     */
    protected $renderer;

    /**
     * @param MailmanInterface  $mailman
     * @param RendererInterface $phpRenderer
     * @param Translator        $translator
     */
    public function __construct(MailmanInterface $mailman, RendererInterface $phpRenderer, Translator $translator)
    {
        $this->mailman    = $mailman;
        $this->translator = $translator;
        $this->renderer   = $phpRenderer;
    }

    /**
     * @return PhpRenderer $renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }
}
