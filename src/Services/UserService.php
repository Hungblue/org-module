<?php

namespace KeyHoang\OrgModule\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use KeyHoang\OrgModule\Models\User;
use KeyHoang\OrgModule\Models\UserNoSQL;
use YaangVu\LaravelBase\Base\BaseService;

/**
 * @Author      hungnv1
 * @Date        DummyDate
 */
class UserService extends BaseService
{
    public function __construct(private Model $model = new User(), private readonly ?string $alias = null)
    {
        if (config('database.default') == 'mongodb') {
            $this->model = new UserNoSQL();
        }
        parent::__construct($this->model, $this->alias);
    }

    public function sync($user): bool
    {
        if ($user?->deleted_at ?? false) {
            $this->model->query()->where('sso_id', '=', $user->sso_id)->delete();
            Log::info("Delete user sso_id: " . $user->sso_id . "  Success");

            return true;
        }

        $userModel = $this->model->query()->where('sso_id', '=', $user->sso_id)->first();
        if (!$userModel) {
            $userModel = $this->model;
        }

        $userModel->sso_id       = $user->sso_id;
        $userModel->username     = $user->username;
        $userModel->full_name    = $user->full_name;
        $userModel->email        = $user->email;
        $userModel->phone_number = $user->phone_number;
        $userModel->staff_code   = $user->staff_code;
        $userModel->position     = $user->position;
        if ($user->user_department) {
            $userDepartment             = is_array($user->user_department) ? (object)$user->user_department
                : $user->user_department;
            $department                 = is_array($userDepartment->department) ? (object)$userDepartment->department
                : $userDepartment->department;
            $userModel->department      = $department->name ?? '';
            $userModel->department_code = $department->code ?? '';
            $userModel->unit            = '';
            if ($department->unit) {
                $unit            = is_array($department->unit) ? (object)$department->unit : $department->unit;
                $userModel->unit = $unit->name;
            }
        }

        try {
            $userModel->save();
            Log::info("Update user sso_id: " . $user->sso_id . "  Success");
        } catch (Exception $e) {
            Log::info("Sync Fail : " . $e->getMessage());
        }

        return true;
    }
}
