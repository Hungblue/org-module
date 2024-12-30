<?php

namespace KeyHoang\OrgModule\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use KeyHoang\OrgModule\Models\DepartmentNoSQL;
use KeyHoang\OrgModule\Models\User;
use KeyHoang\OrgModule\Models\UserNoSQL;
use KeyHoang\OrgModule\Traits\RabbitMQProducer;
use YaangVu\LaravelBase\Base\BaseService;

/**
 * @Author      hungnv1
 * @Date        DummyDate
 */
class UserService extends BaseService
{
    use RabbitMQProducer;

    private string $mongodb   = 'mongodb';
    private bool   $isMongodb = false;

    public function __construct(private Model $model = new User(), private readonly ?string $alias = null)
    {
        $this->isMongodb = (config('database.default') == $this->mongodb);
        $this->model     = $this->isMongodb ? new UserNoSQL() : new User();
        parent::__construct($this->model, $this->alias);
    }

    public function sync($user): bool
    {
        if ($user?->deleted_at ?? false) {
            $this->model->query()->where('uuid', '=', $user->uuid)->delete();
            Log::info("Delete user uuid: " . $user->uuid . "  Success");

            return true;
        }

        $isCreateNew = false;
        $userModel   = $this->model->query()
                                   ->where('uuid', '=', $user->uuid)
                                   ->orWhere('staff_code', '=', $user->staff_code)
                                   // ->withTrashed() //note
                                   ->first();
        if (!$userModel) {
            $userModel   = $this->isMongodb ? new UserNoSQL() : new User();
            $isCreateNew = true;
        }

        $userModel->sso_id       = $user->sso_id;
        $userModel->username     = $user->username;
        $userModel->full_name    = $user->full_name;
        $userModel->email        = $user->email;
        $userModel->phone_number = $user->phone_number_1;
        $userModel->staff_code   = $user->staff_code;
        $userModel->position     = $user->position;
        $userModel->avatar       = $user->avatar;
        $userModel->uuid         = $user->uuid;
        $userModel->gender       = $user->gender;
        if ($user->user_status) {
            $userStatus        = (object)$user->user_status;
            $userModel->status = $userStatus->name;
        }

        $department = is_array($user->department) ? (object)$user->department : $user->department;
        #Set khoi/ban/phong
        if ($department) {
            if ($this->isMongodb) {
                $departmentMongoDb        = DepartmentNoSQL::query()
                                                           ->where('uuid', '=', $department->uuid)
                                                           ->first();
                $userModel->department_id = $departmentMongoDb->_id ?? null;
            }
            $userModel->department                  = $department->name ?? null;
            $userModel->department_code             = $department->code ?? null;
            $userModel->department_abbreviated_name = $department->abbreviated_name ?? null;
        }
        #Set unit
        $unit = is_array($user->unit) ? (object)$user->unit : $user->unit;
        if ($unit) {
            if ($this->isMongodb) {
                $unitMongoDb        = DepartmentNoSQL::query()->where('uuid', '=', $unit->uuid)->first();
                $userModel->unit_id = $unitMongoDb->_id;
            }
            $userModel->unit                  = $unit->name;
            $userModel->unit_code             = $unit->code;
            $userModel->unit_abbreviated_name = $unit->abbreviated_name ?? null;
        }

        if ($this->isMongodb && $isCreateNew) {
            $userModel->is_active = true;
        }

        try {
            $userModel->save();
            $this->postSync($user, $userModel, $isCreateNew);
            Log::info("Sync user uuid: " . $user->uuid . "  Success");
        } catch (Exception $e) {
            Log::info("Sync Fail : " . $e->getMessage());
        }

        return true;
    }

    public function postSync($userSync, $user, $isCreateNew): void
    {
        $userServiceClass = config('organization.user_service_class');
        if ($userServiceClass) {
            $class = app()->make($userServiceClass);
            $class->postSync($userSync, $user, $isCreateNew);
        }
    }
}
