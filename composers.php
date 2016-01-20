<?php

/* view()->composer(['*::*.edit', '*::*.create'], Modules\Dynamicfield\Composers\DynamicfieldViewComposer::class); */
view()->composer(['dynamicfield::admin.group.edit'], Modules\Dynamicfield\Composers\TemplateViewComposer::class);
view()->composer('*', Modules\Dynamicfield\Composers\FrontendViewComposer::class);
