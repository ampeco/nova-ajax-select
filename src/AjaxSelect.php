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

    public function parent($attribute)
    {
        $this->withMeta(['parent_attribute' => $attribute]);

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
}
