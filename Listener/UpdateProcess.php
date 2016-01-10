<?php

namespace Modules\Dynamicfield\Listener;

use Modules\Blog\Entities\Post;
use Modules\Dynamicfield\Utility\DynamicFields;
use Modules\Page\Entities\Page;

class UpdateProcess
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
     * @param PageWasUpdated $event
     */
    public function pageHandle($event)
    {
        $page = Page::find($event->pageId);
        $this->saveDynamicData($page, $event->data);
    }
    /**
     * Handle the event.
     *
     * @param BlogWasUpdated $event
     */
    public function blogHandle($event)
    {
        $post = Post::find($event->blogId);
        $this->saveDynamicData($post, $event->data);
    }
    public function handle($event)
    {
    }
    public function subscribe($events)
    {
        $events->listen(
            'Modules\Blog\Events\BlogWasUpdated',
            'Modules\Dynamicfield\Listener\UpdateProcess@blogHandle'
        );

        $events->listen(
            'Modules\Page\Events\PageWasUpdated',
            'Modules\Dynamicfield\Listener\UpdateProcess@pageHandle'
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
