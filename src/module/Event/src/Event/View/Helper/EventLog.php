<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Event\View\Helper;

use Event\Entity\EventLogInterface;
use Event\EventManagerAwareTrait;
use Event\EventManagerInterface;
use Event\Exception;
use Zend\View\Helper\AbstractHelper;

class EventLog extends AbstractHelper
{
    use EventManagerAwareTrait;

    /**
     * @var string
     */
    protected $eventsTemplate = 'event/helper/events';

    /**
     * @var string
     */
    protected $eventTemplate = 'event/helper/event/default';

    /**
     * @param EventManagerInterface $eventManager
     */
    public function __construct(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function renderObjectLog($id)
    {
        $events = $this->getEventManager()->findEventsByObject($id);
        return $this->getView()->partial($this->eventsTemplate, ['events' => $events]);
    }

    public function renderUserLog($id)
    {
        $events = $this->getEventManager()->findEventsByActor($id);
        return $this->getView()->partial($this->eventsTemplate, ['events' => $events]);
    }

    public function renderEvent($id)
    {
        if (is_numeric($id)) {
            $event = $this->getEventManager()->getEvent($id);
        } elseif ($id instanceof EventLogInterface) {
            $event = $id;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected numeric, EventLogInterface or EventServiceInterface but got `%s`',
                gettype($id)
            ));
        }

        return $this->getView()->partial($this->eventTemplate, ['event' => $event]);
    }
}
