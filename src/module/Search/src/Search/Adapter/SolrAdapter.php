<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Search\Adapter;

use Common\Guard\StringGuardTrait;
use Normalizer\NormalizerInterface;
use Search\Exception;
use Search\Result;
use Solarium\Client;
use Solarium\QueryType\Update\Query\Query;
use Uuid\Manager\UuidManagerInterface;
use Zend\I18n\Translator\TranslatorInterface;

class SolrAdapter implements AdapterInterface
{
    use StringGuardTrait;

    const KEYWORD_DELIMITER = ';';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var UuidManagerInterface
     */
    protected $uuidManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Query
     */
    protected $update;

    /**
     * @param Client               $client
     * @param NormalizerInterface  $normalizer
     * @param TranslatorInterface  $translator
     * @param UuidManagerInterface $uuidManager
     */
    public function __construct(
        Client $client,
        NormalizerInterface $normalizer,
        TranslatorInterface $translator,
        UuidManagerInterface $uuidManager
    ) {
        $this->client      = $client;
        $this->normalizer  = $normalizer;
        $this->uuidManager = $uuidManager;
        $this->translator  = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function add($id, $title, $content, $type, $link, array $keywords, $instance = null)
    {
        $keywords               = implode(self::KEYWORD_DELIMITER, $keywords);
        $update                 = $this->update = $this->client->createUpdate();
        $document               = $update->createDocument();
        $document->id           = (string)$id;
        $document->title        = (string)$title;
        $document->content      = (string)$content;
        $document->content_type = (string)$type;
        $document->keywords     = (string)$keywords;
        $document->link         = (string)$link;
        $document->instance     = (string)$instance;

        $update->addDeleteById($id);
        $update->addDocument($document);
        $update->addCommit();
        $this->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id)
    {
        $update = $this->update = $this->client->createUpdate();
        $update->addDeleteById($id);
        $update->addCommit();
    }

    /**
     * {@inheritDoc}
     */
    public function erase()
    {
        $update = $this->update = $this->client->createUpdate();
        $update->addDeleteQuery('*:*');
        $update->addCommit();
        $this->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        if (!is_object($this->update)) {
            return;
        }
        $this->client->update($this->update);
        $this->update = null;
    }

    /**
     * {@inheritDoc}
     */
    public function search($query, $limit)
    {
        $container  = new Result\Container();
        $queryClass = $this->client->createSelect();
        // $types      = ['article', 'topic', 'course', 'video'];
        // $types      = implode(' OR ', $types);
        // $queryClass->createFilterQuery('typeFilter')->setQuery('content_type:(' . $types . ')');
        $hl = $queryClass->getHighlighting();
        $hl->setFields('content');
        $hl->setSimplePrefix('<strong>');
        $hl->setSimplePostfix('</strong>');
        $queryClass->setFields(['*', 'score']);
        $disMax = $queryClass->getDisMax();
        $disMax->setQueryFields('title^4 content keywords^2 type^3');
        $queryClass->setQuery($query);
        $queryClass->setRows($limit);
        $queryClass->addSort('score', $queryClass::SORT_DESC);
        $queryClass->setQueryDefaultOperator($queryClass::QUERY_OPERATOR_AND);
        $resultSet    = $this->client->select($queryClass);
        $highlighting = $resultSet->getHighlighting();

        foreach ($resultSet as $document) {
            $highlightedDoc = $highlighting->getResult($document->id);
            $id             = $document['id'];
            $title          = $document['title'];
            $content        = '...' . implode(' ... ', $highlightedDoc->getField('content')) . '...';
            $type           = ucfirst($document['content_type']);
            $keywords       = explode(self::KEYWORD_DELIMITER, $document['keywords']);
            if ($type) {
                $type = $this->translator->translate($type);
            }
            $item = new Result\Result($id, $title, $content, $type, $id, $keywords);
            $container->addResult($item);
        }

        return $container;
    }
}
