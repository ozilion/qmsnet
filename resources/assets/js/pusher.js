/**
 * pusher-js
 */

'use strict';

(function () {
  var notificationsWrapper = $('.dropdown-notifications-list');
  var notificationsWrapper1 = $('.dropdown-notifications');
  var notificationsCountElem = notificationsWrapper1.find('span[data-count]');
  var notificationsCount = parseInt(notificationsCountElem.data('count'));
  var notifications = notificationsWrapper.find('ul');

  if (notificationsCount <= 0) {
    notificationsWrapper.hide();
  }

  // Enable pusher logging - don't include this in production
  Pusher.logToConsole = true;

  var pusher = new Pusher('4228803d8065596c6be2', {
    cluster: 'eu'
  });
  // var pusher = new Pusher("{{env('PUSHER_APP_KEY')}}", {
  //   cluster: '{{env("PUSHER_APP_CLUSTER")}}',
  //   encrypted: true
  // });

  var channel = pusher.subscribe('easynet-channel');


  // Bind a function to a Event (the full Laravel class)
  channel.bind('plan-event', function (data) {
    // console.log(data);
    var existingNotifications = notifications.html();
    var newNotificationHtml = `
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                <a href="` + myRoutes(data.asama, data.planno) + `" target="_blank">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        [` + data.dtipi + `]
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h7 class="mb-1 text-truncate">` + data.dentarihi + `</h7>
                      <small class="text-truncate text-body">` + data.firmaadi + `...</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted"></small>
                    </div>
                  </div>
                  </a>
                </li>
        `;
    notifications.html(newNotificationHtml + existingNotifications);

    notificationsCount += 1;
    notificationsCountElem.attr('data-count', notificationsCount);
    notificationsWrapper1.find('.badge').text(notificationsCount);
    notificationsWrapper1.show();
    notificationsWrapper.show();
  });


  channel.bind('my-event', function (data) {
    const toastAnimationExample = document.querySelector('.toast-ex'),
      toastAnimationBody = $('#toast-body');
    let selectedType, selectedAnimation, toastAnimation;

    if (toastAnimation) {
      toastDispose(toastAnimation);
    }
    selectedType = 'text-info';
    selectedAnimation = 'animate__swing';

    toastAnimationBody.html(data.message);
    toastAnimationExample.querySelector('i.mdi').classList.add(selectedType);
    toastAnimationExample.classList.add(selectedAnimation);

    toastAnimation = new bootstrap.Toast(toastAnimationExample);
    toastAnimation.show();

    var existingNotifications = notifications.html();
    var newNotificationHtml = `
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        <i class="mdi mdi-information-variant-circle-outline mdi-48px"></i>
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h6 class="mb-1 text-truncate">Bilgi</h6>
                      <small class="text-truncate text-body">` + data.message + `</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted"></small>
                    </div>
                  </div>
                </li>
        `;
    notifications.html(newNotificationHtml + existingNotifications);

    notificationsCount += 1;
    notificationsCountElem.attr('data-count', notificationsCount);
    notificationsWrapper1.find('.badge').text(notificationsCount);
    notificationsWrapper1.show();
    notificationsWrapper.show();
  });


})();
