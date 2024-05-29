<template>
    <DefaultField :field="field" :errors="errors" v-show="!isFieldHidden" :show-help-text="showHelpText" >
        <template #field>
            <div class="flex relative w-full">
                <select v-model="value" class="w-full block form-control form-control-bordered form-input" :disabled="disabled" :dusk="field.attribute">
                    <option :value="null">Choose an option</option>
                    <option
                        :key="option.value"
                        :value="option.value"
                        v-for="option in options">
                        {{ option.display }}
                    </option>
                </select>
                <svg class="flex-shrink-0 pointer-events-none form-select-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6"><path class="fill-current" d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path></svg>
            </div>
        </template>
    </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data() {
        return {
            options: [],
            loaded: false,
            parentValue: null,
            operator: null,
        }
    },

    mounted() {
        this.operator = this.field.operator;
        this.parentValue = this.field.parent_value;

        if (this.field.options) {
          this.options = this.field.options;
        } else {
            this.updateOptions();
        }

        Nova.$on(this.field.parent_attribute+'-change', (value) => {
            this.parentValue = value
            this.updateOptions()
        });

        Nova.$on('operator-change', (value) => {
            this.operator = value
            this.updateOptions()
        });
    },

    computed: {
        endpoint() {
            const result = this.field.endpoint
                .replace('{resource-name}', this.resourceName)
                .replace('{resource-id}', this.resourceId ? this.resourceId : '')
                .replace('{operator}', this.operator ? this.operator : '')
                .replace('{'+ this.field.parent_attribute +'}', this.parentValue ? this.parentValue : '')

            return result;
        },
        empty() {
            return this.loaded && this.options.length == 0;
        },

        disabled() {
            if(this.field.alwaysShow === true) {
                return false;
            }
            return this.loaded == false && (this.field.parent_attribute != undefined && this.parentValue == null) || this.options.length == 0;
        },

      isFieldHidden(){
        if(this.field.alwaysShow === true) {
            return false;
        }

        if(this.disabled){
          return true;
        }
        if(this.field.hideIfSingleResultOrParentNotSelected != undefined && this.field.hideIfSingleResultOrParentNotSelected == true) {
          return this.options.length <= 1;
        }

        return false;
      },
    },

    methods: {
        setInitialValue() {
            this.value = this.field.value || ''
        },

        fill(formData) {
            formData.append(this.field.attribute, this.getFieldvalue() || '')
        },

        updateOptions() {
            this.options = [];
            this.loaded = false;

            if(this.notWatching() || (this.parentValue != null && this.parentValue != '')) {
                Nova.request().get(this.endpoint)
                    .then(response => {
                        this.loaded = true;
                        this.options = response.data;
                        let optionValueExists = false;
                        this.options.forEach(option => {
                            if(option.value == this.value) {
                                optionValueExists = true;
                            }
                        })

                        if(optionValueExists == false) {
                            this.value = null;
                        }
                    })
            }
        },

        notWatching() {
            return this.field.parent_attribute == undefined;
        },

        getFieldvalue() {
          if(this.field.hideIfSingleResultOrParentNotSelected && this.options.length == 1){
            return this.options[0].value;
          }

          return this.value;
        }
    },
}
</script>
