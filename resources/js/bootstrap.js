window._ = require('lodash')

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.Popper = require('popper.js').default
window.$ = window.jQuery = require('jquery')

require('bootstrap')
window.ApexCharts = require('apexcharts')
require('web-animations-js')
// window.Vivus = require('vivus');
// window.dragula = require('dragula');
window.Scrollbar = require('smooth-scrollbar/dist/smooth-scrollbar')
require('jquery.appear')
require('datatables')
require('datatables.net-bs4')
require('flatpickr')
require('quill')
require('bootstrap-validator')
window.moment = require('moment')
window.moment.locale('ru')
window.choice = require('choices.js/public/assets/scripts/choices.min.js')
window.axios = require('axios')

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo'

window.Pusher = require('pusher-js')

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,
  forceTLS: true
})

if (window.User && window.User.id) {
  const userId = window.User.id
  window.Echo.private('App.Models.User.' + userId).notification((notification) => {
    getNotificationCounts()
  })
}

const getNotificationCounts = () => {
  var url = '/notification-counts'
  $.ajax({
    type: 'get',
    url: url,
    success: function (res) {
      if (res.counts > 0) {
        $('.notify_count').removeClass('d-none')
        $('.notify_count').addClass('notification_tag').text(res.counts)
        setNotification(res.counts)
        $('.notification_list span.dots').removeClass('d-none')
      } else {
        $('.notify_count').addClass('notification_tag').text(res.unread_total_count)
        setNotification(res.unread_total_count)
        $('.notify_count').removeClass('d-none')
        $('.notification_list span.dots').removeClass('d-none')

        if (res.counts <= 0 && res.unread_total_count > 0) {
          $('.notification_list span.dots').removeClass('d-none')
        } else {
          $('.notify_count').addClass('d-none')
          $('.notification_list span.dots').addClass('d-none')
        }
      }
    }
  })
}

const setNotification = (count) => {
  if (Number(count) >= 100) {
    $('.notify_count').text('99+')
  }
}
