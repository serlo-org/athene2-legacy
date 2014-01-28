<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author         Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license        LGPL-3.0
 * @license        http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link           https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright      Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Markdown\Service;

use DNode\DNode;
use Markdown\Exception;
use Markdown\Options\ModuleOptions;
use React\EventLoop\StreamSelectLoop;

class HtmlRenderService implements RenderServiceInterface
{

    /**
     * @var StreamSelectLoop
     */
    protected $loop;

    /**
     * @var Donde
     */
    protected $dnode;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @return ModuleOptions $options
     */
    public function getModuleOptions()
    {
        return $this->options;
    }

    /**
     * @param ModuleOptions $options
     * @return self
     */
    public function setModuleOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return void
     */
    public function __construct()
    {
        $this->loop  = new StreamSelectLoop();
        $this->dnode = new DNode($this->loop);
    }

    /**
     * @see \Markdown\Service\RenderServiceInterface::render()
     */
    public function render($input)
    {
        $rendered = 'BROKEN PIPE';

        $this->dnode->connect(
            $this->options->getHost(),
            $this->options->getPort(),
            function ($remote, $connection) use ($input, &$rendered) {
                $remote->render(
                    $input,
                    function ($output, $exception = null, $error = null) use (&$rendered, $connection) {
                        if ($exception !== null) {
                            $connection->end();
                            throw new Exception\RuntimeException(sprintf(
                                'Bridge threw exception "%s" with message "%s".',
                                $exception,
                                $error
                            ));
                        }
                        $rendered = $output;
                        $connection->end();
                    }
                );
            }
        );

        $this->loop->run();

        return $rendered;
    }
}