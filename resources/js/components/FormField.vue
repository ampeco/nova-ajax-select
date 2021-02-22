<template>
    <default-field :field="field" :errors="errors" v-show="!isFieldHidden">
        <template slot="field">
             <select v-model="value" class="w-full form-control form-select" :disabled="disabled">
                <option :value="null">Choose an option</option>
                <option
                    :key="option.value"
                    :value="option.value"
                    v-for="option in options">
                    {{ option.display }}
                </option>
            </select>
        </template>
    </default-field>
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
            parentValue: null
        }
    },

    mounted() {
        this.watchedComponents.forEach(component => {
            let attribute = this.field.selectedAttribute !== undefined ? this.field.selectedAttribute : 'value'

            component.$watch(attribute, (value) => {

                this.parentValue = (value && attribute !== 'value') ? value.value : value;

                this.updateOptions();
            }, { immediate: true });
        });
    },

    computed: {
        watchedComponents() {
            return this.$parent.$children.filter(component => {
                return this.isWatchingComponent(component);
            })
        },
        endpoint() {
            return this.field.endpoint
                .replace('{resource-name}', this.resourceName)
                .replace('{resource-id}', this.resourceId ? this.resourceId : '')
                .replace('{'+ this.field.parent_attribute +'}', this.parentValue ? this.parentValue : '')
        },
        empty() {
            return this.loaded && this.options.length == 0;
        },

        disabled() {
            return this.loaded == false && (this.field.parent_attribute != undefined && this.parentValue == null) || this.options.length == 0;
        },

      isFieldHidden(){
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

        isWatchingComponent(component) {
            return component.field !== undefined
                && component.field.attribute == this.field.parent_attribute;
        },

        getFieldvalue()
        {
          if(this.field.hideIfSingleResultOrParentNotSelected && this.options.length == 1){
            return this.options[0].value;
          }

          return this.value;
        }
    },
}
</script>
