<?php

namespace tpadmin\controller\auth;

use tpadmin\model\Adminer as AdminerModel;
use tpadmin\model\Role as RoleModel;
// use tpadmin\service\upload\contract\Factory as Uploader;
use tpadmin\controller\Controller;
use think\exception\ValidateException;
use think\Request;

class Adminer extends Controller
{
    protected $adminer;

    public function __construct(AdminerModel $adminer)
    {
        parent::__construct();
        $this->adminer = $adminer;
    }

    public function index()
    {
        $paginate = $this->adminer->with('roles')->paginate();

        return $this->fetch('auth/adminer/index', [
            'paginate' => $paginate,
        ]);
    }

    public function create(Request $request)
    {
        $adminer = [];
        $roles = RoleModel::all();
        $role_ids = [];

        return $this->fetch('auth/adminer/create', compact('adminer', 'role_ids', 'roles'));
    }

    public function save(Request $request)
    {
        $data = $request->only(['name', 'password', 'password_confirm']);
        try {
            $adminer = AdminerModel::createItem($data);
        } catch (ValidateException $e) {
            $this->error($e->getError());
        } catch (\Exception $e) {
            $this->error($e->getError());
        }
        return $this->success('创建成功', url('[tpadmin.auth.adminer.index]'));
    }

    public function edit(Request $request, $id)
    {
        $adminer = $this->adminer->find(intval($id));
        if(empty($adminer)){
            $this->error('未找到被管理员信息');
        }

        $roles = RoleModel::all();
        $role_ids = []; //$adminer->roles()->column('id');

        return $this->fetch('auth/adminer/edit', compact('adminer', 'role_ids', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'password', 'password_confirm']);
        try {
            $adminer = AdminerModel::updateItem($id, $data);
        } catch (ValidateException $e) {
            $this->error($e->getError());
        } catch (\Exception $e) {
            $this->error($e->getError());
        }
        return $this->success('更新成功', url('[tpadmin.auth.adminer.index]'));
    }

    public function delete(Request $request, $id)
    {
        $id = intval($id);
        try {
            AdminerModel::deleteItem($id);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->success('删除成功');
    }

    // public function save(Request $request)
    // {
    //     try {
    //         if ($request->get('id') > 0) {
    //             $adminer = $this->updateAdminer($request);
    //         } else {
    //             $adminer = $this->createAdminer($request);
    //         }

    //         $this->updateRoles($adminer, $request->post('role_id', []));
    //     } catch (\Exception $e) {
    //         $this->error($e->getMessage());
    //     }
    //     $this->redirect('tadmin.auth.adminer');
    // }

    // protected function updateRoles($adminer, $newRoleIds)
    // {
    //     $roleIds = $adminer->roles()->column('id');

    //     $newRoleIds = array_map(function ($item) {
    //         return (int) $item;
    //     }, $newRoleIds);
    //     if (!empty($roleIds)) {
    //         $detachRoleIds = array_diff(
    //             array_merge($roleIds, $newRoleIds),
    //             $newRoleIds
    //         );
    //         $attachRoleIds = array_diff(
    //             $newRoleIds,
    //             array_intersect($roleIds, $newRoleIds)
    //         );
    //     } else {
    //         $attachRoleIds = $newRoleIds;
    //     }

    //     if (isset($attachRoleIds) && !empty($attachRoleIds)) {
    //         $adminer->roles()->attach(array_values($attachRoleIds));
    //     }

    //     if (isset($detachRoleIds) && !empty($detachRoleIds)) {
    //         $adminer->roles()->detach(array_values($detachRoleIds));
    //     }
    // }

    // protected function createAdminer(Request $request)
    // {
    //     $data = $request->only(['admin_account', 'admin_password', 'admin_password_confirm']);

    //     $this->validateAdminAccount($data);
    //     $this->validateAdminPassword($data);

    //     $adminer = $this->adminer->allowField(true)->create($data, true, true);

    //     if (!$adminer) {
    //         throw new \Exception('创建管理员失败');
    //     }

    //     return $adminer;
    // }

    // protected function updateAdminer(Request $request)
    // {
    //     $data = $request->only(['admin_password', 'admin_password_confirm', 'id']);
    //     if (isset($data['admin_password']) && !empty($data['admin_password'])) {
    //         $this->validateAdminPassword($data);
    //     } else {
    //         unset($data['admin_password']);
    //     }

    //     $adminer = $this->adminer->isUpdate(true)->update($data);
    //     if (!$adminer) {
    //         throw new \Exception('修改管理员失败');
    //     }

    //     return $adminer;
    // }

    // protected function validateAdminAccount(array $data)
    // {
    //     $validate = Validate::make([
    //         'admin_account' => 'require|alphaDash|max:16|unique:adminers',
    //     ], [
    //         'admin_account.require' => '登录账号必须',
    //         'admin_account.alphaDash' => '登录账号只能是字母、数字和下划线_及破折号-',
    //         'admin_account.max' => '登录账号最多不能超过16个字符',
    //         'admin_account.unique' => '登录账号被使用',
    //     ]);

    //     if (!$validate->check($data)) {
    //         throw new ValidateException($validate->getError());
    //     }
    // }

    // protected function validateAdminPassword(array $data)
    // {
    //     $validate = Validate::make([
    //         'admin_password' => 'require|alphaDash|confirm|max:16',
    //     ], [
    //         'admin_password.require' => '登录密码必须',
    //         'admin_password.alphaDash' => '登录密码只能是字母、数字和下划线_及破折号-',
    //         'admin_password.max' => '登录密码最多不能超过16个字符',
    //         'admin_password.confirm' => '登录密码和确认密码不一致',
    //     ]);

    //     if (!$validate->check($data)) {
    //         throw new ValidateException($validate->getError());
    //     }
    // }

    // public function delete(Request $request)
    // {
    //     try {
    //         if (1 == $request->get('id')) {
    //             throw new \Exception('该账号不能删除');
    //         }
    //         $this->adminer->destroy($request->get('id'));
    //     } catch (\Exception $e) {
    //         return $this->error($e->getMessage());
    //     }

    //     return $this->success('删除成功');
    // }
}