<?php

namespace Modules\Dynamicfield\Listener;

use Modules\Dynamicfield\Utility\DynamicFields;
use Modules\Page\Entities\Page;
use Modules\Blog\Entities\Post;
use Modules\Page\Events\PageWasCreated;
use Modules\Blog\Events\BlogWasCreated;

class AddNewProcess
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }
	public function handle($event)
    {
       
    }
    /**
     * Handle the event.
     *
     * @param PageWasCreated $event
     */
    public function pageHandle($event)
    {
        $page = Page::firstOrNew(['id' => $event->pageId]);
        $this->saveDynamicData($page, $event->data);
    }
    /**
     * Handle the event.
     *
     * @param BlogWasCreated $event
     */
    public function blogHandle($event)
    {
        $post    = Post::firstOrNew(['id' => $event->blogId]);
        $this->saveDynamicData($post, $event->data);
    }
    public function subscribe($events)
    {
        $events->listen(
            'Modules\Blog\Events\BlogWasCreated',
            'Modules\Dynamicfield\Listener\AddNewProcess@blogHandle'
        );

        $events->listen(
            'Modules\Page\Events\PageWasCreated',
            'Modules\Dynamicfield\Listener\AddNewProcess@pageHandle'
        );
    }
    // save data to dynamic database ;
    private function saveDynamicData($entity, $data)
    {
        $fields = new DynamicFields($entity);
        $fields->init($data);
        $fields->save();
    }
}
