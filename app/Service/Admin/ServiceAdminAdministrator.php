<?php
declare(strict_types=1);

namespace App\Service\Admin;

use App\Dao\Admin\DaoAdminAdministrator;
use App\Exception\CustomMessageException;
use App\Model\Admin\ModelAdminAdministrator;
use App\Proxy\DefaultRedis;
use App\Security\AdminSecurity;
use Hyperf\Di\Annotation\Inject;

/**
 * @ServiceAdminAdministrator
 * @\App\Service\Admin\ServiceAdminAdministrator
 */
final class ServiceAdminAdministrator
{
    #[Inject]
    protected DefaultRedis $redis;

    #[Inject]
    protected AdminSecurity $security;

    #[Inject]
    protected DaoAdminAdministrator $daoAdminAdministrator;

    /**
     * @param string $username
     * @param string $password
     * @return array{0: string, 1: array}
     */
    public function login(string $username, string $password): array
    {
        $userinfo = $this->daoAdminAdministrator->getAdministratorByUsername($username);

        if (is_null($userinfo) || !password_verify($password, $userinfo->password)) {
            throw new CustomMessageException('账号或者密码错误');
        }

        if (!$userinfo->status) {
            throw new CustomMessageException('该账号已被禁用');
        }

        $token = $this->security->create(
            [
                'id' => $userinfo->id
            ], 60 * 60 * 24 * 30
        );

        $this->redis->setex("administrator_token:$token", 60 * 60 * 24 * 30, $userinfo->id);

        return [$token, $userinfo->toArray()];
    }

    /**
     * @param string $token
     * @return false|ModelAdminAdministrator|null
     */
    public function getAdministratorByToken(string $token): null|false|ModelAdminAdministrator
    {
        $userid = $this->redis->get("administrator_token:$token");
        if (!$userid) return null;

        $userid = (int)$userid;

        $administrator = $this->daoAdminAdministrator->getAdministratorById($userid);
        if (is_null($administrator)) return null;
        if ($administrator->status) return $this->daoAdminAdministrator->getAdministratorById($userid);

        $this->logout($token);
        return false;
    }

    /**
     * @param string $token
     * @return void
     */
    public function logout(string $token): void
    {
        $this->redis->del("administrator_token:$token");
    }
}