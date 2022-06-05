<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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
}
