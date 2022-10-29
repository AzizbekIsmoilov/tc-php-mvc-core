<?php

namespace app\core\form;

use app\core\Model;

abstract class BaseField
{

    public Model $model;
    public string  $attribute;

    /**
     * @param Model $model
     * @param $attribute
     */
    public function __construct(Model $model, $attribute)
    {
        $this->attribute = $attribute;
        $this->model = $model;
    }
    abstract public function renderInput():string;
    public function __toString()
    {
        return sprintf(' <div class="form-group">
                    <label class="form-label">%s</label>
                    %s
                    <div class="invalid-feedback">
                    %s
                    </div>
                </div>',
            $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute));
    }

}