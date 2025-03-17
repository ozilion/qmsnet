/**
 * DataTables Extensions (jquery)
 */

'use strict';

var densisyet = [];
var duzey = parseFloat(0);
var duzeys = parseFloat(0);
var d = new Date();
var ay = d.getMonth() + 1;
var yil = d.getFullYear();
let dt_filter_en, dt_filter22, dt_smiic, dt_27001, dt_50001;

const verticaldivScrollbar = document.getElementById('vertical-div-scrollbar');

var asama = $('#asama').val(),
  iso900115varyok = parseInt($('#iso900115varyok').val()),
  iso1400115varyok = parseInt($('#iso1400115varyok').val()),
  iso2200018varyok = parseInt($('#iso2200018varyok').val()),
  iso4500118varyok = parseInt($('#iso4500118varyok').val()),
  iso5000118varyok = parseInt($('#iso5000118varyok').val()),
  iso27001varyok = parseInt($('#iso27001varyok').val()),
  helalvaryok = parseInt($('#helalvaryok').val()),
  oicsmiik6varyok = parseInt($('#oicsmiik6varyok').val()),
  oicsmiik9varyok = parseInt($('#oicsmiik9varyok').val()),
  oicsmiik171varyok = parseInt($('#oicsmiik171varyok').val()),
  oicsmiik23varyok = parseInt($('#oicsmiik23varyok').val()),
  oicsmiik24varyok = parseInt($('#oicsmiik24varyok').val());

