<template>
  <form @submit="formSubmit">
    <div class="d-flex justify-content-between align-items-center">
      <CardTitle :title="$t('messages.change_password')" icon="fa-solid fa-key"></CardTitle>
    </div>

      <InputField type="password" class="col-md-12" :is-required="true" :label="$t('users.lbl_old_password')" :value="old_password"
       v-model="old_password" :placeholder="$t('users.old_password')" :error-message="errors.old_password"></InputField>

      <InputField type="password" class="col-md-12" :is-required="true" :label="$t('users.lbl_new_password')" :value="new_password"
      v-model="new_password" :placeholder="$t('users.new_password')" :error-message="errors.new_password"></InputField>

      <InputField type="password" class="col-md-12" :is-required="true" :label="$t('users.lbl_confirm_password')" :value="confirm_password"
      v-model="confirm_password" :placeholder="$t('users.confirm_password')" :error-message="errors.confirm_password"></InputField>

    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>

  </form>
</template>

<script setup>
import { ref } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import { useField, useForm } from 'vee-validate'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { PROFILE_CHANGE_PASSWORD_URL } from '@/vue/constants/users'
import * as yup from 'yup'
import InputField from '@/vue/components/form-elements/InputField.vue'
import SubmitButton from './Forms/SubmitButton.vue'
import { useI18n } from 'vue-i18n'
// props
defineProps({
  createTitle: { type: String, default: '' },

})
const IS_SUBMITED = ref(false)
const {storeRequest} = useRequest()

const { t } = useI18n();
// Validations
const validationSchema = yup.object({
  old_password: yup.string()
    .required(t('messages.old_password_required'))
    .min(8, t('messages.password_min'))
    .max(12, t('messages.password_max')),
  new_password: yup.string()
    .required(t('messages.new_password_required'))
    .min(8, t('messages.password_min'))
    .max(12, t('messages.password_max')),
  confirm_password: yup.string()
    .oneOf([yup.ref('new_password'), null], t('messages.password_must_match'))
    .required(t('messages.confirm_password_required'))
    .min(8, t('messages.password_min'))
    .max(12, t('messages.password_max')),
})

const defaultData = () => {
  errorMessages.value = {}
  return {
    password: '',
    new_password:'',
    confirm_password: '',
  }
}

const setFormData = (data) => {
  resetForm({
    values: {
      old_password:'' ,
      confirm_password:'',
      new_password:''
    }
  })
}

const { handleSubmit, errors,resetForm } = useForm({
  validationSchema
})

const { value: old_password } = useField('old_password')
const { value: confirm_password } = useField('confirm_password')
const { value: new_password } = useField('new_password')
const errorMessages = ref({})

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    errors.value=res.errors
    window.errorSnackbar(res.message)
  }
}

// Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  storeRequest({ url: PROFILE_CHANGE_PASSWORD_URL, body: values}).then((res) => {
    display_submit_message(res)
    if (res.status) {
        setFormData()
    }
  })
})


</script>

