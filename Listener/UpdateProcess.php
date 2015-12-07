<?php namespace Modules\Dynamicfield\Listener;

use Modules\Dynamicfield\Utility\DynamicFields;
use Modules\Page\Entities\Page;
use Modules\Page\Events\PageWasUpdated;

class UpdateProcess
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PodcastWasPurchased  $event
     * @return void
     */
    public function handle(PageWasUpdated $event)
    {
        $page            =  Page::find($event->pageId);
        $fields    = new DynamicFields($page) ;
        $fields->init($event->data) ;
        $fields->save();
    }
}
