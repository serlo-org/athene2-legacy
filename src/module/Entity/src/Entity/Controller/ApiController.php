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
namespace Entity\Controller;

use Common\Filter\PreviewFilter;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Entity\Filter\EntityAgeCollectionFilter;
use Entity\Manager\EntityManagerInterface;
use Markdown\Exception\RuntimeException;
use Markdown\Service\RenderServiceInterface;
use Normalizer\NormalizerInterface;
use Uuid\Filter\NotTrashedCollectionFilter;
use Versioning\Filter\HasCurrentRevisionCollectionFilter;
use Zend\Feed\Writer\Feed;
use Zend\Filter\FilterChain;
use Zend\View\Model\FeedModel;
use Zend\View\Model\JsonModel;

class ApiController extends AbstractController
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var PreviewFilter
     */
    protected $descriptionFilter;
    /**
     * @var RenderServiceInterface
     */
    protected $renderService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param NormalizerInterface    $normalizer
     * @param RenderServiceInterface $renderService
     */
    function __construct(
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer,
        RenderServiceInterface $renderService
    ) {
        $this->normalizer        = $normalizer;
        $this->entityManager     = $entityManager;
        $this->renderService     = $renderService;
        $this->descriptionFilter = new PreviewFilter(300);
    }

    public function exportAction()
    {
        $type     = $this->params('type');
        $entities = $this->getEntityManager()->findEntitiesByTypeName($type);
        $chain    = new FilterChain();
        $chain->attach(new HasCurrentRevisionCollectionFilter());
        $chain->attach(new NotTrashedCollectionFilter());
        $entities = $chain->filter($entities);
        $data     = $this->normalize($entities);
        return new JsonModel($data);
    }

    public function latestAction()
    {
        $type     = $this->params('type');
        $age      = (int)$this->params('age');
        $maxAge   = new DateTime($age . ' days ago');
        $entities = $this->getEntityManager()->findEntitiesByTypeName($type);
        $chain    = new FilterChain();
        $chain->attach(new EntityAgeCollectionFilter($maxAge));
        $chain->attach(new NotTrashedCollectionFilter());
        $entities = $chain->filter($entities);
        $data     = $this->normalize($entities);
        return new JsonModel($data);
    }

    public function rssAction()
    {
        $feed     = new Feed();
        $type     = $this->params('type');
        $age      = (int)$this->params('age');
        $maxAge   = new DateTime($age . ' days ago');
        $entities = $this->getEntityManager()->findEntitiesByTypeName($type);
        $chain    = new FilterChain();
        $chain->attach(new EntityAgeCollectionFilter($maxAge));
        $chain->attach(new NotTrashedCollectionFilter());
        $entities = $chain->filter($entities);
        $data     = $this->normalize($entities);

        foreach ($data as $item) {
            $entry = $feed->createEntry();
            $entry->setTitle($item['title']);
            $entry->setDescription($item['description']);
            $entry->setId($item['guid']);
            $entry->setLink($item['link']);
            foreach($item['keywords'] as $keyword){
                $entry->addCategory(['term' => $keyword]);
            }
            $entry->setDateModified($item['lastModified']);
            $feed->addEntry($entry);
        }

        $feed->setTitle($this->brand()->getHeadTitle());
        $feed->setDescription($this->brand()->getDescription());
        $feed->setDateModified(time());
        $feed->setLink($this->url()->fromRoute('home', [], ['force_canonical' => true]));
        $feed->setFeedLink(
            $this->url()->fromRoute('entity/api/rss', ['type' => $type, 'age' => $age], ['force_canonical' => true]),
            'atom'
        );
        $feed->export('atom');
        $feedModel = new FeedModel();
        $feedModel->setFeed($feed);

        return $feedModel;
    }

    protected function normalize(Collection $collection)
    {
        $data = [];
        foreach ($collection as $entity) {
            $normalized  = $this->normalizer->normalize($entity);
            $description = $normalized->getMetadata()->getDescription();

            try {
                $description = $this->renderService->render($description);
            } catch (RuntimeException $e) {
                // nothing to do
            }

            $description = $this->descriptionFilter->filter($description);
            $item        = [
                'title'        => $normalized->getTitle(),
                'description'  => $description,
                'guid'         => (string) $entity->getId(),
                'keywords'     => $normalized->getMetadata()->getKeywords(),
                'link'         => $this->url()->fromRoute($normalized->getRouteName(), $normalized->getRouteParams()),
                'lastModified' => $normalized->getMetadata()->getLastModified()
            ];
            $data[]      = $item;
        }
        return $data;
    }
}
 