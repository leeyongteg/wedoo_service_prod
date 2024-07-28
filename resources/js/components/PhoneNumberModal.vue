<template>
  <div>
    <div ref="modal" id="staticBackdrop" class="modal fade">
      <Loader v-if="IsLoading" />
      <div class="modal-dialog">
        <div class="modal-header">
          <div class="circle">
            <span v-if="!error">
              <img
                v-if="operator === 'Orange'"
                src="https://download.logo.wine/logo/Orange_S.A./Orange_S.A.-Logo.wine.png"
                alt="Logo Orange"
              />
              <img
                v-else-if="operator === 'Mtn'"
                src="https://download.logo.wine/logo/MTN_Group/MTN_Group-Logo.wine.png"
                alt="Logo MTN"
              />
            </span>
          </div>
        </div>
        <div class="modal-content">
          <form @submit.prevent="formSubmit">
            <input type="hidden" name="_token" :value="csrfToken" />

            <div class="modal-body">
              <div class="text-left text-danger close" @click="closeModal">
                <i class="fa fa-times" aria-hidden="true" data-dismiss="modal"></i>
              </div>

              <div class="px-4 py-5">
                <!-- <h5 class="text-uppercase">Jonathan Adler</h5> -->

                <h4 class="mt-5 theme-color capitalize mb-5">
                  {{ props.thanks_you_for_your_order }}
                </h4>

                <span class="theme-color"> {{ $t('messages.phone_information') }}</span>
                <div class="mb-2">
                  <hr class="new1" />
                </div>

                <div class="d-flex justify-content-center mb-5">
                  <input
                    maxlength="9"
                    id="phone_number"
                    v-model="phone_number"
                    name="phone_number"
                    type="text"
                    value="6"
                    placeholder="695921917"
                    @input="validatePhoneNumber()"
                  />
                </div>
                <p v-if="error" class="text-danger text-center">{{ error }}</p>

                <span class="theme-color mt-2">{{ $t('messages.booking_detail') }}</span>
                <div class="mb-3">
                  <hr class="new1" />
                </div>

                <!-- <div class="d-flex justify-content-between">
                  <span class="font-weight-bold">Ether Chair(Qty:1)</span>
                  <span class="text-muted">55555</span>
                </div> -->

                <div class="d-flex justify-content-between">
                  <small>{{ $t('messages.discount') }}</small>
                  <small>{{ formatCurrencyVue(props.discount) }}</small>
                </div>

                <div class="d-flex justify-content-between mt-3">
                  <span class="font-weight-bold">Total</span>
                  <span class="font-weight-bold theme-color">
                    {{ formatCurrencyVue(props.total_amount) }}</span
                  >
                </div>

                <div class="text-center mt-5">
                  <button v-if="!error" class="btn btn-primary">
                    {{ $t('messages.pay_now') }}
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, onMounted } from 'vue'
import PhoneNumberModal from '../components/PhoneNumberModal.vue'
import Swal from 'sweetalert2'
import { useField, useForm } from 'vee-validate'
import * as yup from 'yup'
import { GET_FREEMOPAY_PAYMENT_URL, GET_PAYMENT_METHOD } from '../data/api'
import Loader from '../components/Loader.vue'

const props = defineProps([
  'booking_id',
  'customer_id',
  'discount',
  'service',
  'total_amount',
  'advance_payment_amount',
  'wallet_amount'
])

const validationSchema = yup.object({})

const { handleSubmit } = useForm({
  validationSchema,
  phone_number
})

const IsLoading = ref(false)
const payment_method = 'freemopay'
const amount = props.total_amount
const { value: phone_number } = useField('phone_number')

const error = ref('Veiller renseigner un numero')
let operator = ref('')

const isChildComponentVisible = ref(false)
const currentComponent = ref(null)

onMounted(() => {
  myModal = new bootstrap.Modal(modal.value, {
    backdrop: 'static',
    keyboard: false
  })
  isChildComponentVisible.value = true
  currentComponent.value = PhoneNumberModal
  myModal.show()
})




const validatePhoneNumber = () => {
  if (!phone_number.value) {
    error.value = 'Le numéro de téléphone est requis.'
  } else if (!isValidPhoneNumber(phone_number.value)) {
    error.value = "Le numéro de téléphone n'est pas prit en compte."
  } else if (determineOperator(phone_number.value) == 'Inconnu') {
    error.value = 'Operateur non prit en charge.'
  } else {
    error.value = ''
    determineOperator(phone_number.value)
  }
}

const isValidPhoneNumber = (number) => {
  const regex = /^6[5789]\d{7}$/ // Vérifie si le numéro commence par 6 et suivi de 5,7,8 ou 9 a 9 chiffres au total
  return regex.test(number)
}

