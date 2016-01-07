<?php

return [
    'name' => 'Dynamicfield',
    'files-path' => '/assets/dynamicfield/',
    'entity-type'=> array(
                            "Modules\Page\Entities\Page"=>"page",
                            "Modules\Blog\Entities\Post"=>"post",
                        ),
    'router'=> array(
                        "admin.page.page.create"=>"Modules\Page\Entities\Page",
                        "admin.blog.post.create"=>"Modules\Blog\Entities\Post"
                    ),
];