$(function () {

  var dt_crm_planlar_table = $('.dt-dashboard-crm-planlar'),
    dt_ea_nace_table = $('.dt-ea-nace-kodlari'),
    dt_22000_table = $('.dt-22000-kategori'),
    dt_smiic_table = $('.dt-smiic-kategori'),
    dt_27001_table = $('.dt-27001-kategori'),
    dt_50001_table = $('.dt-50001-kategori'),
    dt_asama1_basdenetci_table = $('.dt-asama1-basdenetciler'),
    dt_asama1_denetci_table = $('.dt-asama1-denetciler'),
    dt_asama1_teknik_uzman_table = $('.dt-asama1-teknik-uzman'),
    dt_asama1_gozlemci_table = $('.dt-asama1-gozlemci'),
    dt_asama1_iku_table = $('.dt-asama1-iku'),
    dt_asama1_aday_denetci_table = $('.dt-asama1-aday-denetci'),
    dt_asama1_degerlendirici_table = $('.dt-asama1-degerlendirici'),
    dt_asama2_basdenetci_table = $('.dt-asama2-basdenetciler'),
    dt_asama2_denetci_table = $('.dt-asama2-denetciler'),
    dt_asama2_teknik_uzman_table = $('.dt-asama2-teknik-uzman'),
    dt_asama2_gozlemci_table = $('.dt-asama2-gozlemci'),
    dt_asama2_iku_table = $('.dt-asama2-iku'),
    dt_asama2_aday_denetci_table = $('.dt-asama2-aday-denetci'),
    dt_asama2_degerlendirici_table = $('.dt-asama2-degerlendirici'),
    dt_gozetim1_basdenetci_table = $('.dt-gozetim1-basdenetciler'),
    dt_gozetim1_denetci_table = $('.dt-gozetim1-denetciler'),
    dt_gozetim1_teknik_uzman_table = $('.dt-gozetim1-teknik-uzman'),
    dt_gozetim1_gozlemci_table = $('.dt-gozetim1-gozlemci'),
    dt_gozetim1_iku_table = $('.dt-gozetim1-iku'),
    dt_gozetim1_aday_denetci_table = $('.dt-gozetim1-aday-denetci'),
    dt_gozetim1_degerlendirici_table = $('.dt-gozetim1-degerlendirici'),
    dt_gozetim2_basdenetci_table = $('.dt-gozetim2-basdenetciler'),
    dt_gozetim2_denetci_table = $('.dt-gozetim2-denetciler'),
    dt_gozetim2_teknik_uzman_table = $('.dt-gozetim2-teknik-uzman'),
    dt_gozetim2_gozlemci_table = $('.dt-gozetim2-gozlemci'),
    dt_gozetim2_iku_table = $('.dt-gozetim2-iku'),
    dt_gozetim2_aday_denetci_table = $('.dt-gozetim2-aday-denetci'),
    dt_gozetim2_degerlendirici_table = $('.dt-gozetim2-degerlendirici'),
    dt_yb_basdenetci_table = $('.dt-yb-basdenetciler'),
    dt_yb_denetci_table = $('.dt-yb-denetciler'),
    dt_yb_teknik_uzman_table = $('.dt-yb-teknik-uzman'),
    dt_yb_gozlemci_table = $('.dt-yb-gozlemci'),
    dt_yb_iku_table = $('.dt-yb-iku'),
    dt_yb_aday_denetci_table = $('.dt-yb-aday-denetci'),
    dt_yb_degerlendirici_table = $('.dt-yb-degerlendirici'),
    dt_ot_basdenetci_table = $('.dt-ot-basdenetciler'),
    dt_ot_denetci_table = $('.dt-ot-denetciler'),
    dt_ot_teknik_uzman_table = $('.dt-ot-teknik-uzman'),
    dt_ot_gozlemci_table = $('.dt-ot-gozlemci'),
    dt_ot_iku_table = $('.dt-ot-iku'),
    dt_ot_aday_denetci_table = $('.dt-ot-aday-denetci'),
    dt_ot_degerlendirici_table = $('.dt-ot-degerlendirici'),
    tblbelgelifirmalarals05 = $('#tblbelgelifirmalarals05');

  if (verticaldivScrollbar) {
    new PerfectScrollbar(verticaldivScrollbar, {
      wheelPropagation: false
    });
  }

  $.extend($.fn.dataTable.ext.type.order, {
    "turkish-pre": function (d) {
      return d.replace(/ö/g, 'oe')
        .replace(/Ö/g, 'Oe')
        .replace(/ç/g, 'c')
        .replace(/Ç/g, 'C')
        .replace(/ş/g, 's')
        .replace(/Ş/g, 'S')
        .replace(/ğ/g, 'g')
        .replace(/Ğ/g, 'G')
        .replace(/ü/g, 'ue')
        .replace(/Ü/g, 'Ue')
        .replace(/ı/g, 'i')
        .replace(/İ/g, 'I');
    },
    "turkish-asc": function (a, b) {
      return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "turkish-desc": function (a, b) {
      return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
  });

  $.fn.dataTable.ext.search.push(
    function (settings, data, dataIndex) {
      var normalizedSearch = function (str) {
        return str.replace(/ö/g, 'oe')
          .replace(/Ö/g, 'Oe')
          .replace(/ç/g, 'c')
          .replace(/Ç/g, 'C')
          .replace(/ş/g, 's')
          .replace(/Ş/g, 'S')
          .replace(/ğ/g, 'g')
          .replace(/Ğ/g, 'G')
          .replace(/ü/g, 'ue')
          .replace(/Ü/g, 'Ue')
          .replace(/ı/g, 'i')
          .replace(/İ/g, 'I');
      };

      var searchTerm = normalizedSearch(settings.oPreviousSearch.sSearch);
      for (var i = 0; i < data.length; i++) {
        if (normalizedSearch(data[i]).includes(searchTerm)) {
          return true;
        }
      }
      return false;
    }
  );

  // FixedHeader
  // --------------------------------------------------------------------
  // dt_crm_planlar_table.DataTable.datetime('D.M.YYYY');
  if (dt_crm_planlar_table.length) {
    // Setup - add a text input to each footer cell
    $('.dt-dashboard-crm-planlar thead tr').clone(true).appendTo('.dt-dashboard-crm-planlar thead');
    $('.dt-dashboard-crm-planlar thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_crm_planlar.column(i).search() !== this.value) {
          dt_crm_planlar.column(i).search(this.value).draw();
        }
      });
    });

    var dt_crm_planlar = dt_crm_planlar_table.DataTable({
      ajax: planlarRoutePath,
      columns: [
        {data: ''},
        {data: 'planno'},
        {data: 'firmaadi'},
        {data: 'belgelendirileceksistemler'},
        {data: 'dentarihi'},
        {data: 'bitistarihi'},
        {data: 'danisman'},
        {data: 'belgedurum'}
      ],
      columnDefs: [
        {
          // Actions
          width: '4%',
          targets: 0,
          title: '#',
          orderable: false,
          searchable: false,
          render: function (data, type, full, meta) {
            return (
              '<div class="d-inline-block">' +
              '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>' +
              '<div class="dropdown-menu dropdown-menu-end m-0">' +
              '<a href="' + myRoutes('basvuru', full['planno']) + '" class="dropdown-item" target="_blank">Başvuru bilgileri</a>' +
              '<a href="' + myRoutes('ilkplan', full['planno']) + '" class="dropdown-item" target="_blank">İlk Planlama</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a href="' + auditPlanRoutes('asama1', full['planno']) + '" class="dropdown-item" target="_blank">Denetim Planı A1</a>' +
              '<a href="' + auditPlanRoutes('asama2', full['planno']) + '" class="dropdown-item" target="_blank">Denetim Planı A2</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a href="' + myRoutes('ilkkarar', full['planno']) + '" class="dropdown-item" target="_blank">İlk Karar</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a href="' + myRoutes('g1', full['planno']) + '" class="dropdown-item" target="_blank">1. Gözetim</a>' +
              '<a href="' + myRoutes('g1karar', full['planno']) + '" class="dropdown-item" target="_blank">G1 Karar</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a href="' + myRoutes('g2', full['planno']) + '" class="dropdown-item" target="_blank">2. Gözetim</a>' +
              '<a href="' + myRoutes('g2karar', full['planno']) + '" class="dropdown-item" target="_blank">G2 Karar</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a href="' + myRoutes('yb', full['planno']) + '" class="dropdown-item" target="_blank">Yb</a>' +
              '<a href="' + myRoutes('ybkarar', full['planno']) + '" class="dropdown-item" target="_blank">Yb Karar</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a href="' + myRoutes('ozel', full['planno']) + '" class="dropdown-item" target="_blank">Özel</a>' +
              '<a href="' + myRoutes('ozelkarar', full['planno']) + '" class="dropdown-item" target="_blank">Özel Karar</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a href="' + myRoutes('sertifika', full['planno']) + '" class="dropdown-item text-success delete-record" target="_blank">Sertifika</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a href="javascript:;" class="dropdown-item text-danger delete-record">Plan Sil</a>' +
              '</div>' +
              '</div>'
            );
          }
        },
        {
          // müşteri no
          width: '6%',
          targets: 1,
          orderable: true,
          visible: true,
          render: function (data, type, full, meta) {
            return (
              '<button type="button" class="btn btn-outline-primary planno-btn" data-planno="' + full['planno'] + '">' +
              full['planno'].toString().padStart(4, '0') +
              ' </button>'
            );
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '40%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $planno = full['planno'],
              $name = full['firmaadi'],
              $addrr = full['firmaadresi'],
              $kapsam = full['belgelendirmekapsami'],
              $iso9 = parseInt(full['iso900115varyok']),
              $iso14 = parseInt(full['iso1400115varyok']),
              $iso45 = parseInt(full['iso4500118varyok']),
              $iso27 = parseInt(full['iso27001varyok']),
              $iso51 = parseInt(full['iso5000118varyok']),
              $iso22 = parseInt(full['iso2200018varyok']),
              $smiic = parseInt(full['helalvaryok']),
              $smiic6 = parseInt(full['oicsmiik6varyok']),
              $smiic9 = parseInt(full['oicsmiik9varyok']),
              $smiic171 = parseInt(full['oicsmiik171varyok']),
              $smiic24 = parseInt(full['oicsmiik24varyok']),
              $yayintarihi = full['ilkyayintarihi'] ?? "-",
              $dtipi = full['dtipi'];
            var $kodlar = full['eanacekat'];
            // $kodlar = ($iso9 === 1 || $iso14 === 1 || $iso45 === 1) ? full['eakodu'] + '|' + full['nacekodu'] : '';
            // $kodlar += ($iso22 === 1) ? '@' + full['kategori22'] : '';
            // $kodlar += ($smiic === 1 || $smiic6 === 1 || $smiic9 === 1 || $smiic171 === 1 || $smiic24 === 1) ? 'ß' + full['kategorioic'] : '';
            // $kodlar += ($iso51 === 1) ? 'Æ' + full['teknikalanenys'] : '';
            // $kodlar += ($iso27 === 1) ? '€' + full['kategoribgys'] : '';
            // console.log($kodlar);
            // For Avatar badge
            var stateNum = Math.floor(Math.random() * 6);
            var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
            var $state = states[stateNum];
            // var $initials = $name.match(/\b\w/g) || [];
            // $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
            var $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $dtipi + '</span>';
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="avatar-wrapper">' +
              '<div class="avatar me-2">' +
              $output +
              '</div>' +
              '</div>' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap"><a href="' + myRoutes(full['asama'], $planno) + '" target="_blank">' +
              $name +
              '</a></span>' +
              '<small class="emp_post text-truncate text-wrap">' +
              $addrr +
              '</small>' +
              '<small class="emp_post text-truncate text-wrap text-danger">' +
              $kapsam +
              '</small>' +
              '<small class="emp_post text-truncate text-wrap text-success">' +
              $kodlar +
              '</small>' +
              '<small class="emp_post text-truncate text-wrap text-success">Belge yayın tarihi: ' +
              $yayintarihi +
              '</small>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // standartlar
          width: '20%',
          targets: 3
        },
        {
          // denetim tarihi
          width: '8%',
          targets: 4
        },
        {
          // belge geçerlilik tarihi
          width: '8%',
          targets: 5,
          render: dt_crm_planlar_table.DataTable.render.date()
        },
        {
          // kimden
          width: '6%',
          targets: 6
        },
        {
          // durum
          width: '10%',
          targets: 7,
          render: function (data, type, full, meta) {
            var $status_name = full['belgedurum'];
            // console.log(full['planno'] + " => " + $status_name);
            var $status_number = ($status_name === 'devam') ? 1 : ($status_name === 'aski') ? 2 : ($status_name === 'iptal') ? 3 : 4;
            var $status = {
              1: {title: 'Devam', class: 'bg-label-success'},
              2: {title: 'Askı', class: ' bg-label-warning'},
              3: {title: 'İptal', class: ' bg-label-danger'},
              4: {title: '  -  ', class: ' bg-label-primary'}
            };
            if (typeof $status[$status_number] === 'undefined') {
              return data;
            }
            return (
              '<span class="badge rounded-pill ' +
              $status[$status_number].class +
              '">' +
              $status[$status_number].title +
              '</span>'
            );
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'desc']],
      dom:
        '<"row mx-2"' +
        '<"col-md-2"<"me-3"l>>' +
        '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0 gap-3"fB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
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
          className: 'btn btn-label-secondary dropdown-toggle me-3 waves-effect waves-light',
          text: '<i class="mdi mdi-export-variant me-1"></i> <span class="d-none d-sm-inline-block">Dışarı Aktar</span>',
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
        }
      ],
      displayLength: 10,
      lengthMenu: [7, 10, 25, 50, 75, 100],
      scrollX: true,
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['firmaadi'];
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
                '<td class="emp_post text-truncate text-wrap">' +
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

    // Butonlara event listener ekleme
    $('.dt-dashboard-crm-planlar').on('click', '.planno-btn', function() {
      var planno = $(this).data('planno');
      $('#modalContent').text('Plan No: ' + planno);
      $('#backDropModal').modal('show');
    });

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      var navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_crm_planlar).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_crm_planlar);
    }

  }

  if (dt_ea_nace_table.length) {
    // Setup - add a text input to each footer cell
    $('.dt-ea-nace-kodlari thead tr').clone(true).appendTo('.dt-ea-nace-kodlari thead');
    $('.dt-ea-nace-kodlari thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_filter_en.column(i).search() !== this.value) {
          dt_filter_en.column(i).search(this.value).draw();
        }
      });
    });

    dt_filter_en = dt_ea_nace_table.DataTable({
      ajax: eanaceRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'kk9'},
        {data: 'kk14'},
        {data: 'kk45'},
        {data: 'ea'},
        {data: 'nace'},
        {data: 'aciklama'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
            var chkvalue = full['id'] + '|' + full['kk9'] + '|' + full['kk14'] + '|' + full['kk45'] + '|' + full['ea'] + '|' + full['nace'];
            return (full['kk9'] !== '') ? '<input type="checkbox" id="dt_ea_nace_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setEaNaceKategori(\'' + chkvalue + '\')" class="dt-checkboxes form-check-input">' : '';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          targets: 1,
          searchable: false,
          orderable: true,
          width: '10px'
        },
        {
          targets: 2,
          searchable: false,
          orderable: false,
          width: '10px'
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_filter_en.columns.adjust().draw();
  }

  if (dt_22000_table.length) {
    // Setup - add a text input to each footer cell
    $('.dt-22000-kategori thead tr').clone(true).appendTo('.dt-22000-kategori thead');
    $('.dt-22000-kategori thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_filter22.column(i).search() !== this.value) {
          dt_filter22.column(i).search(this.value).draw();
        }
      });
    });

    dt_filter22 = dt_22000_table.DataTable({
      ajax: cat22RoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'kategori'},
        {data: 'baslik'},
        {data: 'aciklama'},
        {data: 'bb'},
        {data: 'cc'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['id'] + '|' + full['kategori'] + '|' + full['bb'] + '|' + full['cc'];
            return '<input type="checkbox" id="dt_22000_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setEaNaceKategori(\'' + chkvalue + '\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          targets: 5,
          visible: true
        },
        {
          targets: 6,
          visible: true
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_filter22.columns.adjust().draw();
  }

  if (dt_smiic_table.length) {
    // Setup - add a text input to each footer cell
    $('.dt-smiic-kategori thead tr').clone(true).appendTo('.dt-smiic-kategori thead');
    $('.dt-smiic-kategori thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_smiic.column(i).search() !== this.value) {
          dt_smiic.column(i).search(this.value).draw();
        }
      });
    });

    dt_smiic = dt_smiic_table.DataTable({
      ajax: catSmiicRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'kategori'},
        {data: 'baslik'},
        {data: 'aciklama'},
        {data: 'ornekler'},
        {data: 'bb'},
        {data: 'cc'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['id'] + '|' + full['kategori'] + '|' + full['bb'] + '|' + full['cc'];
            return '<input type="checkbox" id="dt_smiic_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setEaNaceKategori(\'' + chkvalue + '\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          targets: 6,
          visible: true
        },
        {
          targets: 7,
          visible: true
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_smiic.columns.adjust().draw();
  }

  if (dt_27001_table.length) {
    // Setup - add a text input to each footer cell
    $('.dt-27001-kategori thead tr').clone(true).appendTo('.dt-27001-kategori thead');
    $('.dt-27001-kategori thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_27001.column(i).search() !== this.value) {
          dt_27001.column(i).search(this.value).draw();
        }
      });
    });

    dt_27001 = dt_27001_table.DataTable({
      ajax: cat27001RoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'sektorgrubu'},
        {data: 'sektor'},
        {data: 'teknikalan'},
        {data: 'teknikalankodu'},
        {data: 'teknikalangrubu'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['id'] + '|' + full['teknikalankodu'] + '|' + full['teknikalangrubu'];
            return '<input type="checkbox" id="dt_27001_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setEaNaceKategori(\'' + chkvalue + '\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          targets: 6,
          visible: true
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_27001.columns.adjust().draw();
  }

  if (dt_50001_table.length) {
    // Setup - add a text input to each footer cell
    $('.dt-50001-kategori thead tr').clone(true).appendTo('.dt-50001-kategori thead');
    $('.dt-50001-kategori thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_50001.column(i).search() !== this.value) {
          dt_50001.column(i).search(this.value).draw();
        }
      });
    });

    dt_50001 = dt_50001_table.DataTable({
      ajax: cat50001RoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'teknikalan'},
        {data: 'aciklama'},
        {data: 'teknikalangrubu'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['id'] + '|' + full['teknikalan'] + '|' + full['teknikalangrubu'];
            return '<input type="checkbox" id="dt_50001_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setEaNaceKategori(\'' + chkvalue + '\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_50001.columns.adjust().draw();
  }

  if (dt_asama1_basdenetci_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama1-basdenetciler thead tr').clone(true).appendTo('.dt-asama1-basdenetciler thead');
    $('.dt-asama1-basdenetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama1_basdenetci.column(i).search() !== this.value) {
          dt_asama1_basdenetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama1_basdenetci = dt_asama1_basdenetci_table.DataTable({
      ajax: dtBasdenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci', type: 'turkish'},
        {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="radio" id="dt_asama1_basdenetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setBasdenetci(\'bd1\', \'' + chkvalue + '\', \'asama1tar\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="radio" class="form-check-input">'
          }
        },
        {
          targets: 1,
          visible: false,
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[2, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'single'
      }
    });
    dt_asama1_basdenetci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_asama1_basdenetci).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_asama1_basdenetci);
    }
  }

  if (dt_asama1_denetci_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama1-denetciler thead tr').clone(true).appendTo('.dt-asama1-denetciler thead');
    $('.dt-asama1-denetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama1_denetci.column(i).search() !== this.value) {
          dt_asama1_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama1_denetci = dt_asama1_denetci_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama1_denetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'d1\', \'asama1-denetciler\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama1_denetci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_asama1_denetci).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_asama1_denetci);
    }
  }

  if (dt_asama1_teknik_uzman_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama1-teknik-uzman thead tr').clone(true).appendTo('.dt-asama1-teknik-uzman thead');
    $('.dt-asama1-teknik-uzman thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama1_teknik_uzman.column(i).search() !== this.value) {
          dt_asama1_teknik_uzman.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama1_teknik_uzman = dt_asama1_teknik_uzman_table.DataTable({
      ajax: dtTeknikUzmanRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama1_teknik_uzman' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'tu1\', \'asama1-teknik-uzman\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama1_teknik_uzman.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama1_teknik_uzman).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama1_teknik_uzman);
    // }
  }

  if (dt_asama1_gozlemci_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama1-gozlemci thead tr').clone(true).appendTo('.dt-asama1-gozlemci thead');
    $('.dt-asama1-gozlemci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama1_gozlemci.column(i).search() !== this.value) {
          dt_asama1_gozlemci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama1_gozlemci = dt_asama1_gozlemci_table.DataTable({
      ajax: dtGozlemciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama1_gozlemci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'g1\', \'asama1-gozlemci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama1_gozlemci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_asama1_teknik_uzman).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_asama1_teknik_uzman);
    }
  }

  if (dt_asama1_iku_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama1-iku thead tr').clone(true).appendTo('.dt-asama1-iku thead');
    $('.dt-asama1-iku thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama1_iku.column(i).search() !== this.value) {
          dt_asama1_iku.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama1_iku = dt_asama1_iku_table.DataTable({
      ajax: dtIkuRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama1_iku' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'iku1\', \'asama1-iku\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama1_iku.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama1_iku).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama1_iku);
    // }
  }

  if (dt_asama1_aday_denetci_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama1-aday-denetci thead tr').clone(true).appendTo('.dt-asama1-aday-denetci thead');
    $('.dt-asama1-aday-denetci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama1_aday_denetci.column(i).search() !== this.value) {
          dt_asama1_aday_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama1_aday_denetci = dt_asama1_aday_denetci_table.DataTable({
      ajax: dtAdayDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama1_aday_denetci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'ad1\', \'asama1-aday-denetci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama1_aday_denetci.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama1_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama1_aday_denetci);
    // }
  }

  if (dt_asama1_degerlendirici_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama1-degerlendirici thead tr').clone(true).appendTo('.dt-asama1-degerlendirici thead');
    $('.dt-asama1-degerlendirici thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama1_degerlendirici.column(i).search() !== this.value) {
          dt_asama1_degerlendirici.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama1_degerlendirici = dt_asama1_degerlendirici_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama1_degerlendirici' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'sid1\', \'asama1-degerlendirici\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama1_degerlendirici.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama1_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama1_aday_denetci);
    // }
  }

  if (dt_asama2_basdenetci_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama2-basdenetciler thead tr').clone(true).appendTo('.dt-asama2-basdenetciler thead');
    $('.dt-asama2-basdenetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama2_basdenetci.column(i).search() !== this.value) {
          dt_asama2_basdenetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama2_basdenetci = dt_asama2_basdenetci_table.DataTable({
      ajax: dtBasdenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="radio" id="dt_asama2_basdenetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setBasdenetci(\'bd2\', \'' + chkvalue + '\', \'asama2tar\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="radio" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'single'
      }
    });
    dt_asama2_basdenetci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_asama2_basdenetci).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_asama2_basdenetci);
    }
  }

  if (dt_asama2_denetci_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama2-denetciler thead tr').clone(true).appendTo('.dt-asama2-denetciler thead');
    $('.dt-asama2-denetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama2_denetci.column(i).search() !== this.value) {
          dt_asama2_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama2_denetci = dt_asama2_denetci_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama2_denetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'d2\', \'asama2-denetciler\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama2_denetci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_asama2_denetci).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_asama2_denetci);
    }
  }

  if (dt_asama2_teknik_uzman_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama2-teknik-uzman thead tr').clone(true).appendTo('.dt-asama2-teknik-uzman thead');
    $('.dt-asama2-teknik-uzman thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama2_teknik_uzman.column(i).search() !== this.value) {
          dt_asama2_teknik_uzman.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama2_teknik_uzman = dt_asama2_teknik_uzman_table.DataTable({
      ajax: dtTeknikUzmanRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama2_teknik_uzman' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'tu2\', \'asama2-teknik-uzman\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama2_teknik_uzman.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama2_teknik_uzman).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama2_teknik_uzman);
    // }
  }

  if (dt_asama2_gozlemci_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama2-gozlemci thead tr').clone(true).appendTo('.dt-asama2-gozlemci thead');
    $('.dt-asama2-gozlemci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama2_gozlemci.column(i).search() !== this.value) {
          dt_asama2_gozlemci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama2_gozlemci = dt_asama2_gozlemci_table.DataTable({
      ajax: dtGozlemciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama2_gozlemci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'g2\', \'asama2-gozlemci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama2_gozlemci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_asama2_teknik_uzman).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_asama2_teknik_uzman);
    }
  }

  if (dt_asama2_iku_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama2-iku thead tr').clone(true).appendTo('.dt-asama2-iku thead');
    $('.dt-asama2-iku thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama2_iku.column(i).search() !== this.value) {
          dt_asama2_iku.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama2_iku = dt_asama2_iku_table.DataTable({
      ajax: dtIkuRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama2_iku' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'iku2\', \'asama2-iku\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama2_iku.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama2_iku).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama2_iku);
    // }
  }

  if (dt_asama2_aday_denetci_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama2-aday-denetci thead tr').clone(true).appendTo('.dt-asama2-aday-denetci thead');
    $('.dt-asama2-aday-denetci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama2_aday_denetci.column(i).search() !== this.value) {
          dt_asama2_aday_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama2_aday_denetci = dt_asama2_aday_denetci_table.DataTable({
      ajax: dtAdayDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama2_aday_denetci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'ad2\', \'asama2-aday-denetci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama2_aday_denetci.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci);
    // }
  }

  if (dt_asama2_degerlendirici_table.length && asama === 'ilkplan') {
    // Setup - add a text input to each footer cell
    $('.dt-asama2-degerlendirici thead tr').clone(true).appendTo('.dt-asama2-degerlendirici thead');
    $('.dt-asama2-degerlendirici thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_asama2_degerlendirici.column(i).search() !== this.value) {
          dt_asama2_degerlendirici.column(i).search(this.value).draw();
        }
      });
    });

    var dt_asama2_degerlendirici = dt_asama2_degerlendirici_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_asama2_degerlendirici' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'sid2\', \'asama2-degerlendirici\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_asama2_degerlendirici.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci);
    // }
  }

  if (dt_gozetim1_basdenetci_table.length && asama === 'g1') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim1-basdenetciler thead tr').clone(true).appendTo('.dt-gozetim1-basdenetciler thead');
    $('.dt-gozetim1-basdenetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim1_basdenetci.column(i).search() !== this.value) {
          dt_gozetim1_basdenetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim1_basdenetci = dt_gozetim1_basdenetci_table.DataTable({
      ajax: dtBasdenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="radio" id="dt_gozetim1_basdenetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setBasdenetci(\'gbd1\', \'' + chkvalue + '\', \'gozetim1tar\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="radio" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'single'
      }
    });
    dt_gozetim1_basdenetci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_gozetim1_basdenetci).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_gozetim1_basdenetci);
    }
  }

  if (dt_gozetim1_denetci_table.length && asama === 'g1') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim1-denetciler thead tr').clone(true).appendTo('.dt-gozetim1-denetciler thead');
    $('.dt-gozetim1-denetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim1_denetci.column(i).search() !== this.value) {
          dt_gozetim1_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim1_denetci = dt_gozetim1_denetci_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim1_denetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'gd1\', \'gozetim1-denetciler\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim1_denetci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_gozetim1_denetci).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_gozetim1_denetci);
    }
  }

  if (dt_gozetim1_teknik_uzman_table.length && asama === 'g1') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim1-teknik-uzman thead tr').clone(true).appendTo('.dt-gozetim1-teknik-uzman thead');
    $('.dt-gozetim1-teknik-uzman thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim1_teknik_uzman.column(i).search() !== this.value) {
          dt_gozetim1_teknik_uzman.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim1_teknik_uzman = dt_gozetim1_teknik_uzman_table.DataTable({
      ajax: dtTeknikUzmanRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim1_teknik_uzman' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'gtu1\', \'gozetim1-teknik-uzman\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim1_teknik_uzman.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_gozetim1_teknik_uzman).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_gozetim1_teknik_uzman);
    // }
  }

  if (dt_gozetim1_gozlemci_table.length && asama === 'g1') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim1-gozlemci thead tr').clone(true).appendTo('.dt-gozetim1-gozlemci thead');
    $('.dt-gozetim1-gozlemci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim1_gozlemci.column(i).search() !== this.value) {
          dt_gozetim1_gozlemci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim1_gozlemci = dt_gozetim1_gozlemci_table.DataTable({
      ajax: dtGozlemciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim1_gozlemci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'gg1\', \'gozetim1-gozlemci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim1_gozlemci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_gozetim1_teknik_uzman).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_gozetim1_teknik_uzman);
    }
  }

  if (dt_gozetim1_iku_table.length && asama === 'g1') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim1-iku thead tr').clone(true).appendTo('.dt-gozetim1-iku thead');
    $('.dt-gozetim1-iku thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim1_iku.column(i).search() !== this.value) {
          dt_gozetim1_iku.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim1_iku = dt_gozetim1_iku_table.DataTable({
      ajax: dtIkuRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim1_iku' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'giku1\', \'gozetim1-iku\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim1_iku.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_gozetim1_iku).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_gozetim1_iku);
    // }
  }

  if (dt_gozetim1_aday_denetci_table.length && asama === 'g1') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim1-aday-denetci thead tr').clone(true).appendTo('.dt-gozetim1-aday-denetci thead');
    $('.dt-gozetim1-aday-denetci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim1_aday_denetci.column(i).search() !== this.value) {
          dt_gozetim1_aday_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim1_aday_denetci = dt_gozetim1_aday_denetci_table.DataTable({
      ajax: dtAdayDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim1_aday_denetci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'gad1\', \'gozetim1-aday-denetci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim1_aday_denetci.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_gozetim1_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_gozetim1_aday_denetci);
    // }
  }

  if (dt_gozetim1_degerlendirici_table.length && asama === 'g1') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim1-degerlendirici thead tr').clone(true).appendTo('.dt-gozetim1-degerlendirici thead');
    $('.dt-gozetim1-degerlendirici thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim1_degerlendirici.column(i).search() !== this.value) {
          dt_gozetim1_degerlendirici.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim1_degerlendirici = dt_gozetim1_degerlendirici_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim1_degerlendirici' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'sidg1\', \'gozetim1-degerlendirici\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim1_degerlendirici.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci);
    // }
  }

  if (dt_gozetim2_basdenetci_table.length && asama === 'g2') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim2-basdenetciler thead tr').clone(true).appendTo('.dt-gozetim2-basdenetciler thead');
    $('.dt-gozetim2-basdenetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim2_basdenetci.column(i).search() !== this.value) {
          dt_gozetim2_basdenetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim2_basdenetci = dt_gozetim2_basdenetci_table.DataTable({
      ajax: dtBasdenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="radio" id="dt_gozetim2_basdenetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setBasdenetci(\'gbd2\', \'' + chkvalue + '\', \'gozetim2tar\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="radio" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'single'
      }
    });
    dt_gozetim2_basdenetci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_gozetim2_basdenetci).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_gozetim2_basdenetci);
    }
  }

  if (dt_gozetim2_denetci_table.length && asama === 'g2') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim2-denetciler thead tr').clone(true).appendTo('.dt-gozetim2-denetciler thead');
    $('.dt-gozetim2-denetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim2_denetci.column(i).search() !== this.value) {
          dt_gozetim2_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim2_denetci = dt_gozetim2_denetci_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim2_denetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'gd2\', \'gozetim2-denetciler\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim2_denetci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_gozetim2_denetci).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_gozetim2_denetci);
    }
  }

  if (dt_gozetim2_teknik_uzman_table.length && asama === 'g2') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim2-teknik-uzman thead tr').clone(true).appendTo('.dt-gozetim2-teknik-uzman thead');
    $('.dt-gozetim2-teknik-uzman thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim2_teknik_uzman.column(i).search() !== this.value) {
          dt_gozetim2_teknik_uzman.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim2_teknik_uzman = dt_gozetim2_teknik_uzman_table.DataTable({
      ajax: dtTeknikUzmanRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim2_teknik_uzman' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'gtu2\', \'gozetim2-teknik-uzman\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim2_teknik_uzman.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_gozetim2_teknik_uzman).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_gozetim2_teknik_uzman);
    // }
  }

  if (dt_gozetim2_gozlemci_table.length && asama === 'g2') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim2-gozlemci thead tr').clone(true).appendTo('.dt-gozetim2-gozlemci thead');
    $('.dt-gozetim2-gozlemci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim2_gozlemci.column(i).search() !== this.value) {
          dt_gozetim2_gozlemci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim2_gozlemci = dt_gozetim2_gozlemci_table.DataTable({
      ajax: dtGozlemciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim2_gozlemci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'gg2\', \'gozetim2-gozlemci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim2_gozlemci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_gozetim2_teknik_uzman).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_gozetim2_teknik_uzman);
    }
  }

  if (dt_gozetim2_iku_table.length && asama === 'g2') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim2-iku thead tr').clone(true).appendTo('.dt-gozetim2-iku thead');
    $('.dt-gozetim2-iku thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim2_iku.column(i).search() !== this.value) {
          dt_gozetim2_iku.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim2_iku = dt_gozetim2_iku_table.DataTable({
      ajax: dtIkuRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim2_iku' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'giku2\', \'gozetim2-iku\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim2_iku.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_gozetim2_iku).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_gozetim2_iku);
    // }
  }

  if (dt_gozetim2_aday_denetci_table.length && asama === 'g2') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim2-aday-denetci thead tr').clone(true).appendTo('.dt-gozetim2-aday-denetci thead');
    $('.dt-gozetim2-aday-denetci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim2_aday_denetci.column(i).search() !== this.value) {
          dt_gozetim2_aday_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim2_aday_denetci = dt_gozetim2_aday_denetci_table.DataTable({
      ajax: dtAdayDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim2_aday_denetci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'gad2\', \'gozetim2-aday-denetci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim2_aday_denetci.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_gozetim2_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_gozetim2_aday_denetci);
    // }
  }

  if (dt_gozetim2_degerlendirici_table.length && asama === 'g2') {
    // Setup - add a text input to each footer cell
    $('.dt-gozetim2-degerlendirici thead tr').clone(true).appendTo('.dt-gozetim2-degerlendirici thead');
    $('.dt-gozetim2-degerlendirici thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_gozetim2_degerlendirici.column(i).search() !== this.value) {
          dt_gozetim2_degerlendirici.column(i).search(this.value).draw();
        }
      });
    });

    var dt_gozetim2_degerlendirici = dt_gozetim2_degerlendirici_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_gozetim2_degerlendirici' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'sidg2\', \'gozetim2-degerlendirici\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_gozetim2_degerlendirici.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci);
    // }
  }

  if (dt_yb_basdenetci_table.length && asama === 'yb') {
    // Setup - add a text input to each footer cell
    $('.dt-yb-basdenetciler thead tr').clone(true).appendTo('.dt-yb-basdenetciler thead');
    $('.dt-yb-basdenetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_yb_basdenetci.column(i).search() !== this.value) {
          dt_yb_basdenetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_yb_basdenetci = dt_yb_basdenetci_table.DataTable({
      ajax: dtBasdenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="radio" id="dt_yb_basdenetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setBasdenetci(\'ybbd\', \'' + chkvalue + '\', \'ybtar\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="radio" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'single'
      }
    });
    dt_yb_basdenetci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_yb_basdenetci).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_yb_basdenetci);
    }
  }

  if (dt_yb_denetci_table.length && asama === 'yb') {
    // Setup - add a text input to each footer cell
    $('.dt-yb-denetciler thead tr').clone(true).appendTo('.dt-yb-denetciler thead');
    $('.dt-yb-denetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_yb_denetci.column(i).search() !== this.value) {
          dt_yb_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_yb_denetci = dt_yb_denetci_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_yb_denetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'ybd\', \'yb-denetciler\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_yb_denetci.columns.adjust().draw();

    // Fixed header
    if (window.Helpers.isNavbarFixed()) {
      navHeight = $('#layout-navbar').outerHeight();
      new $.fn.dataTable.FixedHeader(dt_yb_denetci).headerOffset(navHeight);
    } else {
      new $.fn.dataTable.FixedHeader(dt_yb_denetci);
    }
  }

  if (dt_yb_teknik_uzman_table.length && asama === 'yb') {
    // Setup - add a text input to each footer cell
    $('.dt-yb-teknik-uzman thead tr').clone(true).appendTo('.dt-yb-teknik-uzman thead');
    $('.dt-yb-teknik-uzman thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_yb_teknik_uzman.column(i).search() !== this.value) {
          dt_yb_teknik_uzman.column(i).search(this.value).draw();
        }
      });
    });

    var dt_yb_teknik_uzman = dt_yb_teknik_uzman_table.DataTable({
      ajax: dtTeknikUzmanRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_yb_teknik_uzman' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'ybtu\', \'yb-teknik-uzman\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_yb_teknik_uzman.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_yb_teknik_uzman).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_yb_teknik_uzman);
    // }
  }

  if (dt_yb_gozlemci_table.length && asama === 'yb') {
    // Setup - add a text input to each footer cell
    $('.dt-yb-gozlemci thead tr').clone(true).appendTo('.dt-yb-gozlemci thead');
    $('.dt-yb-gozlemci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_yb_gozlemci.column(i).search() !== this.value) {
          dt_yb_gozlemci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_yb_gozlemci = dt_yb_gozlemci_table.DataTable({
      ajax: dtGozlemciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_yb_gozlemci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'ybg\', \'yb-gozlemci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_yb_gozlemci.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_yb_teknik_uzman).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_yb_teknik_uzman);
    // }
  }

  if (dt_yb_iku_table.length && asama === 'yb') {
    // Setup - add a text input to each footer cell
    $('.dt-yb-iku thead tr').clone(true).appendTo('.dt-yb-iku thead');
    $('.dt-yb-iku thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_yb_iku.column(i).search() !== this.value) {
          dt_yb_iku.column(i).search(this.value).draw();
        }
      });
    });

    var dt_yb_iku = dt_yb_iku_table.DataTable({
      ajax: dtIkuRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_yb_iku' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'ybiku\', \'yb-iku\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_yb_iku.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_yb_iku).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_yb_iku);
    // }
  }

  if (dt_yb_aday_denetci_table.length && asama === 'yb') {
    // Setup - add a text input to each footer cell
    $('.dt-yb-aday-denetci thead tr').clone(true).appendTo('.dt-yb-aday-denetci thead');
    $('.dt-yb-aday-denetci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_yb_aday_denetci.column(i).search() !== this.value) {
          dt_yb_aday_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_yb_aday_denetci = dt_yb_aday_denetci_table.DataTable({
      ajax: dtAdayDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_yb_aday_denetci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'ybad\', \'yb-aday-denetci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_yb_aday_denetci.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_yb_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_yb_aday_denetci);
    // }
  }

  if (dt_yb_degerlendirici_table.length && asama === 'yb') {
    // Setup - add a text input to each footer cell
    $('.dt-yb-degerlendirici thead tr').clone(true).appendTo('.dt-yb-degerlendirici thead');
    $('.dt-yb-degerlendirici thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_yb_degerlendirici.column(i).search() !== this.value) {
          dt_yb_degerlendirici.column(i).search(this.value).draw();
        }
      });
    });

    var dt_yb_degerlendirici = dt_yb_degerlendirici_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_yb_degerlendirici' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'sidyb\', \'yb-degerlendirici\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_yb_degerlendirici.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci);
    // }
  }

  if (dt_ot_basdenetci_table.length && asama === 'ozel') {
    // Setup - add a text input to each footer cell
    $('.dt-ot-basdenetciler thead tr').clone(true).appendTo('.dt-ot-basdenetciler thead');
    $('.dt-ot-basdenetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_ot_basdenetci.column(i).search() !== this.value) {
          dt_ot_basdenetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_ot_basdenetci = dt_ot_basdenetci_table.DataTable({
      ajax: dtBasdenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="radio" id="dt_ot_basdenetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setBasdenetci(\'otbd\', \'' + chkvalue + '\', \'ozeltar\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="radio" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'single'
      }
    });
    dt_ot_basdenetci.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_ot_basdenetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_ot_basdenetci);
    // }
  }

  if (dt_ot_denetci_table.length && asama === 'ozel') {
    // Setup - add a text input to each footer cell
    $('.dt-ot-denetciler thead tr').clone(true).appendTo('.dt-ot-denetciler thead');
    $('.dt-ot-denetciler thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_ot_denetci.column(i).search() !== this.value) {
          dt_ot_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_ot_denetci = dt_ot_denetci_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_ot_denetci_table_' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'otd\', \'ot-denetciler\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_ot_denetci.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_ot_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_ot_denetci);
    // }
  }

  if (dt_ot_teknik_uzman_table.length && asama === 'ozel') {
    // Setup - add a text input to each footer cell
    $('.dt-ot-teknik-uzman thead tr').clone(true).appendTo('.dt-ot-teknik-uzman thead');
    $('.dt-ot-teknik-uzman thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_ot_teknik_uzman.column(i).search() !== this.value) {
          dt_ot_teknik_uzman.column(i).search(this.value).draw();
        }
      });
    });

    var dt_ot_teknik_uzman = dt_ot_teknik_uzman_table.DataTable({
      ajax: dtTeknikUzmanRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_ot_teknik_uzman' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'ottu\', \'ot-teknik-uzman\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_ot_teknik_uzman.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_ot_teknik_uzman).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_ot_teknik_uzman);
    // }
  }

  if (dt_ot_gozlemci_table.length && asama === 'ozel') {
    // Setup - add a text input to each footer cell
    $('.dt-ot-gozlemci thead tr').clone(true).appendTo('.dt-ot-gozlemci thead');
    $('.dt-ot-gozlemci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_ot_gozlemci.column(i).search() !== this.value) {
          dt_ot_gozlemci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_ot_gozlemci = dt_ot_gozlemci_table.DataTable({
      ajax: dtGozlemciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'}, {data: 'sistemler'},
        {data: 'nace'},
        {data: 'kategori'},
        {data: 'kategorioic'},
        {data: 'kategoribg'},
        {data: 'teknikalan'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_ot_gozlemci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'otg\', \'ot-gozlemci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 2,
          render: function (data, type, full, meta) {
            var $name = full['denetci'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '20%',
          targets: 3,
          render: function (data, type, full, meta) {
            var $name = full['sistemler'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // kuruluş adı/adesi/kapsamı
          width: '30%',
          targets: 4,
          render: function (data, type, full, meta) {
            var $name = full['nace'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center">' +
              '<div class="d-flex flex-column">' +
              '<span class="emp_name text-truncate text-heading fw-medium text-wrap">' +
              $name +
              '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          width: '10%',
          targets: 5
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_ot_gozlemci.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_ot_teknik_uzman).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_ot_teknik_uzman);
    // }
  }

  if (dt_ot_iku_table.length && asama === 'ozel') {
    // Setup - add a text input to each footer cell
    $('.dt-ot-iku thead tr').clone(true).appendTo('.dt-ot-iku thead');
    $('.dt-ot-iku thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_ot_iku.column(i).search() !== this.value) {
          dt_ot_iku.column(i).search(this.value).draw();
        }
      });
    });

    var dt_ot_iku = dt_ot_iku_table.DataTable({
      ajax: dtIkuRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_ot_iku' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'otiku\', \'ot-iku\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_ot_iku.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_ot_iku).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_ot_iku);
    // }
  }

  if (dt_ot_aday_denetci_table.length && asama === 'ozel') {
    // Setup - add a text input to each footer cell
    $('.dt-ot-aday-denetci thead tr').clone(true).appendTo('.dt-ot-aday-denetci thead');
    $('.dt-ot-aday-denetci thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_ot_aday_denetci.column(i).search() !== this.value) {
          dt_ot_aday_denetci.column(i).search(this.value).draw();
        }
      });
    });

    var dt_ot_aday_denetci = dt_ot_aday_denetci_table.DataTable({
      ajax: dtAdayDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_ot_aday_denetci' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'otad\', \'ot-aday-denetci\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_ot_aday_denetci.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_ot_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_ot_aday_denetci);
    // }
  }

  if (dt_ot_degerlendirici_table.length && asama === 'ozel') {
    // Setup - add a text input to each footer cell
    $('.dt-ot-degerlendirici thead tr').clone(true).appendTo('.dt-ot-degerlendirici thead');
    $('.dt-ot-degerlendirici thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_ot_degerlendirici.column(i).search() !== this.value) {
          dt_ot_degerlendirici.column(i).search(this.value).draw();
        }
      });
    });

    var dt_ot_degerlendirici = dt_ot_degerlendirici_table.DataTable({
      ajax: dtDenetciRoutePath,
      columns: [
        {data: 'id'},
        {data: 'id'},
        {data: 'denetci'},
        {data: 'sistemler'}
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          width: '10px',
          render: function (data, type, full, meta) {
            var chkvalue = full['denetci'];
            return '<input type="checkbox" id="dt_ot_degerlendirici' + full['id'] + '" value="' + chkvalue + '" onclick="setDenetci(\'sidot\', \'ot-degerlendirici\')" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        }

      ],
      orderCellsTop: true,
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    dt_ot_degerlendirici.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_asama2_aday_denetci);
    // }
  }

  if (tblbelgelifirmalarals05.length) {
    // Setup - add a text input to each footer cell
    $('#tblbelgelifirmalarals05 thead tr').clone(true).appendTo('#tblbelgelifirmalarals05 thead');
    $('#tblbelgelifirmalarals05 thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (belgelifirmalarals05.column(i).search() !== this.value) {
          belgelifirmalarals05.column(i).search(this.value).draw();
        }
      });
    });

    var belgelifirmalarals05 = tblbelgelifirmalarals05.DataTable({
      // ajax: tblbelgelifirmalarals05RoutePath,
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-secondary dropdown-toggle me-3 waves-effect waves-light',
          text: '<i class="mdi mdi-export-variant me-1"></i> <span class="d-none d-sm-inline-block">Dışarı Aktar</span>',
          buttons: [
            {
              extend: 'excelHtml5',
              autoFilter: true,
              text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
              className: 'dropdown-item',
              exportOptions: {
                // columns: [1, 2, 3, 4, 5, 6, 7],
              }
            },
            {
              extend: 'pdf',
              text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
              className: 'dropdown-item',
            }
          ]
        }
      ],
      orderCellsTop: true,
      order: [[12, 'desc']],
      dom:
        '<"row mx-2"' +
        '<"col-md-2"<"me-3"l>>' +
        '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0 gap-3"fB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
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
      paging: false,
      scrollX: true,
      scrollY: '400px',
      select: {
        // Select style
        style: 'multi'
      }
    });
    belgelifirmalarals05.columns.adjust().draw();

    // Fixed header
    // if (window.Helpers.isNavbarFixed()) {
    //   navHeight = $('#layout-navbar').outerHeight();
    //   new $.fn.dataTable.FixedHeader(dt_ot_aday_denetci).headerOffset(navHeight);
    // } else {
    //   new $.fn.dataTable.FixedHeader(dt_ot_aday_denetci);
    // }
  }

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
  denetimTakvimiGoster();
});


function denetimTakvimiGoster() {
  if ($("#divdenetimtakvimi").length > 0) {
    var ay = $("#curmonth").html();
    var yil = $("#curyear").html();
    var datastring = "ay=" + ay + "&yil=" + yil;
    // console.log("denetimTakvimiGoster: "+datastring);
    $.ajax({
      type: "GET",
      url: denetimTakvimiRoutePath,
      data: datastring,
      cache: false,
      success: function (html) {
        $("#divdenetimtakvimi").html(html);
      }
    });
  }
}
