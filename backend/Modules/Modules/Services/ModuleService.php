<?php

namespace Modules\Modules\Services;

use Modules\Modules\Models\Module;

class ModuleService
{
    /*
    |--------------------------------------------------------------------------
    | Find Module
    |--------------------------------------------------------------------------
    */

    public function find(string $id): Module
    {
        return Module::findOrFail($id);
    }

    public function getAll()
    {
        return Module::latest()->paginate();
    }

    public function create(array $data)
    {
        return Module::create($data);
    }

    public function update(Module $module, array $data)
    {
        $module->update($data);
        return $module;
    }

    public function delete(Module $module)
    {
        $module->delete();
    }
}
