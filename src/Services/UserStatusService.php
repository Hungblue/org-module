<?php

namespace KeyHoang\OrgModule\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use KeyHoang\OrgModule\Models\UserStatus;
use KeyHoang\OrgModule\Models\UserStatusNoSQL;
use YaangVu\LaravelBase\Base\BaseService;

/**
 * @Author      hungnv1
 * @Date        DummyDate
 */
class UserStatusService extends BaseService
{
    private string $mongodb   = 'mongodb';
    private bool   $isMongodb = false;

    public function __construct(private Model $model = new UserStatus(), private readonly ?string $alias = null)
    {
        $this->isMongodb = (config('database.default') == $this->mongodb);

        $this->model = $this->isMongodb ? new UserStatusNoSQL() : new UserStatus();
        parent::__construct($this->model, $this->alias);
    }

    public function sync($userStatus): bool
    {
        try {
            foreach ($userStatus as $value) {
                $userStatusModel = $this->model->query()->where('code', '=', $value->code)->first();
                if (!$userStatusModel) {
                    $userStatusModel = $this->isMongodb ? new UserStatusNoSQL() : new UserStatus();
                }
                $userStatusModel->code = $value->code;
                $userStatusModel->name = $value->name;
                $userStatusModel->save();
                Log::info("Sync user status code: " . $value->code . "  Success");
            }
        } catch (Exception $e) {
            Log::info("Sync Fail : " . $e->getMessage());
        }

        return true;
    }
}
