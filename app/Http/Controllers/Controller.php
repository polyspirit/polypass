<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Gate;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __get($name)
    {
        if ($name === 'modelClassName') {
            return $this->setModelClassNameByControllerName();
        } else if ($name === 'entityName') {
            return $this->setEntityNameByModelClassName();
        }
    }

    /**
     * Set and return model class name based by controller class name
     */
    private function setModelClassNameByControllerName(): string
    {
        if (!isset($this->modelClassName)) {
            $controllerClassName = get_class();
            $this->modelClassName = str_replace('App\Http\Controllers', '', $controllerClassName);
            $this->modelClassName = str_replace('Controller', '', $this->modelClassName);
            $this->modelClassName = 'App\Models' . $this->modelClassName;
        }

        return $this->modelClassName;
    }

    private function setEntityNameByModelClassName(): string
    {
        if (!isset($this->entityName)) {
            $this->entityName = getUnderscoreClassName($this->modelClassName);
        }

        return $this->entityName;
    }

    protected function checkAuthorization(): void
    {
        $this->authorizeResource($this->modelClassName, $this->entityName);
    }

    /**
     * Check view policy for each in items collection.
     */
    protected function checkItemsPolicy(\Illuminate\Database\Eloquent\Collection &$items): void
    {
        foreach ($items as $key => $item) {
           $this->checkItemPolicy($item);
           if (is_null($item)) {
                $items->forget($key);
           }
        }
    }

    /**
     * Check item policy by view method and set to null if not allowed.
     */
    protected function checkItemPolicy(\Illuminate\Database\Eloquent\Model &$item): void
    {
        $response = Gate::inspect('view', $item);

        if (!$response->allowed()) {
            $item = null;
        }
    }
}
