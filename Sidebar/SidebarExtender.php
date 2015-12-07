<?php namespace Modules\Dynamicfield\Sidebar;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Contracts\Authentication;

class SidebarExtender implements \Maatwebsite\Sidebar\SidebarExtender
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param Authentication $auth
     *
     * @internal param Guard $guard
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Menu $menu
     *
     * @return Menu
     */
    public function extendWith(Menu $menu)
    {
        $menu->group(trans('janzz::menus.sidebar.components'), function (Group $group) {
            $group->item(trans('dynamicfield::dynamicfield.title.dynamicfield'), function (Item $item) {
                $item->icon('fa fa-cubes');
                $item->weight(50);
                $item->append('admin.dynamicfield.group.create');
                $item->route('admin.dynamicfield.group.index');
                $item->authorize(
                    $this->auth->hasAccess('dynamicfield.group.index')
                );
            });
        });

        return $menu;
    }
}
