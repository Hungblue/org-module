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
            $this->model->query()->where('organization_id', '=', $department->id)->delete();
            Log::info("Delete department organization_id: " . $department->id . "  Success");

            return true;
        }

        $departmentModel = $this->model->query()
                                       ->where('organization_id', '=', $department->id)
                                       ->first();
        $isCreateNew     = false;
        if (!$departmentModel) {
            $departmentModel = $this->isMongodb ? new DepartmentNoSQL() : new Department();
            $isCreateNew     = true;
        }

        $departmentModel->organization_id = $department->id;
        $departmentModel->code            = $department->code;
        $departmentModel->name            = $department->name;
        $departmentModel->level           = $department->level;
        $departmentModel->is_unit         = $department->is_unit;
        $departmentModel->is_department   = $department->is_department;

        if ($department->parent) {
            $parent = $this->model->query()
                                  ->where('organization_id', '=', $department->parent->id)
                                  ->first();

            $departmentModel->parent_id = $this->isMongodb ? $parent?->_id : $parent?->id;
        }

        if ($department->department_head) {
            $departmentHead                      = $this->userModel->query()
                                                                   ->where('sso_id', '=',
                                                                           $department->department_head->sso_id)
                                                                   ->first();
            $departmentModel->department_head_id = $this->isMongodb ? $departmentHead?->_id : $departmentHead?->id;
        }

        try {
            $departmentModel->save();
            $this->postSync($departmentModel, $isCreateNew);
            Log::info("Sync department organization_id: " . $department->id . "  Success");
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
