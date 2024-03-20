<?php

namespace KeyHoang\OrgModule\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use KeyHoang\OrgModule\Models\User;
use KeyHoang\OrgModule\Models\UserNoSQL;
use KeyHoang\OrgModule\Traits\RabbitMQProducer;
use PhpAmqpLib\Exchange\AMQPExchangeType;
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
            $this->model->query()->where('sso_id', '=', $user->sso_id)->delete();
            Log::info("Delete user sso_id: " . $user->sso_id . "  Success");

            return true;
        }

        $userModel = $this->model->query()->where('sso_id', '=', $user->sso_id)->first();
        if (!$userModel) {
            $userModel = $this->isMongodb ? new UserNoSQL() : new User();
        }

        $userModel->sso_id       = $user->sso_id;
        $userModel->username     = $user->username;
        $userModel->full_name    = $user->full_name;
        $userModel->email        = $user->email;
        $userModel->phone_number = $user->phone_number;
        $userModel->staff_code   = $user->staff_code;
        $userModel->position     = $user->position;
        $userModel->avatar       = $user->avatar;
        if ($user->user_status) {
            $userModel->status = $user->user_status->name;
        }
        if ($user->user_department) {
            $userDepartment = is_array($user->user_department) ? (object)$user->user_department
                : $user->user_department;
            $department     = is_array($userDepartment->department) ? (object)$userDepartment->department
                : $userDepartment->department;
            #Set khoi/ban/phong
            if ($department->department) {
                $userModel->department      = $department->department->name ?? null;
                $userModel->department_code = $department->department->code ?? null;
            }
            #Set unit
            $userModel->unit = null;
            if ($department->unit) {
                $unit                 = is_array($department->unit) ? (object)$department->unit : $department->unit;
                $userModel->unit      = $unit->name;
                $userModel->unit_code = $unit->code;
            }
        }

        try {
            $userModel->save();
            if (config('organization.set_role')) {
                $this->setDefaultRole($userModel);
            }
            Log::info("Sync user sso_id: " . $user->sso_id . "  Success");
        } catch (Exception $e) {
            Log::info("Sync Fail : " . $e->getMessage());
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function setDefaultRole($user): void
    {
        $this->setVhost(config('rabbitmq.vhost'))
             ->pushToExchange($user, 'SET-ROLE', AMQPExchangeType::DIRECT, 'set-role');
    }
}
