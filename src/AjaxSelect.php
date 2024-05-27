<?php

namespace NovaAjaxSelect;

use Laravel\Nova\Fields\Field;

class AjaxSelect extends Field
{
    public $showOnIndex = false;

    public $showOnDetail = false;

    public $component = 'nova-ajax-select';

    public function get($endpoint)
    {
        $this->withMeta(['endpoint' => $endpoint]);

        return $this;
    }

    public function parent($attribute, $parentValue = null)
    {
        $this->withMeta([
            'parent_attribute' => $attribute,
            'parent_value' => $parentValue,
        ]);

        return $this;
    }

    public function selectedAttribute($attribute)
    {
        $this->withMeta(['selectedAttribute' => $attribute]);

        return $this;
    }

    public function hideIfSingleResultOrParentNotSelected()
    {
        $this->withMeta(['hideIfSingleResultOrParentNotSelected' => true]);

        return $this;
    }

    public function alwaysShow()
    {
        $this->withMeta(['alwaysShow' => true]);

        return $this;
    }

    public function options(array $options)
    {
        $this->withMeta(['options' => $options]);

        return $this;
    }

    public function operator(int $operator) {
        $this->withMeta(['operator' => $operator]);
        return $this;
    }
}
