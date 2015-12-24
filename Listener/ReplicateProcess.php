<?php

namespace Modules\Dynamicfield\Listener;

use Modules\Dynamicfield\Entities\Entity;
use Modules\Page\Events\PageWasReplicated;

class ReplicateProcess
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param PodcastWasPurchased $event
     */
    public function handle(PageWasReplicated $event)
    {
        $entity = new Entity();
        $entities = $entity->getFieldsByEntity($event->pageId);
        foreach ($entities as $item) {
            $item->duplicate($event->replicateId);
        }
    }
}
