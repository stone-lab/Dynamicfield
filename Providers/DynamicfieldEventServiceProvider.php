<?php

namespace Modules\Dynamicfield\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class DynamicfieldEventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
            'Modules\Page\Events\PageWasCreated' => [
                    'Modules\Dynamicfield\Listener\AddNewProcess',
            ],
            'Modules\Page\Events\PageWasUpdated' => [
                    'Modules\Dynamicfield\Listener\UpdateProcess',
            ],
            'Modules\Page\Events\PageWasReplicated' => [
                    'Modules\Dynamicfield\Listener\ReplicateProcess',
            ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
