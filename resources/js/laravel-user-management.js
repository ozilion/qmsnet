/**
 * Page User List
 */

'use strict';

// Datatable (jquery)
$(function () {
  // Variable declaration for table
  var dt_user_table = $('.datatables-users'),
    dt_user_audit_log_table = $('.user-audit-log'),
    select2 = $('.select2'),
    statusObj = {
      1: { active: 2, title: 'Pending', class: 'bg-label-warning' },
      2: { active: 1, title: 'Active', class: 'bg-label-success' },
      3: { active: 0, title: 'Inactive', class: 'bg-label-secondary' }
    },
    offCanvasForm = $('#offcanvasAddUser');

  if (select2.length) {
    var $this = select2;
    select2Focus($this);
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select Country',
      dropdownParent: $this.parent()
    });
  }

  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Users datatable
  if (dt_user_table.length) {
    console.log(baseUrl + 'user-list');
    var dt_user = dt_user_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: baseUrl + 'user-list'
      },
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'id' },
        { data: 'name' },
        { data: 'email' },
        { data: 'role' },
        { data: 'status' },
        { data: 'action' }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          searchable: false,
          orderable: true,
          targets: 1,
          render: function (data, type, full, meta) {
            return `<span>${full.fake_id}</span>`;
          }
        },
        {
          // User full name
          targets: 2,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            var $name = full['name'];
            var $id = full['id'];
            var userView = getUserViewUrl($id);

            // For Avatar badge
            var stateNum = Math.floor(Math.random() * 6);
            var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
            var $state = states[stateNum],
              $name = full['name'],
              $initials = $name.match(/\b\w/g) || [],
              $output;
            $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
            $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';

            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center user-name">' +
              '<div class="avatar-wrapper">' +
              '<div class="avatar avatar-sm me-3">' +
              $output +
              '</div>' +
              '</div>' +
              '<div class="d-flex flex-column">' +
              '<a href="' +
              userView +
              '" class="text-body text-truncate" target="_blank"><span class="fw-medium">' +
              $name +
              '</span></a>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // User email
          targets: 3,
          searchable: true,
          render: function (data, type, full, meta) {
            var $email = full['email'];

            return '<span class="user-email">' + $email + '</span>';
          }
        },
        {
          // User role
          targets: 4,
          searchable: true,
          render: function (data, type, full, meta) {
            var $role = full['role'];

            return '<span class="user-role">' + $role + '</span>';
          }
        },
        {
          // User Status
          targets: 5,
          searchable: true,
          render: function (data, type, full, meta) {
            var statusVal = full['status'];
            var found = null;

            // statusObj üzerinde döngü ile arama yapıyoruz
            for (var key in statusObj) {
              if (statusObj.hasOwnProperty(key) && statusObj[key].active == statusVal) {
                found = statusObj[key];
                break;
              }
            }

            if (found) {
              return '<span class="badge rounded-pill ' + found.class + '" text-capitalized>' + found.title + '</span>';
            } else {
              return '';
            }
          }

        },
        {
          // Actions
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
            var $id = full['id'];
            var userView = getUserViewUrl($id);

            return (
              '<div class="d-inline-block text-nowrap">' +
              `<button class="btn btn-sm btn-icon edit-record" data-id="${full['id']}" data-name="${full['name']}" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="mdi mdi-pencil-outline mdi-20px"></i></button>` +
              `<button class="btn btn-sm btn-icon delete-record" data-id="${full['id']}"><i class="mdi mdi-delete-outline mdi-20px"></i></button>` +
              '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical mdi-20px"></i></button>' +
              '<div class="dropdown-menu dropdown-menu-end m-0">' +
              '<a href="' +
              userView +
              '" class="dropdown-item" target="_blank">View</a>' +
              '<a href="javascript:;" class="dropdown-item">Suspend</a>' +
              '</div>' +
              '</div>'
            );
          }
        }
      ],
      order: [[2, 'asc']],
      dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      language: {
        "info": "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor",
        "infoEmpty": "Kayıt yok",
        "infoFiltered": "(_MAX_ kayıt içerisinden bulunan)",
        "infoThousands": ".",
        "lengthMenu": "Sayfada _MENU_ kayıt göster",
        "loadingRecords": "Yükleniyor...",
        "processing": "İşleniyor...",
        "search": "",
        "zeroRecords": "Eşleşen kayıt bulunamadı",
        "paginate": {
          "first": "İlk",
          "last": "Son",
          "next": "Sonraki",
          "previous": "Önceki"
        },
        "aria": {
          "sortAscending": ": artan sütun sıralamasını aktifleştir",
          "sortDescending": ": azalan sütun sıralamasını aktifleştir"
        },
        "select": {
          "rows": {
            "_": "%d kayıt seçildi",
            "1": "1 kayıt seçildi"
          },
          "cells": {
            "1": "1 hücre seçildi",
            "_": "%d hücre seçildi"
          },
          "columns": {
            "1": "1 sütun seçildi",
            "_": "%d sütun seçildi"
          }
        },
        "autoFill": {
          "cancel": "İptal",
          "fillHorizontal": "Hücreleri yatay olarak doldur",
          "fillVertical": "Hücreleri dikey olarak doldur",
          "fill": "Bütün hücreleri <i>%d<\/i> ile doldur",
          "info": "Detayı"
        },
        "buttons": {
          "collection": "Koleksiyon <span class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"><\/span>",
          "colvis": "Sütun görünürlüğü",
          "colvisRestore": "Görünürlüğü eski haline getir",
          "copySuccess": {
            "1": "1 satır panoya kopyalandı",
            "_": "%ds satır panoya kopyalandı"
          },
          "copyTitle": "Panoya kopyala",
          "csv": "CSV",
          "excel": "Excel",
          "Export": "Dışa Aktar",
          "pageLength": {
            "-1": "Bütün satırları göster",
            "_": "%d satır göster",
            "1": "1 Satır Göster"
          },
          "pdf": "PDF",
          "print": "Yazdır",
          "copy": "Kopyala",
          "copyKeys": "Tablodaki veriyi kopyalamak için CTRL veya u2318 + C tuşlarına basınız. İptal etmek için bu mesaja tıklayın veya escape tuşuna basın.",
          "createState": "Şuanki Görünümü Kaydet",
          "removeAllStates": "Tüm Görünümleri Sil",
          "removeState": "Aktif Görünümü Sil",
          "renameState": "Aktif Görünümün Adını Değiştir",
          "savedStates": "Kaydedilmiş Görünümler",
          "stateRestore": "Görünüm -&gt; %d",
          "updateState": "Aktif Görünümün Güncelle"
        },
        "searchBuilder": {
          "add": "Koşul Ekle",
          "button": {
            "0": "Arama Oluşturucu",
            "_": "Arama Oluşturucu (%d)"
          },
          "condition": "Koşul",
          "conditions": {
            "date": {
              "after": "Sonra",
              "before": "Önce",
              "between": "Arasında",
              "empty": "Boş",
              "equals": "Eşittir",
              "not": "Değildir",
              "notBetween": "Dışında",
              "notEmpty": "Dolu"
            },
            "number": {
              "between": "Arasında",
              "empty": "Boş",
              "equals": "Eşittir",
              "gt": "Büyüktür",
              "gte": "Büyük eşittir",
              "lt": "Küçüktür",
              "lte": "Küçük eşittir",
              "not": "Değildir",
              "notBetween": "Dışında",
              "notEmpty": "Dolu"
            },
            "string": {
              "contains": "İçerir",
              "empty": "Boş",
              "endsWith": "İle biter",
              "equals": "Eşittir",
              "not": "Değildir",
              "notEmpty": "Dolu",
              "startsWith": "İle başlar",
              "notContains": "İçermeyen",
              "notStartsWith": "Başlamayan",
              "notEndsWith": "Bitmeyen"
            },
            "array": {
              "contains": "İçerir",
              "empty": "Boş",
              "equals": "Eşittir",
              "not": "Değildir",
              "notEmpty": "Dolu",
              "without": "Hariç"
            }
          },
          "data": "Veri",
          "deleteTitle": "Filtreleme kuralını silin",
          "leftTitle": "Kriteri dışarı çıkart",
          "logicAnd": "ve",
          "logicOr": "veya",
          "rightTitle": "Kriteri içeri al",
          "title": {
            "0": "Arama Oluşturucu",
            "_": "Arama Oluşturucu (%d)"
          },
          "value": "Değer",
          "clearAll": "Filtreleri Temizle"
        },
        "searchPanes": {
          "clearMessage": "Hepsini Temizle",
          "collapse": {
            "0": "Arama Bölmesi",
            "_": "Arama Bölmesi (%d)"
          },
          "count": "{total}",
          "countFiltered": "{shown}\/{total}",
          "emptyPanes": "Arama Bölmesi yok",
          "loadMessage": "Arama Bölmeleri yükleniyor ...",
          "title": "Etkin filtreler - %d",
          "showMessage": "Tümünü Göster",
          "collapseMessage": "Tümünü Gizle"
        },
        "thousands": ".",
        "datetime": {
          "amPm": [
            "öö",
            "ös"
          ],
          "hours": "Saat",
          "minutes": "Dakika",
          "next": "Sonraki",
          "previous": "Önceki",
          "seconds": "Saniye",
          "unknown": "Bilinmeyen",
          "weekdays": {
            "6": "Paz",
            "5": "Cmt",
            "4": "Cum",
            "3": "Per",
            "2": "Çar",
            "1": "Sal",
            "0": "Pzt"
          },
          "months": {
            "9": "Ekim",
            "8": "Eylül",
            "7": "Ağustos",
            "6": "Temmuz",
            "5": "Haziran",
            "4": "Mayıs",
            "3": "Nisan",
            "2": "Mart",
            "11": "Aralık",
            "10": "Kasım",
            "1": "Şubat",
            "0": "Ocak"
          }
        },
        "decimal": ",",
        "editor": {
          "close": "Kapat",
          "create": {
            "button": "Yeni",
            "submit": "Kaydet",
            "title": "Yeni kayıt oluştur"
          },
          "edit": {
            "button": "Düzenle",
            "submit": "Güncelle",
            "title": "Kaydı düzenle"
          },
          "error": {
            "system": "Bir sistem hatası oluştu (Ayrıntılı bilgi)"
          },
          "multi": {
            "info": "Seçili kayıtlar bu alanda farklı değerler içeriyor. Seçili kayıtların hepsinde bu alana aynı değeri atamak için buraya tıklayın; aksi halde her kayıt bu alanda kendi değerini koruyacak.",
            "noMulti": "Bu alan bir grup olarak değil ancak tekil olarak düzenlenebilir.",
            "restore": "Değişiklikleri geri al",
            "title": "Çoklu değer"
          },
          "remove": {
            "button": "Sil",
            "confirm": {
              "_": "%d adet kaydı silmek istediğinize emin misiniz?",
              "1": "Bu kaydı silmek istediğinizden emin misiniz?"
            },
            "submit": "Sil",
            "title": "Kayıtları sil"
          }
        },
        "stateRestore": {
          "creationModal": {
            "button": "Kaydet",
            "columns": {
              "search": "Kolon Araması",
              "visible": "Kolon Görünümü"
            },
            "name": "Görünüm İsmi",
            "order": "Sıralama",
            "paging": "Sayfalama",
            "scroller": "Kaydırma (Scrool)",
            "search": "",
            "searchBuilder": "Arama Oluşturucu",
            "select": "Seçimler",
            "title": "Yeni Görünüm Oluştur",
            "toggleLabel": "Kaydedilecek Olanlar"
          },
          "duplicateError": "Bu Görünüm Daha Önce Tanımlanmış",
          "emptyError": "Görünüm Boş Olamaz",
          "emptyStates": "Herhangi Bir Görünüm Yok",
          "removeJoiner": "ve",
          "removeSubmit": "Sil",
          "removeTitle": "Görünüm Sil",
          "renameButton": "Değiştir",
          "renameLabel": "Görünüme Yeni İsim Ver -&gt; %s:",
          "renameTitle": "Görünüm İsmini Değiştir",
          "removeConfirm": "Görünümü silmek istediğinize emin misiniz?",
          "removeError": "Görünüm silinemedi"
        },
        "emptyTable": "Tabloda veri bulunmuyor",
        "searchPlaceholder": "Arayın...",
        "infoPostFix": " "
      },
      // Buttons with Dropdown
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-primary dropdown-toggle mx-3',
          text: '<i class="mdi mdi-export-variant me-sm-1"></i>Export',
          buttons: [
            {
              extend: 'excelHtml5',
              autoFilter: true,
              text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7],
                // prevent avatar to be display
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'pdf',
              text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7],
                // prevent avatar to be display
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            }
          ]
        },
        {
          text: '<i class="mdi mdi-plus me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Add New User</span>',
          className: 'add-new btn btn-primary',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasAddUser'
          }
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      }
    });
  }

  // Delete Record
  $(document).on('click', '.delete-record', function () {
    var user_id = $(this).data('id'),
      user_name = $(this).data('name'), // Kullanıcının adını alıyoruz
      dtrModal = $('.dtr-bs-modal.show');

    // Küçük ekranlarda açık modal varsa kapatıyoruz
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // Silme işlemi için SweetAlert onay penceresi
    Swal.fire({
      title: 'Emin misiniz, ' + user_name + '?',
      text: "Bu işlemi geri alamayacaksınız!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Evet, sil!',
      cancelButtonText: 'İptal',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        // Veriyi silme işlemi
        $.ajax({
          type: 'DELETE',
          url: `${baseUrl}user-list/${user_id}`,
          success: function () {
            // Silme başarılı ise, datatable (veya form) güncellensin
            dt_user.draw();
            // Başarı mesajı
            Swal.fire({
              icon: 'success',
              title: 'Silindi!',
              text: 'Kullanıcı silindi.',
              customClass: {
                confirmButton: 'btn btn-success'
              }
            });
          },
          error: function (error) {
            console.log(error);
            Swal.fire({
              icon: 'error',
              title: 'Hata!',
              text: 'Silme işlemi gerçekleştirilemedi.',
              customClass: {
                confirmButton: 'btn btn-danger'
              }
            });
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire({
          title: 'İptal edildi',
          text: 'Kullanıcı silinmedi!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });

  // edit record
  $(document).on('click', '.edit-record', function () {
    var user_id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    // hide responsive modal in small screen
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // changing the title of offcanvas
    $('#offcanvasAddUserLabel').html('Edit User');

    // get data
    $.get(`${baseUrl}user-list\/${user_id}\/edit`, function (data) {
      // console.log(data);
      $('#user_id').val(data.id);
      $('#add-user-fullname').val(data.name);
      $('#add-user-email').val(data.email);
      $('#add-user-company').val(data.kurulus);
      $('#user-role').html(data.role);
      $('#user-permissions').html(data.permissions);
    });
  });

  // changing the title
  $('.add-new').on('click', function () {
    $('#user_id').val(''); //reseting input field
    $('#offcanvasAddUserLabel').html('Add User');
  });

  // validating form and updating user's data
  const addNewUserForm = document.getElementById('addNewUserForm');

  // user form validation
  const fv = FormValidation.formValidation(addNewUserForm, {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: 'Please enter fullname'
          }
        }
      },
      email: {
        validators: {
          notEmpty: {
            message: 'Please enter your email.'
          },
          emailAddress: {
            message: 'The value is not a valid email address'
          }
        }
      },
      company: {
        validators: {
          notEmpty: {
            message: 'Please enter your company'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: '',
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          return '.mb-4';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {
    // adding or updating user when form successfully validate
    $.ajax({
      data: $('#addNewUserForm').serialize(),
      url: `${baseUrl}user-list`,
      type: 'POST',
      success: function (status) {
        dt_user.draw();
        offCanvasForm.offcanvas('hide');

        // sweetalert
        Swal.fire({
          icon: 'success',
          title: `Successfully ${status}!`,
          text: `User ${status} Successfully.`,
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      },
      error: function (err, textStatus, errorThrown) {
        offCanvasForm.offcanvas('hide');
        Swal.fire({
          title: 'Duplicate Entry!',
          text: 'Your email should be unique.',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });

  // clearing form data when offcanvas hidden
  offCanvasForm.on('hidden.bs.offcanvas', function () {
    fv.resetForm(true);
  });

  const phoneMaskList = document.querySelectorAll('.phone-mask');

  // Phone Number
  if (phoneMaskList) {
    phoneMaskList.forEach(function (phoneMask) {
      new Cleave(phoneMask, {
        phone: true,
        phoneRegionCode: 'US'
      });
    });
  }
});
