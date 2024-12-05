<?php

namespace KeyHoang\OrgModule\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use KeyHoang\OrgModule\Models\Department;
use KeyHoang\OrgModule\Models\DepartmentNoSQL;
use KeyHoang\OrgModule\Models\User;
use KeyHoang\OrgModule\Models\UserNoSQL;
use YaangVu\LaravelBase\Base\BaseService;

/**
 * @Author      hungnv1
 * @Date        DummyDate
 */
class DepartmentService extends BaseService
{
    protected Model $userModel;
    private string  $mongodb   = 'mongodb';
    private bool    $isMongodb = false;

    public function __construct(private Model $model = new Department(), private readonly ?string $alias = null)
    {
        $this->isMongodb = (config('database.default') == $this->mongodb);

        $this->model     = $this->isMongodb ? new DepartmentNoSQL() : new Department();
        $this->userModel = $this->isMongodb ? new UserNoSQL() : new User();
        parent::__construct($this->model, $this->alias);
    }

    public function sync($department): bool
    {
        if ($department?->deleted_at ?? false) {
            $this->model->query()->where('uuid', '=', $department->uuid)->delete();
            Log::info("Delete department uuid: " . $department->uuid . "  Success");

            return true;
        }

        $departmentModel = $this->model->query()
                                       ->where('uuid', '=', $department->uuid)
                                       ->first();
        $isCreateNew     = false;
        if (!$departmentModel) {
            $departmentModel = $this->isMongodb ? new DepartmentNoSQL() : new Department();
            $isCreateNew     = true;
        }

        $departmentModel->uuid             = $department->uuid;
        $departmentModel->code             = $department->code;
        $departmentModel->abbreviated_name = $department->abbreviated_name;
        $departmentModel->name             = $department->name;
        $departmentModel->level            = $department->level;
        $departmentModel->is_unit          = $department->is_unit;
        $departmentModel->is_department    = $department->is_department;

        $departmentParent = is_array($department->parent) ? (object)$department->parent : $department->parent;
        if ($departmentParent) {
            $parentModel = $this->model->query()
                                       ->where('uuid', '=', $departmentParent->uuid)
                                       ->first();

            $departmentModel->parent_id = $this->isMongodb ? $parentModel?->_id : $parentModel?->id;
        }

        $departmentHead = is_array($department->department_head) ? (object)$department->department_head
            : $department->department_head;
        if ($departmentHead) {
            $departmentHeadModel                 = $this->userModel->query()
                                                                   ->where('sso_id', '=',
                                                                           $departmentHead->sso_id)
                                                                   ->first();
            $departmentModel->department_head_id = $this->isMongodb ? $departmentHeadModel?->_id
                : $departmentHeadModel?->id;
        }

        $departmentUnit = is_array($department->unit) ? (object)$department->unit : $department->unit;
        if ($departmentUnit) {
            $unitModel                = $this->model->query()
                                                    ->where('uuid', '=', $departmentUnit?->uuid)
                                                    ->first();
            $departmentModel->unit_id = $this->isMongodb ? $unitModel?->_id : $unitModel?->id;
        }

        $departmentLv1 = is_array($department->department) ? (object)$department->department : $department->department;
        if ($departmentLv1) {
            $departmentLv1Model             = $this->model->query()
                                                          ->where('uuid', '=', $departmentLv1?->uuid)
                                                          ->first();
            $departmentModel->department_id = $this->isMongodb ? $departmentLv1Model?->_id : $departmentLv1Model?->id;
        }

        try {
            $departmentModel->save();
            $this->postSync($departmentModel, $isCreateNew);
            Log::info("Sync department uuid: " . $department->uuid . "  Success");
        } catch (Exception $e) {
            Log::info("Sync Fail : " . $e->getMessage());
        }

        return true;
    }

    public function postSync($department, $isCreateNew): void
    {
        $departmentServiceClass = config('organization.department_service_class');
        if ($departmentServiceClass) {
            $class = app()->make($departmentServiceClass);
            $class->postSync($department, $isCreateNew);
        }
    }
}
