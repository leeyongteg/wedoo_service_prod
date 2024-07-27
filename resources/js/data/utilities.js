import 'sweetalert2/dist/sweetalert2.min.css';
import Swal from 'sweetalert2';

export const confirmSwal = async ({ title }) => {
  return await Swal.fire({
    title: title,
    icon: 'success',
    showCancelButton: true,
    confirmButtonColor: '#F05034',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Confirm',
    iconColor: '#F05034'
  }).then((result) => {
    return result
  })
}

export const confirmcancleSwal = async ({ title, subtitle }) => {
  return await Swal.fire({
    title: title,
    html: subtitle,
    icon: 'success',
    showCancelButton: true,
    confirmButtonColor: '#F05034',
    cancelButtonColor: '#858482',
    confirmButtonText: 'Confirm',
    iconColor: '#F05034'
  }).then((result) => {
    return result
  })
}

export const confirmcancleWallet = async ({ title }) => {
  return await Swal.fire({
    title: title,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#F05034',
    cancelButtonColor: '#858482',
    confirmButtonText: 'Confirm',
    iconColor: '#F05034'
  }).then((result) => {
    return result
  })
}

export const successOperation = async ({ message }) => {
  const capitalizedMessage = message.charAt(0).toUpperCase() + message.slice(1)
  Swal.fire({
    title: 'Good job!',
    text: capitalizedMessage,
    icon: 'success'
  })
}

export const formatCurrencyVue = (value) => {
  if (window.currencyFormat !== undefined) {
    return window.currencyFormat(value)
  }
  return value
}

