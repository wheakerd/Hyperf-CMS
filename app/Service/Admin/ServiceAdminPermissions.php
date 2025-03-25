<?php
declare(strict_types=1);

namespace App\Service\Admin;

use App\Dao\Admin\DaoAdminRoles;
use App\Dao\Admin\DaoAdminRouter;
use Hyperf\Di\Annotation\Inject;

/**
 * @ServiceAdminPermissions
 * @\App\Service\Admin\ServiceAdminPermissions
 */
final class ServiceAdminPermissions
{
    #[Inject]
    protected DaoAdminRoles $adminAdminRoles;

    #[Inject]
    protected DaoAdminRouter $adminRouter;

    public function getMenuList(int $roleId): array
    {
        $routerIds = $roleId === 1
            ? $this->adminRouter->getAllId()
            : $this->adminAdminRoles->getRouters($roleId);

        $routes = $this->adminRouter->getSelectInId($routerIds);

        $getMenuList = function (array &$list, int $parentId = 0) use (&$getMenuList) {
            $data = [];

            foreach ($list as &$item) if ($item ['parentId'] == $parentId) {

                $metadata = array_filter($item, fn($value, $key) => in_array($key, [
                        'name', 'path', 'component', 'redirect',
                    ]) && !is_null($value), ARRAY_FILTER_USE_BOTH);
                $metadata ['meta'] = $item;

                if ($children = $getMenuList($list, $item ['id'])) {
                    $metadata += compact('children');
                }
                $data [] = $metadata;
                unset($item);
            }

            return $data;
        };

        return $getMenuList($routes);
    }
}