<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle :title="$t('setting_sidebar.lbl_quick_booking')" icon="fa-solid fa-paper-plane"></CardTitle>
    </div>
    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="quick_booking_method">{{ $t('quick_booking.lbl_quick_booking') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_quick_booking"
            :checked="is_quick_booking == 1 ? true : false" name="is_quick_booking" id="is_quick_booking"
            type="checkbox" v-model="is_quick_booking" />
        </div>
      </div>
    </div>

  <div v-if="is_quick_booking == 1" class="mb-3">
    <pre class="text-dark mb-0"><code>{{ iframeCode }}</code></pre>
        <h6>{{ $t('quick_booking.lbl_shared_link') }}</h6>
     <a :href="url" target="_blank">{{ url }}</a>
  </div>

  <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import { ref, onMounted , computed} from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import SubmitButton from './Forms/SubmitButton.vue'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { createRequest } from '@/helpers/utilities'
import { GET_URL, STORE_URL } from '@/vue/constants/setting'
import { useField, useForm } from 'vee-validate'
import * as yup from 'yup';

const { storeRequest } = useRequest()
const url = ref('') // Set the URL
const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');
const quickBookingUrl = computed(() => `${baseUrl}/quick-booking`)
const iframeCode = computed(() => {
  return `<iframe 
  src="${url.value}" 
  frameborder="0" 
  scrolling="yes" 
  style="display:block; width:100%; height:100vh;"></iframe>`.replace(/\s+/g, ' ').trim()
})


const IS_SUBMITED = ref(false)

const setFormData = (data) => {
  resetForm({
    values: {
      is_quick_booking: data.is_quick_booking || 0,
    }
  })
}

const validationSchema = yup.object({
})

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })

const { value: is_quick_booking } = useField('is_quick_booking')

const data = 'is_quick_booking'

onMounted(() => {
  createRequest(GET_URL(data)).then((response) => {
    setFormData(response)
    url.value = response.quick_booking_url
  })
})

const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  const newValues = {}
  Object.keys(values).forEach((key) => {
    newValues[key] = values[key] || ''
  })
  storeRequest({
    url: STORE_URL,
    body: newValues
  }).then((res) => display_submit_message(res))
})

const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.errors
  }
}


</script>
<style scoped>
.copy-icon {
  cursor: pointer;
  margin-left: 0.5rem;
}

.copy-icon i {
  color: #888;
}
</style>