const determineOperator = (phone_number) => {
  const prefix = phone_number.substring(0, 3)
  if ((prefix >= 655 && prefix <= 659) || (prefix >= 690 && prefix <= 699)) {
    operator.value = 'Orange'
  } else if ((prefix >= 670 && prefix <= 683) || (prefix >= 650 && prefix <= 654)) {
    operator.value = 'Mtn'
  } else {
    operator.value = 'Inconnu'
  }
  return operator
}


const formSubmit = handleSubmit(async (values) => {
    IsLoading.value = true;
  values.booking_id = props.booking_id
  values.customer_id = props.customer_id
  values.discount = props.discount
  values.payment_type = payment_method
  values.amount = amount
  values.phone_number = values.phone_number

  if (props.advance_payment_amount != null) {
    values.total_amount = props.advance_payment_amount
    values.type = 'advance_payment'
  } else {
    values.total_amount = props.total_amount
    values.type = 'full_payment'
  }

//   IsLoading.value = 1
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content

  const response = await fetch(GET_PAYMENT_METHOD, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify(values)
  })
  if (response.ok) {
    // IsLoading.value = 0

    const responseData = await response.json()
    console.log(responseData)
    if (responseData.payment_geteway_data != null && responseData.payment_type == 'freemopay') {
      createFreemoPay(responseData)
    } else {
    //   IsLoading.value = 0

      Swal.fire({
        title: 'Error',
        text: 'check Your FreeMoPAY key Integration !',
        icon: 'error',
        iconColor: '#F05034'
      }).then((result) => {})
    }
  } else {
    // IsLoading.value = 0

    Swal.fire({
      title: 'Error',
      text: 'Something Went Wrong!',
      icon: 'error',
      iconColor: '#F05034'
    }).then((result) => {})
  }
  IsLoading.value = false;
  console.log(7777777)
})

const createFreemoPay = async (data) => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content

  const res = await fetch(GET_FREEMOPAY_PAYMENT_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify(data)
  })

  if (res.ok) {
    const responseData = await res.json()

    const message = responseData.message
    const capitalizedMessage = message.charAt(0).toUpperCase() + message.slice(1)
    if (responseData.reference) {
      closeModal()
      Swal.fire({
        title: 'Good job! ' + capitalizedMessage,
        icon: 'success',
        html: `
            Veillez composer le  <b>#150*50#</b>,
            pour confirmer votre paiement.
        `
      })
    } else {
      Swal.fire({
        title: 'Error',
        text: capitalizedMessage,
        icon: 'error',
        iconColor: '#F05034'
      }).then((result) => {})
    }
  } else {
    Swal.fire({
      title: 'Error',
      text: 'Something Went Wrong For Mobile Pay !',
      icon: 'error',
      iconColor: '#F05034'
    }).then((result) => {})
  }
}

const emits = defineEmits('modalClosed')

const modal = ref(null)
let myModal = '@l#'

const closeModal = () => {
  if (myModal !== '@l#' && isChildComponentVisible.value == true) {
    isChildComponentVisible.value = false
    currentComponent.value = null
    myModal.hide()
    emits('modalClosed')
  }
}
const formatCurrencyVue = (value) => {
  if (window.currencyFormat !== undefined) {
    return window.currencyFormat(value)
  }
  return value
}
</script>

<style scoped>
.modal.show {
  display: block;
}
body {
  background-color: #5165ff;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-body {
  background-color: #fff;
  border-color: #fff;
}

.close {
  position: absolute;
  top: 12px;
  right: 12px;
  cursor: pointer;
}

.theme-color {
  color: #004cb9;
}
hr.new1 {
  border-top: 2px dashed #fff;
  margin: 0.4rem 0;
}

.btn-primary {
  color: #fff;
  background-color: #004cb9;
  border-color: #004cb9;
  padding: 12px;
  padding-right: 30px;
  padding-left: 30px;
  border-radius: 1px;
  font-size: 17px;
}

.btn-primary:hover {
  color: #fff;
  background-color: #004cb9;
  border-color: #004cb9;
  padding: 12px;
  padding-right: 30px;
  padding-left: 30px;
  border-radius: 1px;
  font-size: 17px;
}

input {
  display: block;
  /* margin: 2em auto; */
  border: none;
  padding: 0;
  width: 13.5ch;
  background: repeating-linear-gradient(
      90deg,
      dimgrey 0,
      dimgrey 1ch,
      transparent 0,
      transparent 1.5ch
    )
    0 100%/ 13ch 2px no-repeat;
  font:
    5ch droid sans mono,
    consolas,
    monospace;
  letter-spacing: 0.5ch;
}

input:focus {
  outline: none;
  color: rgb(4, 3, 4);
}

/** Circle */
.modal-header {
  justify-content: center;
  border: none;
  padding: 0;
}

.circle {
  display: flex;
  justify-content: center;
  align-items: center;
  background: #000;
  height: 150px;
  width: 150px;
  border-radius: 50%;
  border: 5px solid #fff;
  margin-bottom: -15px;
  z-index: 2;
}

.circle img {
  max-width: 100%;
  max-height: 100%;
  border-radius: 50%;
}
</style>
