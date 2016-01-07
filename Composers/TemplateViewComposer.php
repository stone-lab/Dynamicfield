<?php

namespace Modules\Dynamicfield\Composers;

use Illuminate\Contracts\View\View;
use Modules\Page\Composers\TemplateViewComposer as PageTemplateViewComposer;

class TemplateViewComposer extends PageTemplateViewComposer
{
    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        parent::compose($view);
    }
}
