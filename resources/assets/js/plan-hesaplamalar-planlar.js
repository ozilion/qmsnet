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

var isFaktor = 0;
var btFaktor = 0;

$(function () {
  denetimRaporuYukle();

  bgMudurOnayAcKapa();

  $('#denetimZamanHesaplari tbody input[type="checkbox"]').on('change', subeSurelerEkle);

  // Initial calculation
  subeSurelerEkle();

  runBgysCalculation();
});

function changeCevrim() {
  var selectedCevirim = $('#cevrim').val(); // Get the selected cevirim value
  var pno = $('#planno').val(); // Get the selected planno value
  var asama = $('#asama').val(); // Get the selected asama value

  // Proceed with the page refresh logic or other actions
  if (selectedCevirim !== "0") {
    // Generate the URL dynamically
    const formURL = $('#cevrimYenileRoute').val();  // The route from input field

    // Append the parameters to the URL
    const newURL = checkCevrimRoutes(selectedCevirim, pno, asama);

    // Refresh the page with new URL
    window.location.href = newURL;  // Page will reload with the new URL
  }
}

function denetimRaporuYukle(){
  const isChecked = $("#asama").val();

  if (isChecked === "rpyukle"){
    $('#btnDenetimRaporuYukle').show();
  }
  if (isChecked  !== "rpyukle"){
    $('#btnDenetimRaporuYukle').hide();
  }
}

function bgMudurOnayAcKapa(){
  const isChecked = $("#asama").val();

  if (isChecked === "g1karar" || isChecked === "g2karar"){
    $('#divbgmuduronay').show();
  }
  if (isChecked !== "g1karar" && isChecked !== "g2karar"){
    $('#divbgmuduronay').hide();
  }
}

/**
 * setEaNaceKategori
 * - Tablolardaki checkbox'ları (görünür/görünmez sayfalar dahil) okur.
 * - Seçilen satırlardaki verileri (kk9, kk14, ea, nace, vb.) diziye ekler.
 * - Elde edilen dizileri ekrana ve gizli input'lara yazar.
 * - Son olarak verileri setLblEaNaceKats fonksiyonuna gönderir.
 */
function setEaNaceKategori() {
  // Bu değerleri en başta "boş" tanımlıyoruz.
  // Koşullar sağlanırsa gerçek değerler ile dolduracağız.
  let nace = [];
  let kat22 = [];
  let oicKat = [];
  let enysKat = [];
  let bgysKat = []; // 27001

  // EA-NACE
  if (iso900115varyok === 1 || iso1400115varyok === 1 || iso4500118varyok === 1) {
    let { kk9, kk14, kk45, ea, nace: foundNace } = getEaNaceSelections(dt_filter_en);

    // Ekranda göster
    $('#lbl_ea').text(`[Q:${kk9}, E:${kk14}, O:${kk45}] ${ea}`);
    $('#diveanace').html(`[Q:${kk9}, E:${kk14}, O:${kk45}] ${ea} | ${foundNace}`);

    // Gizli inputlara yaz
    $('#gizliea').val(ea);
    $('#gizlinace').val(foundNace);
    $('#riskgrubu9').val(kk9);
    $('#riskgrubu14').val(kk14);
    $('#riskgrubu45').val(kk45);

    // setLblEaNaceKats'a göndereceğimiz "nace" değerini burada saklıyoruz
    nace = foundNace;
  }

  // 22000
  if (iso2200018varyok === 1) {
    let cat22000 = get22000Selections(dt_filter22);
    $('#div22cat').html(`${cat22000.kats22} ::: bb::${cat22000.bb22} ::: cc::${cat22000.cc22}`);
    $('#gizlikat').val(cat22000.kats22);
    $('#gizlikatbb').val(cat22000.bb22);
    $('#gizlikatcc').val(cat22000.cc22);

    // setLblEaNaceKats'a göndereceğimiz "kat22" değerini sakla
    kat22 = cat22000.kats22;
  }

  // SMIIC / OIC
  if (helalvaryok === 1 || oicsmiik6varyok === 1 || oicsmiik9varyok === 1 || oicsmiik23varyok === 1 || oicsmiik24varyok === 1 || oicsmiik171varyok === 1) {
    let catOic = getSmiicSelections(dt_smiic);
    $('#divoiccat').html(`${catOic.katsoic} ::: bb::${catOic.bboic} ::: cc::${catOic.ccoic}`);
    $('#gizlioickat').val(catOic.katsoic);
    $('#gizlikatbboic').val(catOic.bboic);
    $('#gizlikatccoic').val(catOic.ccoic);

    oicKat = catOic.katsoic;
  }

  // 27001
  if (iso27001varyok === 1) {
    let cat27001 = get27001Selections(dt_27001);
    $('#div27001cat').html(`${cat27001.ta27001} ::: tag27001::${cat27001.tag27001}`);
    $('#gizlibgys').val(cat27001.ta27001);

    bgysKat = cat27001.ta27001;
  }

  // 50001 (ENYS)
  if (iso5000118varyok === 1) {
    let cat50001 = get50001Selections(dt_50001);
    $('#div50001cat').html(`${cat50001.ta50001} ::: tag50001::${cat50001.tag50001}`);
    $('#gizlienysta').val(cat50001.ta50001);
    $('#lbl_enys').val(cat50001.ta50001);

    enysKat = cat50001.ta50001;
  }

  // Son olarak etiketleri güncelle
  // Artık setLblEaNaceKats'a her bir tablo için elde ettiğimiz dizileri parametre olarak veriyoruz
  // (Tablo seçilmeyen durumlarda boş dizi gönderilmiş olur)
  setLblEaNaceKats(nace, kat22, oicKat, enysKat, bgysKat);
}

/**
 * getEaNaceSelections
 * - DataTables API kullanarak, tüm sayfalardaki seçili checkbox'ları bulur.
 * - kk9, kk14, kk45, ea, nace dizilerini döndürür.
 */
function getEaNaceSelections(dataTableInstance) {
  let kk9 = [], kk14 = [], kk45 = [], ea = [], nace = [];

  // Tüm sayfalardaki (gizli/görünür) checkbox'ları tarar
  dataTableInstance.$('input[type="checkbox"]:checked').each(function() {
    const $row = $(this).closest('tr');

    // Hücre değerlerini topla (eq(2): kk9, eq(3): kk14, eq(4): kk45, eq(5): ea, eq(6): nace)
    let kk9tmp   = $row.find('td:eq(2)').text().trim();
    let kk14tmp  = $row.find('td:eq(3)').text().trim();
    let kk45tmp  = $row.find('td:eq(4)').text().trim();
    let eatmp    = $row.find('td:eq(5)').text().trim();
    let nacetmp  = $row.find('td:eq(6)').text().trim();

    pushUnique(kk9, kk9tmp);
    pushUnique(kk14, kk14tmp);
    pushUnique(kk45, kk45tmp);
    pushUnique(ea, eatmp);
    pushUnique(nace, nacetmp);
  });

  // Sıralama
  kk9.sort();
  kk14.sort();
  kk45.sort();
  ea.sort();
  nace.sort();

  return { kk9, kk14, kk45, ea, nace };
}

/**
 * get22000Selections
 * - 22000 tablosunda checkbox seçimi olan satırlardan kats22, bb22, cc22 değerlerini hesaplar.
 */
function get22000Selections(dataTableInstance) {
  let kats22 = [];
  let bb22 = 0.0, cc22 = 0.0;

  dataTableInstance.$('input[type="checkbox"]:checked').each(function() {
    const $row = $(this).closest('tr');
    let katstmp = $row.find('td:eq(2)').text().trim();

    // 5. ve 6. sütundaki numeric değerler (ör: 1,23 => parseFloat("1.23"))
    let bbtmp = parseFloat($row.find('td:eq(5)').text().trim().replace(',', '.')) || 0;
    let cctmp = parseFloat($row.find('td:eq(6)').text().trim().replace(',', '.')) || 0;

    pushUnique(kats22, katstmp);

    // En büyük bb/cctmp değerini saklamak istediğinizi varsayıyorum (kodunuzda if (bb22 < bbtmp) var).
    if (bbtmp > bb22) {
      bb22 = bbtmp;
      cc22 = cctmp;
    }
  });

  kats22.sort();
  return { kats22, bb22, cc22 };
}

/**
 * getSmiicSelections
 * - SMIIC / OIC tablosundaki seçili satırlardan katsoic, bboic, ccoic hesaplar.
 */
function getSmiicSelections(dataTableInstance) {
  let katsoic = [];
  let bboic = 0.0, ccoic = 0.0;

  dataTableInstance.$('input[type="checkbox"]:checked').each(function() {
    const $row = $(this).closest('tr');
    let katstmp = $row.find('td:eq(2)').text().trim();

    let bbtmp = parseFloat($row.find('td:eq(6)').text().trim().replace(',', '.')) || 0;
    let cctmp = parseFloat($row.find('td:eq(7)').text().trim().replace(',', '.')) || 0;

    pushUnique(katsoic, katstmp);

    if (bbtmp > bboic) {
      bboic = bbtmp;
      ccoic = cctmp;
    }
  });

  katsoic.sort();
  return { katsoic, bboic, ccoic };
}

/**
 * get27001Selections
 */
function get27001Selections(dataTableInstance) {
  let ta27001 = [], tag27001 = [];

  dataTableInstance.$('input[type="checkbox"]:checked').each(function() {
    const $row = $(this).closest('tr');
    let katstmp  = $row.find('td:eq(5)').text().trim();
    let katsgtmp = $row.find('td:eq(6)').text().trim();

    pushUnique(ta27001, katstmp);
    pushUnique(tag27001, katsgtmp);
  });

  ta27001.sort();
  tag27001.sort();

  return { ta27001, tag27001 };
}

/**
 * get50001Selections
 */
function get50001Selections(dataTableInstance) {
  let ta50001 = [], tag50001 = [];

  dataTableInstance.$('input[type="checkbox"]:checked').each(function() {
    const $row = $(this).closest('tr');
    let katstmp  = $row.find('td:eq(2)').text().trim();
    let katsgtmp = $row.find('td:eq(4)').text().trim();

    pushUnique(ta50001, katstmp);
    pushUnique(tag50001, katsgtmp);
  });

  ta50001.sort();
  tag50001.sort();

  return { ta50001, tag50001 };
}

/**
 * pushUnique
 * - Bir dizide eleman yoksa ekler, varsa eklemez (tekrar etmeyen liste).
 */
function pushUnique(arr, val) {
  if (val && !arr.includes(val)) {
    arr.push(val);
  }
}

/**
 * setLblEaNaceKats
 * - Kodun orijinalinde bulunan çok dallı if-else yapısı,
 *   dizi kombinasyonlarını farklı karakterlerle birleştiriyor (|, ß, Æ, €).
 * - Sadeleştirmek için orijinal mantığı koruyoruz.
 */
function setLblEaNaceKats(valuenace, valuekat, valueoickat, valueenysta, valuebgys) {
  const lbleanacekat = $('#lbl_eanacekat');

  // Orijinal if-else yapısının "temiz kod" versiyonunu
  // hepsini bir arada kontrol eden bir mantığa dönüştürebilirsiniz.
  // Burada değişmeden koruyoruz:
  if (valuenace.length > 0 && valuekat.length === 0 && valueoickat.length === 0 && valueenysta.length === 0 && valuebgys.length === 0) {
    lbleanacekat.val(valuenace.toString());
  } else if (valuenace.length === 0 && valuekat.length > 0 && valueoickat.length === 0 && valueenysta.length === 0 && valuebgys.length === 0) {
    lbleanacekat.val(valuekat.toString());
  } else if (valuenace.length === 0 && valuekat.length === 0 && valueoickat.length > 0 && valueenysta.length === 0 && valuebgys.length === 0) {
    lbleanacekat.val(valueoickat.toString());
  } else if (valuenace.length === 0 && valuekat.length === 0 && valueoickat.length === 0 && valueenysta.length > 0 && valuebgys.length === 0) {
    lbleanacekat.val(valueenysta.toString());
  } else if (valuenace.length === 0 && valuekat.length === 0 && valueoickat.length === 0 && valueenysta.length === 0 && valuebgys.length > 0) {
    lbleanacekat.val(valuebgys.toString());
  } else if (valuenace.length > 0 && valuekat.length > 0 && valueoickat.length === 0 && valueenysta.length === 0 && valuebgys.length === 0) {
    lbleanacekat.val(valuenace.toString() + '|' + valuekat.toString());
  } else if (valuenace.length > 0 && valuekat.length === 0 && valueoickat.length > 0 && valueenysta.length === 0 && valuebgys.length === 0) {
    lbleanacekat.val(valuenace.toString() + 'ß' + valueoickat.toString());
  } else if (valuenace.length > 0 && valuekat.length === 0 && valueoickat.length === 0 && valueenysta.length > 0 && valuebgys.length === 0) {
    lbleanacekat.val(valuenace.toString() + 'Æ' + valueenysta.toString());
  } else if (valuenace.length === 0 && valuekat.length > 0 && valueoickat.length > 0 && valueenysta.length === 0 && valuebgys.length === 0) {
    lbleanacekat.val(valuekat.toString() + 'ß' + valueoickat.toString());
  } else if (valuenace.length === 0 && valuekat.length > 0 && valueoickat.length === 0 && valueenysta.length > 0 && valuebgys.length === 0) {
    lbleanacekat.val(valuekat.toString() + 'Æ' + valueenysta.toString());
  } else if (valuenace.length === 0 && valuekat.length === 0 && valueoickat.length === 0 && valueenysta.length > 0 && valuebgys.length > 0) {
    lbleanacekat.val(valueenysta.toString() + '€' + valuebgys.toString());
  } else if (valuenace.length > 0 && valuekat.length > 0 && valueoickat.length > 0 && valueenysta.length === 0 && valuebgys.length === 0) {
    lbleanacekat.val(valuenace.toString() + '|' + valuekat.toString() + 'ß' + valueoickat.toString());
  } else if (valuenace.length > 0 && valuekat.length > 0 && valueoickat.length > 0 && valueenysta.length === 0 && valuebgys.length > 0) {
    lbleanacekat.val(valuenace.toString() + '|' + valuekat.toString() + 'ß' + valueoickat.toString() + '€' + valuebgys.toString());
  } else if (valuenace.length > 0 && valuekat.length > 0 && valueoickat.length > 0 && valueenysta.length > 0 && valuebgys.length > 0) {
    lbleanacekat.val(valuenace.toString() + '|' + valuekat.toString() + 'ß' + valueoickat.toString() + 'Æ' + valueenysta.toString() + '€' + valuebgys.toString());
  } else {
    lbleanacekat.val('NA');
  }
}

function getDenetimOnerilenBasdenetci() {
  var nace = $('#gizlinace').val().replace('|', '');
  var kat22 = $('#gizlikat').val().replace('@', '');
  var katoic = $('#gizlioickat').val().replace('ß', '');
  var tabgys = $('#gizlibgys').val().replace('€', '');
  var taenys = $('#gizlienysta').val().replace('Æ', '');

  var postData = 'nace=' + nace + '&kat22=' + kat22 + '&katoic=' + katoic + '&tabgys=' + tabgys + '&taenys=' + taenys;
  var formURL = getDenetimOnerilenBasdenetciPath;

  // Mevcut tablo varsa temizle
  var dt_denetim_onerilen_basdenetci_table = $('.dt-denetim-onerilen-basdenetci');
  var existingTable = dt_denetim_onerilen_basdenetci_table.DataTable();

  if ($.fn.DataTable.isDataTable('.dt-denetim-onerilen-basdenetci')) {
    existingTable.clear().destroy();
    // Ekstra oluşturulmuş olan thead satırını temizle
    $('.dt-denetim-onerilen-basdenetci thead tr:not(:first)').remove();
  }

  $.ajax({
    url: formURL,
    type: 'GET',
    data: postData,
    success: function (html) {
      if (dt_denetim_onerilen_basdenetci_table.length) {
        // Setup - add a text input to each footer cell
        $('.dt-denetim-onerilen-basdenetci thead tr').clone(true).appendTo('.dt-denetim-onerilen-basdenetci thead');
        $('.dt-denetim-onerilen-basdenetci thead tr:eq(1) th').each(function (i) {
          // İlk 3 sütun ve EA, Kategori, Teknik Alan sütunları için arama input'u ekle (0-checkbox, 1-id, 2-adı soyadı, 3-ea, 5-kategori, 8-teknik alan)
          if (i == 2 || i == 3 || i == 5 || i == 8) {
            var title = $(this).text();
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" />');

            $('input', this).on('keyup change', function () {
              if (dt_denetim_onerilen_basdenetci.column(i).search() !== this.value) {
                dt_denetim_onerilen_basdenetci.column(i).search(this.value).draw();
              }
            });
          } else {
            // Diğer sütunlar için boş div
            $(this).html('');
          }
        });

        var dt_denetim_onerilen_basdenetci = dt_denetim_onerilen_basdenetci_table.DataTable({
          data: html,
          columns: [
            {data: 'id'}, // Checkbox
            {data: 'id'}, // ID
            {data: 'denetci'}, // Adı Soyadı
            {data: 'ea'}, // Ea Kodu - görünür
            {data: 'nace', visible: false}, // Gizli sütun
            {data: 'kategori'}, // 22000 Kategori - görünür
            {data: 'kategorioic', visible: false}, // Gizli sütun
            {data: 'kategoribg', visible: false}, // Gizli sütun
            {data: 'teknikalan'} // Teknik Alan - görünür
          ],
          columnDefs: [
            {
              // For Radio Buttons
              targets: 0,
              searchable: false,
              orderable: false,
              width: '30px',
              className: 'dt-center',
              render: function (data, type, full, meta) {
                var chkvalue = full['denetci'];
                return '<input type="radio" name="denetci_radio" id="dt_denetim_onerilen_basdenetci_table' + full['id'] + '" value="' + chkvalue + '" onclick="setOnerilenBasdenetci(\'' + chkvalue + '\')" class="form-check-input">';
              }
            },
            {
              targets: 1,
              width: '0px',
              visible: false // ID sütununu gizle
            },
            {
              targets: 2,
              width: '100px', // Adı soyadı sütununu genişlet
              className: 'dt-denetci-link', // Tıklanabilir sınıf ekle
              render: function (data, type, full, meta) {
                return '<a href="javascript:void(0);" onclick="showDenetciDetails(\'' +
                  full['id'] + '\', \'' +
                  full['denetci'] + '\', \'' +
                  (full['ea'] || '') + '\', \'' +
                  (full['nace'] || '') + '\', \'' +
                  (full['kategori'] || '') + '\', \'' +
                  (full['kategorioic'] || '') + '\', \'' +
                  (full['kategoribg'] || '') + '\', \'' +
                  (full['teknikalan'] || '') + '\')">' + data + '</a>';
              }
            },
            {
              targets: 3,
              width: '120px', // EA Kod sütunu genişliği
              className: 'dt-left'
            },
            {
              targets: 5,
              width: '120px', // Kategori sütunu genişliği
              className: 'dt-left'
            },
            {
              targets: 8,
              width: '120px', // Teknik Alan sütunu genişliği
              className: 'dt-left'
            }
          ],
          orderCellsTop: true,
          order: [[2, 'asc']], // Adı soyadına göre sırala
          dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
          paging: false,
          scrollX: true, // Yatay kaydırma aktif
          scrollY: '400px',
          scrollCollapse: true,
          autoWidth: false, // Otomatik genişliği devre dışı bırak - bu önemli
          fixedColumns: true, // Tablo genişliği sabit kalsın
          retrieve: false,
          select: {
            style: 'single'
          },
          responsive: true, // Responsive özelliğini aktifleştir
          language: {
            search: "Ara:",
            lengthMenu: "_MENU_ kayıt göster",
            info: "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
            infoEmpty: "Kayıt yok",
            infoFiltered: "(_MAX_ kayıt arasından filtrelendi)",
            zeroRecords: "Eşleşen kayıt bulunamadı"
          }
        });
        dt_denetim_onerilen_basdenetci.columns.adjust().draw();
      }
      getOnerilenKararUyeleri();
      hesapla();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $('#formkaydetsonucerror').html('[getDenetimOnerilenBasdenetci]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
      $('#myModalError').modal('show');
    }
  });
}

// Denetçi detaylarını gösterecek modal fonksiyonu
function showDenetciDetails(id, denetci, ea, nace, kategori, kategorioic, kategoribg, teknikalan) {
  // Detay modal içeriğini hazırla
  var modalContent = `
    <div class="modal-header">
      <h5 class="modal-title">Denetçi Detayları: ${denetci}</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <th>Ea Kod</th>
              <td>${ea || '-'}</td>
            </tr>
            <tr>
              <th>Nace Kodu</th>
              <td>${nace || '-'}</td>
            </tr>
            <tr>
              <th>22000 Kategori</th>
              <td>${kategori || '-'}</td>
            </tr>
            <tr>
              <th>Bgys Teknik Alan Kodu</th>
              <td>${kategoribg || '-'}</td>
            </tr>
            <tr>
              <th>Enys Teknik Alan Kodu</th>
              <td>${teknikalan || '-'}</td>
            </tr>
            <tr>
              <th>Oic/Smiic Kategori</th>
              <td>${kategorioic || '-'}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" onclick="selectDenetci('${id}', '${denetci}')">Bu Denetçiyi Seç</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
    </div>
  `;

  // Modal'ı göster
  var detailModal = $('#denetciDetailModal');

  // Modal yoksa oluştur
  if (detailModal.length === 0) {
    $('body').append('<div class="modal fade" id="denetciDetailModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"></div></div></div>');
    detailModal = $('#denetciDetailModal');
  }

  // Modal içeriğini güncelle
  detailModal.find('.modal-content').html(modalContent);

  // Modal'ı göster
  var modal = new bootstrap.Modal(document.getElementById('denetciDetailModal'));
  modal.show();
}

// Denetçi seçme fonksiyonu
function selectDenetci(id, denetci) {
  // Tüm radio butonlarını temizle
  $('input[name="denetci_radio"]').prop('checked', false);

  // Seçilen radio butonunu işaretle
  $('#dt_denetim_onerilen_basdenetci_table' + id).prop('checked', true);

  // setOnerilenBasdenetci fonksiyonunu çağır
  setOnerilenBasdenetci(denetci);

  // Modal'ı kapat
  var detailModal = bootstrap.Modal.getInstance(document.getElementById('denetciDetailModal'));
  detailModal.hide();
}
// Sayfanın yüklenmesinde ve resize olaylarda çalıştırılacak yeniden düzenleme fonksiyonu

// Sayfanın yüklenmesinde ve resize olaylarda çalıştırılacak yeniden düzenleme fonksiyonu
function adjustDataTableColumns() {
  // DataTable varsa
  if ($.fn.DataTable.isDataTable('.dt-denetim-onerilen-basdenetci')) {
    var dataTable = $('.dt-denetim-onerilen-basdenetci').DataTable();

    // Gizli sütunları güncelle
    var hiddenColumns = [1, 4, 6, 7];
    hiddenColumns.forEach(function(colIdx) {
      dataTable.column(colIdx).nodes().each(function(cell) {
        $(cell).addClass('dt-hidden-column');
      });
    });

    // Sütun genişliklerini düzelt
    dataTable.columns.adjust().draw();
  }
}

// Sayfa yüklendiğinde veya pencere boyutu değiştiğinde
$(window).on('load resize orientationchange', function() {
  adjustDataTableColumns();
});

// Modal açıldığında da çalıştır
$('#myModalbddenetim').on('shown.bs.modal', function() {
  setTimeout(adjustDataTableColumns, 100); // Modal animasyonu tamamlandıktan sonra çalıştır
});

// DataTables'ı oluşturduktan sonra ek yapılandırma
function initializeDataTablesSettings() {
  // DataTables'ın global ayarlarını düzenle
  $.extend(true, $.fn.dataTable.defaults, {
    language: {
      search: "Ara:",
      lengthMenu: "_MENU_ kayıt göster",
      info: "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
      infoEmpty: "Kayıt yok",
      infoFiltered: "(_MAX_ kayıt arasından filtrelendi)",
      zeroRecords: "Eşleşen kayıt bulunamadı"
    },
    // Varsayılan ayarlar
    scrollCollapse: true,
    autoWidth: false
  });
}

// Sayfa hazır olduğunda çalıştır
$(document).ready(function() {
  initializeDataTablesSettings();
});

function setOnerilenBasdenetci(val) {
  $('#divbddenetime').val(val);
}

function getOnerilenKararUyeleri() {
  var postData = 'ea=' + $('#gizliea').val() + '&kat22=' + $('#gizlikat').val() + '&katoic=' + $('#gizlioickat').val() + '&tabgys=' + $('#gizlibgys').val() + '&taenys=' + $('#gizlienysta').val();

  var formURL = getOnerilenKararUyeleriPath; //$("#getDenetimOnerilenBasdenetciRoute").val();

  // console.log("formKaydet::route:: ");
  // console.log("getDenetimOnerilenBasdenetci::postData:: " + formURL + "?" + postData);
  $.ajax({
    url: formURL,
    type: 'GET',
    data: postData,
    success: function (html) {
      var dt_denetim_onerilen_karar_uye_table = $('.dt-denetim-onerilen-karar-uye');
      if (dt_denetim_onerilen_karar_uye_table.length) {
        // Setup - add a text input to each footer cell
        $('.dt-denetim-onerilen-karar-uye thead tr').clone(true).appendTo('.dt-denetim-onerilen-karar-uye thead');
        $('.dt-denetim-onerilen-karar-uye thead tr:eq(1) th').each(function (i) {
          var title = $(this).text();
          $(this).html('<input type="text" class="form-control" placeholder="' + title + '" />');

          $('input', this).on('keyup change', function () {
            if (dt_denetim_onerilen_karar_uye.column(i).search() !== this.value) {
              dt_denetim_onerilen_karar_uye.column(i).search(this.value).draw();
            }
          });
        });

        var dt_denetim_onerilen_karar_uye = dt_denetim_onerilen_karar_uye_table.DataTable({
          data: html,
          columns: [
            {data: 'id'},
            {data: 'id'},
            {data: 'denetci'},
            {data: 'ea'},
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
                return '<input type="checkbox" id="dt_denetim_onerilen_karar_uye_table' + full['id'] + '" value="' + chkvalue + '" onclick="setOnerilenKararUyeleri()" class="dt-checkboxes form-check-input">';
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
          retrieve: true,
          select: {
            // Select style
            style: 'multi'
          },
          autoWidth: false
        });
        dt_denetim_onerilen_karar_uye.columns.adjust().draw();

      }
      // $("#divDenetimOnerilenBasdenetci").html(html);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $('#formkaydetsonucerror').html('[getOnerilenKararUyeleri]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
      // window.console.log("formKaydet: " + formURL + "?" + postData);
      $('#myModalError').modal('show');
    }
  });
}

function setOnerilenKararUyeleri() {
  var uyeler = [];

  $('#dt-denetim-onerilen-karar-uye-body').find('tr').each(function () {
    var row = $(this);
    if (row.find('input[type="checkbox"]').is(':checked') &&
      row.find('td').val().length <= 0) {
      var uyetmp = row.find('td:eq(2)').text().trim();

      if (uyeler.length < 0) {
        uyeler.push(uyetmp);
      } else {
        if (uyeler.indexOf(uyetmp) === -1) {
          uyeler.push(uyetmp);
        }
      }
      uyeler.sort();
    }

  });

  $('#divbdkararu').val(uyeler);
}

function setBasdenetci(asm, val, act) {
  $('#div' + asm).val(val);

  var dataString = 'denetci=' + val + '&planno=' + $('#planno').val() + '&sistemler=' + $('#belgelendirileceksistemler').val();
  var formURL = $('#denetciSistemleriRoute').val();

  $.ajax({
    type: 'GET',
    url: formURL,
    data: dataString,
    cache: false,
    success: function (html) {
      densisyet[0] = html;
      // console.log("setBasdenetcisay1: " + densisyet);
      $('#denetcisay').val('1');
    }
  });
  console.log("act: " + act);
  denetcikontrol(act);

}

/**
 * setDenetci
 * @param {string} asm   - Hangi aşama? ("asama1tar", "asama2tar", "gozetim1", "gozetim2", "ybtar", "ozeltar")
 * @param {string} divid - Hangi tablo / hangi bölüm? (ör: "asama1-denetciler", "gozetim2-denetciler" vb.)
 *
 * Kullanım Örneği:
 * <button onclick="setDenetci('asama1tar', 'asama1-denetciler')">Denetçileri Seç</button>
 */
function setDenetci(asm, divid) {
  // 1) Hazırlık
  const uyeler = [];
  let denetcisay = 0;
  let g = 1; // densisyet dizisi için index

  // 2) İlgili tablo: #dt-<divid>-body
  const $tableBody = $('#dt-' + divid + '-body');

  // 3) Tablodaki seçili checkbox'ları tarayarak denetçi isimlerini diziye ekle
  $tableBody.find('tr').each(function () {
    const $row = $(this);
    const $checkbox = $row.find('input[type="checkbox"]');

    // a) Checkbox seçili mi?
    if ($checkbox.is(':checked')) {
      // b) row.find('td').val().length <= 0 kontrolü
      if ($row.find('td').val() && $row.find('td').val().length > 0) {
        return;
      }

      // c) Denetçi ismini al
      const uyetmp = $row.find('td:eq(2)').text().trim();
      if (!uyetmp) {
        return; // boş string ise eklemeyelim
      }

      // d) densisyet dizisini "g" indexinden itibaren temizle
      for (let h = 1; h < densisyet.length; h++) {
        densisyet.splice(h, densisyet.length);
      }

      // e) AJAX ile sunucuya gönder
      const dataString = 'denetci=' + encodeURIComponent(uyetmp)
        + '&planno=' + encodeURIComponent($('#planno').val())
        + '&sistemler=' + encodeURIComponent($('#belgelendirileceksistemler').val());
      const formURL = $('#denetciSistemleriRoute').val();

      $.ajax({
        type: 'GET',
        url: formURL,
        data: dataString,
        cache: false,
        success: function (html) {
          densisyet[g] = html;
          g++;
        },
        error: function (xhr, status, err) {
          console.warn("Denetçi sistemleri AJAX hata:", status, err);
        }
      });

      // f) "uyeler" dizisine ekle (unique ekleme)
      if (!uyeler.includes(uyetmp)) {
        uyeler.push(uyetmp);
        denetcisay++;
      }
    }
  });

  // 4) Tüm seçili denetçi isimlerini, ilgili "asm" parametresinin input'una yaz
  $('#div' + asm).val(uyeler.join(', '));

  // 5) Seçili denetçi sayısını kaydet
  $('#denetcisay').val(denetcisay.toString());

  // 6) Ek: hangi aşama (act) olduğunu bulmak için getActFromDivid kullan
  //    Bu sayede tablo ID'sinin prefix'inden ("asama1-", "gozetim2-", vb.)
  //    hangi denetcikontrol parametresine ("asama1tar", "gozetim2", vb.) gidileceğini belirliyoruz.
  const actParam = getActFromDivid(divid);
  if (actParam) {
    console.log('actParam: ', actParam);
    // denetcikontrol fonksiyonunuzu burada çağırın
    denetcikontrol(actParam);
  }
}


function getActFromDivid(divid) {
  // "asama1-denetciler" -> ['asama1','denetciler'][0] => 'asama1'
  const prefix = divid.split('-')[0];

  switch (prefix) {
    case 'asama1':
      return 'asama1tar';
    case 'asama2':
      return 'asama2tar';
    case 'gozetim1':
      return 'gozetim1tar';
    case 'gozetim2':
      return 'gozetim2tar';
    case 'yb':
      return 'ybtar';
    case 'ot':
      return 'ozeltar';
    default:
      // Prefix eşleşmediyse boş döndür,
      // veya uygun değilse uyarı döndürebilirsiniz.
      console.warn('Geçersiz prefix:', prefix);
      return '';
  }
}


function roundNearest5(x) {
  // return x;
  return (Math.round(parseFloat(x) * 2) / 2).toFixed(1);
}

/* ISO 9001 functions */
function iso9001SureHesapla() {
  var iso900115varyok = $('#iso900115varyok').val();
  if (iso900115varyok === '1') {
    var rg = $('#riskgrubu9').val();
    var calsay = $('#toplamcalisansayisi').val();

    var postData = 'rg=' + rg + '&calsay=' + calsay;
    var formURL = $('#iso9001SureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso9001SureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso900115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip9001').attr('title', result['tooltip']);
        $('#spantip9001').text(result['tooltip']);
        $('#iso9001hamsure').val(result['sonuc']);
        $('#iso9001kalansure').val(result['sonuc']);
        $('#iso9001a1sure').val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso9001a2sure').val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso9001gsure').val(parseFloat(result['gsure']).toFixed(1));
        $('#iso9001ybsure').val(parseFloat(result['ybsure']).toFixed(1));

        indartHesapla9001();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso9001SureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartHesapla9001() {
  var form9001 = document.getElementById('write9001IndArt-form');
  var oran = parseFloat(0);
  var ana = parseFloat($('#iso9001hamsure').val());
  var artimax = 30;
  var eksimax = -30;
  var totoran9001 = $('#totoran9001');

  for (var i = 0; i < form9001.length; i++) {
    if (form9001.elements[i].type === 'checkbox' && form9001.elements[i].name.substring(0, 14) === 'chb_indart9001') {
      if (form9001.elements[i].checked === true) {
        oran += parseFloat(form9001.elements[i].value);
      }
    }
  }

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  // console.log("9001 ind oran:::" + oran + ":::ana:::" + ana);
  if (oran > 0 && oran <= artimax) {
    totoran9001.removeClass('bg-primary');
    totoran9001.removeClass('bg-danger');
    totoran9001.addClass('bg-success');
    $('#indart9001varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran9001.removeClass('bg-primary');
    totoran9001.removeClass('bg-success');
    totoran9001.removeClass('bg-danger');
    $('#indart9001varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran9001.removeClass('bg-primary');
    totoran9001.removeClass('bg-danger');
    totoran9001.removeClass('bg-success');
    totoran9001.addClass('bg-success');
    $('#indart9001varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran9001.removeClass('bg-primary');
    totoran9001.removeClass('bg-success');
    totoran9001.removeClass('bg-danger');
    totoran9001.addClass('bg-danger');
    $('#indart9001varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran9001.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);

  kalansure = ana + miktar;
  if (miktar === 0) {
    // console.log("9001 ind oran:::" + oran + ":::miktar:::" + miktar);
    $('#iso9001indart').val(miktar.toFixed(1));
    $('#iso9001azartsure').val(ana.toFixed(1));
    $('#iso9001kalansure').val(ana.toFixed(1));
  } else {
    $('#iso9001indart').val(miktar.toFixed(1));
    $('#iso9001azartsure').val(kalansure.toFixed(1));
    $('#iso9001kalansure').val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso9001a1sure').val(a1sure);
    $('#iso9001a2sure').val(a2sure);
    $('#iso9001gsure').val(gsure);
    $('#iso9001ybsure').val(ybsure);
  }

  indartToplamHesapla();
}

function iso9001SahaSureHesapla(subeno) {
  var iso900115varyok = $('#iso900115varyok').val();
  if (iso900115varyok === '1') {
    var rg = $('#riskgrubu9').val();

    var calsay = $('#sube' + subeno + 'calsay').val();

    var postData = 'rg=' + rg + '&calsay=' + calsay;
    var formURL = $('#iso9001SureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso9001SahaSureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso900115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip9001' + subeno).attr('title', result['tooltip']);
        $('#spantip9001' + subeno).text(result['tooltip']);
        $('#iso9001hamsure' + subeno).val(result['sonuc']);
        $('#iso9001kalansure' + subeno).val(parseFloat(result['sonuc'])/2);
        $('#iso9001a1sure' + subeno).val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso9001a2sure' + subeno).val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso9001gsure' + subeno).val(parseFloat(result['gsure']).toFixed(1));
        $('#iso9001ybsure' + subeno).val(parseFloat(result['ybsure']).toFixed(1));

        //indartSahaHesapla9001(subeno);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso9001SahaSureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartSahaHesapla9001(subeno) {
  var form9001 = document.getElementById('write9001IndArt-form');
  var oran = parseFloat(0);
  var ana = parseFloat($('#iso9001hamsure' + subeno).val());
  var artimax = 30;
  var eksimax = -30;
  var totoran9001 = $('#totoran9001');

  for (var i = 0; i < form9001.length; i++) {
    if (form9001.elements[i].type === 'checkbox' && form9001.elements[i].name.substring(0, 14) === 'chb_indart9001') {
      if (form9001.elements[i].checked === true) {
        oran += parseFloat(form9001.elements[i].value);
      }
    }
  }

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  // console.log("9001 ind oran:::" + oran + ":::ana:::" + ana);
  if (oran > 0 && oran <= artimax) {
    totoran9001.removeClass('bg-primary');
    totoran9001.removeClass('bg-danger');
    totoran9001.addClass('bg-success');
    $('#indart9001varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran9001.removeClass('bg-primary');
    totoran9001.removeClass('bg-success');
    totoran9001.removeClass('bg-danger');
    $('#indart9001varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran9001.removeClass('bg-primary');
    totoran9001.removeClass('bg-danger');
    totoran9001.removeClass('bg-success');
    totoran9001.addClass('bg-success');
    $('#indart9001varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran9001.removeClass('bg-primary');
    totoran9001.removeClass('bg-success');
    totoran9001.removeClass('bg-danger');
    totoran9001.addClass('bg-danger');
    $('#indart9001varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran9001.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);

  kalansure = ana + miktar;
  if (miktar === 0) {
    // console.log("9001 ind oran:::" + oran + ":::miktar:::" + miktar);
    $('#iso9001indart' + subeno).val(miktar.toFixed(1));
    $('#iso9001azartsure' + subeno).val(ana.toFixed(1));
    $('#iso9001kalansure' + subeno).val(ana.toFixed(1));
  } else {
    $('#iso9001indart' + subeno).val(miktar.toFixed(1));
    $('#iso9001azartsure' + subeno).val(kalansure.toFixed(1));
    $('#iso9001kalansure' + subeno).val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso9001a1sure' + subeno).val(a1sure);
    $('#iso9001a2sure' + subeno).val(a2sure);
    $('#iso9001gsure' + subeno).val(gsure);
    $('#iso9001ybsure' + subeno).val(ybsure);
  }

  indartSahaToplamHesapla();
}

/* ISO 14001 functions */
function iso14001SureHesapla() {
  var iso1400115varyok = $('#iso1400115varyok').val();
  if (iso1400115varyok === '1') {
    var rg = $('#riskgrubu14').val();
    var calsay = $('#toplamcalisansayisi').val();

    var postData = 'rg=' + rg + '&calsay=' + calsay;
    var formURL = $('#iso14001SureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso14001SureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso1400115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip14001').attr('title', result['tooltip']);
        $('#spantip14001').text(result['tooltip']);
        $('#iso14001hamsure').val(result['sonuc']);
        $('#iso14001kalansure').val(result['sonuc']);
        $('#iso14001a1sure').val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso14001a2sure').val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso14001gsure').val(parseFloat(result['gsure']).toFixed(1));
        $('#iso14001ybsure').val(parseFloat(result['ybsure']).toFixed(1));

        indartHesapla14001();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso14001SureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartHesapla14001() {
  var form = document.getElementById('write14001IndArt-form');
  var oran = parseFloat(0);
  var ana = parseFloat($('#iso14001hamsure').val());
  var artimax = 30;
  var eksimax = -30;
  var totoran14001 = $('#totoran14001');

  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.substring(0, 15) === 'chb_indart14001') {
      if (form.elements[i].checked === true) {
        oran += parseFloat(form.elements[i].value);
      }
    }
  }

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  // console.log("14001 ind oran:::" + oran + ":::ana:::" + ana);
  if (oran > 0 && oran <= artimax) {
    totoran14001.removeClass('bg-primary');
    totoran14001.removeClass('bg-danger');
    totoran14001.addClass('bg-success');
    $('#indart14001varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran14001.removeClass('bg-primary');
    totoran14001.removeClass('bg-success');
    totoran14001.removeClass('bg-danger');
    $('#indart14001varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran14001.removeClass('bg-primary');
    totoran14001.removeClass('bg-danger');
    totoran14001.removeClass('bg-success');
    totoran14001.addClass('bg-success');
    $('#indart14001varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran14001.removeClass('bg-primary');
    totoran14001.removeClass('bg-success');
    totoran14001.removeClass('bg-danger');
    totoran14001.addClass('bg-danger');
    $('#indart14001varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran14001.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);

  kalansure = ana + miktar;
  if (miktar === 0) {
    // console.log("14001 ind oran:::" + oran + ":::miktar:::" + miktar);
    $('#iso14001indart').val(miktar.toFixed(1));
    $('#iso14001azartsure').val(ana.toFixed(1));
    $('#iso14001kalansure').val(ana.toFixed(1));
  } else {
    $('#iso14001indart').val(miktar.toFixed(1));
    $('#iso14001azartsure').val(kalansure.toFixed(1));
    $('#iso14001kalansure').val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso14001a1sure').val(a1sure);
    $('#iso14001a2sure').val(a2sure);
    $('#iso14001gsure').val(gsure);
    $('#iso14001ybsure').val(ybsure);
  }

  indartToplamHesapla();
}

function iso14001SahaSureHesapla(subeno) {
  var iso1400115varyok = $('#iso1400115varyok').val();
  if (iso1400115varyok === '1') {
    var rg = $('#riskgrubu14').val();

    var calsay = $('#sube' + subeno + 'calsay').val();

    var postData = 'rg=' + rg + '&calsay=' + calsay;
    var formURL = $('#iso14001SureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso14001SahaSureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso1400115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip14001' + subeno).attr('title', result['tooltip']);
        $('#spantip14001' + subeno).text(result['tooltip']);
        $('#iso14001hamsure' + subeno).val(result['sonuc']);
        $('#iso14001kalansure' + subeno).val(parseFloat(result['sonuc'])/2);
        $('#iso14001a1sure' + subeno).val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso14001a2sure' + subeno).val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso14001gsure' + subeno).val(parseFloat(result['gsure']).toFixed(1));
        $('#iso14001ybsure' + subeno).val(parseFloat(result['ybsure']).toFixed(1));

        //indartSahaHesapla14001(subeno);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso14001SahaSureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartSahaHesapla14001(subeno) {
  var form14001 = document.getElementById('write14001IndArt-form');
  var oran = parseFloat(0);
  var ana = parseFloat($('#iso14001hamsure' + subeno).val());
  var artimax = 30;
  var eksimax = -30;
  var totoran14001 = $('#totoran14001');

  for (var i = 0; i < form14001.length; i++) {
    if (form14001.elements[i].type === 'checkbox' && form14001.elements[i].name.substring(0, 15) === 'chb_indart14001') {
      if (form14001.elements[i].checked === true) {
        oran += parseFloat(form14001.elements[i].value);
      }
    }
  }

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  // console.log("14001 ind oran:::" + oran + ":::ana:::" + ana);
  if (oran > 0 && oran <= artimax) {
    totoran14001.removeClass('bg-primary');
    totoran14001.removeClass('bg-danger');
    totoran14001.addClass('bg-success');
    $('#indart14001varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran14001.removeClass('bg-primary');
    totoran14001.removeClass('bg-success');
    totoran14001.removeClass('bg-danger');
    $('#indart14001varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran14001.removeClass('bg-primary');
    totoran14001.removeClass('bg-danger');
    totoran14001.removeClass('bg-success');
    totoran14001.addClass('bg-success');
    $('#indart14001varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran14001.removeClass('bg-primary');
    totoran14001.removeClass('bg-success');
    totoran14001.removeClass('bg-danger');
    totoran14001.addClass('bg-danger');
    $('#indart14001varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran14001.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);

  kalansure = ana + miktar;
  if (miktar === 0) {
    // console.log("9001 ind oran:::" + oran + ":::miktar:::" + miktar);
    $('#iso14001indart' + subeno).val(miktar.toFixed(1));
    $('#iso14001azartsure' + subeno).val(ana.toFixed(1));
    $('#iso14001kalansure' + subeno).val(ana.toFixed(1));
  } else {
    $('#iso14001indart' + subeno).val(miktar.toFixed(1));
    $('#iso14001azartsure' + subeno).val(kalansure.toFixed(1));
    $('#iso14001kalansure' + subeno).val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso14001a1sure' + subeno).val(a1sure);
    $('#iso14001a2sure' + subeno).val(a2sure);
    $('#iso14001gsure' + subeno).val(gsure);
    $('#iso14001ybsure' + subeno).val(ybsure);
  }

  indartSahaToplamHesapla();
}

/* ISO 45001 functions */
function iso45001SureHesapla() {
  var iso4500118varyok = $('#iso4500118varyok').val();
  if (iso4500118varyok === '1') {
    var rg = $('#riskgrubu45').val();
    var calsay = $('#toplamcalisansayisi').val();

    var postData = 'rg=' + rg + '&calsay=' + calsay;
    var formURL = $('#iso45001SureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso14001SureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso1400115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip45001').attr('title', result['tooltip']);
        $('#spantip45001').text(result['tooltip']);
        $('#iso45001hamsure').val(result['sonuc']);
        $('#iso45001kalansure').val(result['sonuc']);
        $('#iso45001a1sure').val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso45001a2sure').val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso45001gsure').val(parseFloat(result['gsure']).toFixed(1));
        $('#iso45001ybsure').val(parseFloat(result['ybsure']).toFixed(1));

        indartHesapla45001();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso45001SureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartHesapla45001() {
  var form = document.getElementById('write45001IndArt-form');
  var oran = parseFloat(0);
  var ana = parseFloat($('#iso45001hamsure').val());
  var artimax = 30;
  var eksimax = -30;
  var totoran45001 = $('#totoran45001');

  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.substring(0, 15) === 'chb_indart45001') {
      if (form.elements[i].checked === true) {
        oran += parseFloat(form.elements[i].value);
      }
    }
  }

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  // console.log("45001 ind oran:::" + oran + ":::ana:::" + ana);
  if (oran > 0 && oran <= artimax) {
    totoran45001.removeClass('bg-primary');
    totoran45001.removeClass('bg-danger');
    totoran45001.addClass('bg-success');
    $('#indart45001varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran45001.removeClass('bg-primary');
    totoran45001.removeClass('bg-success');
    totoran45001.removeClass('bg-danger');
    $('#indart45001varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran45001.removeClass('bg-primary');
    totoran45001.removeClass('bg-danger');
    totoran45001.removeClass('bg-success');
    totoran45001.addClass('bg-success');
    $('#indart45001varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran45001.removeClass('bg-primary');
    totoran45001.removeClass('bg-success');
    totoran45001.removeClass('bg-danger');
    totoran45001.addClass('bg-danger');
    $('#indart45001varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran45001.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);
  // console.log("45001 ind oran:::" + oran + ":::ana:::" + ana + ":::miktar:::" + miktar);

  kalansure = ana + miktar;
  if (miktar === 0) {
    $('#iso45001indart').val(miktar.toFixed(1));
    $('#iso45001azartsure').val(ana.toFixed(1));
    $('#iso45001kalansure').val(ana.toFixed(1));
  } else {
    $('#iso45001indart').val(miktar.toFixed(1));
    $('#iso45001azartsure').val(kalansure.toFixed(1));
    $('#iso45001kalansure').val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso45001a1sure').val(a1sure);
    $('#iso45001a2sure').val(a2sure);
    $('#iso45001gsure').val(gsure);
    $('#iso45001ybsure').val(ybsure);
  }

  indartToplamHesapla();
}

function iso45001SahaSureHesapla(subeno) {
  var iso4500118varyok = $('#iso4500118varyok').val();
  if (iso4500118varyok === '1') {
    var rg = $('#riskgrubu45').val();

    var calsay = $('#sube' + subeno + 'calsay').val();

    var postData = 'rg=' + rg + '&calsay=' + calsay;
    var formURL = $('#iso45001SureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso14001SureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso1400115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip45001' + subeno).attr('title', result['tooltip']);
        $('#spantip45001' + subeno).text(result['tooltip']);
        $('#iso45001hamsure' + subeno).val(result['sonuc']);
        $('#iso45001kalansure' + subeno).val(parseFloat(result['sonuc'])/2);
        $('#iso45001a1sure' + subeno).val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso45001a2sure' + subeno).val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso45001gsure' + subeno).val(parseFloat(result['gsure']).toFixed(1));
        $('#iso45001ybsure' + subeno).val(parseFloat(result['ybsure']).toFixed(1));

        //indartSahaHesapla45001(subeno);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso45001SahaSureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartSahaHesapla45001(subeno) {
  var form = document.getElementById('write45001IndArt-form');
  var oran = parseFloat(0);
  var ana = parseFloat($('#iso45001hamsure' + subeno).val());
  var artimax = 30;
  var eksimax = -30;
  var totoran45001 = $('#totoran45001');

  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.substring(0, 15) === 'chb_indart45001') {
      if (form.elements[i].checked === true) {
        oran += parseFloat(form.elements[i].value);
      }
    }
  }

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  // console.log("45001 ind oran:::" + oran + ":::ana:::" + ana);
  if (oran > 0 && oran <= artimax) {
    totoran45001.removeClass('bg-primary');
    totoran45001.removeClass('bg-danger');
    totoran45001.addClass('bg-success');
    $('#indart45001varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran45001.removeClass('bg-primary');
    totoran45001.removeClass('bg-success');
    totoran45001.removeClass('bg-danger');
    $('#indart45001varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran45001.removeClass('bg-primary');
    totoran45001.removeClass('bg-danger');
    totoran45001.removeClass('bg-success');
    totoran45001.addClass('bg-success');
    $('#indart45001varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran45001.removeClass('bg-primary');
    totoran45001.removeClass('bg-success');
    totoran45001.removeClass('bg-danger');
    totoran45001.addClass('bg-danger');
    $('#indart45001varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran45001.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);
  // console.log("45001 ind oran:::" + oran + ":::ana:::" + ana + ":::miktar:::" + miktar);

  kalansure = ana + miktar;
  if (miktar === 0) {
    $('#iso45001indart' + subeno).val(miktar.toFixed(1));
    $('#iso45001azartsure' + subeno).val(ana.toFixed(1));
    $('#iso45001kalansure' + subeno).val(ana.toFixed(1));
  } else {
    $('#iso45001indart' + subeno).val(miktar.toFixed(1));
    $('#iso45001azartsure' + subeno).val(kalansure.toFixed(1));
    $('#iso45001kalansure' + subeno).val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso45001a1sure' + subeno).val(a1sure);
    $('#iso45001a2sure' + subeno).val(a2sure);
    $('#iso45001gsure' + subeno).val(gsure);
    $('#iso45001ybsure' + subeno).val(ybsure);
  }

  indartSahaToplamHesapla();
  // indartToplamHesapla();
}

/* ISO 50001 functions */
function iso50001SureHesapla() {
  var iso5000118varyok = $('#iso5000118varyok').val();
  if (iso5000118varyok === '1') {

    var yillikenerjituketimi = $('#yillikenerjituketimi').val();
    var enerjikaynaksayisi = $('#enerjikaynaksayisi').val();
    var oeksayisi = $('#oeksayisi').val();
    var rg = $('#riskgrubu50').val();
    var calsay = $('#enyseffectiveemployee').val();

    var postData = 'calsay=' + calsay + '&yet=' + yillikenerjituketimi.toString() + '&keks=' + enerjikaynaksayisi.toString() + '&oeks=' + oeksayisi.toString();

    var formURL = $('#iso50001SureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso14001SureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso1400115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip50001').attr('title', result['tooltip']);
        $('#spantip50001').text(result['tooltip']);
        $('#iso50001hamsure').val(result['sonuc']);
        $('#iso50001kalansure').val(result['sonuc']);
        $('#iso50001a1sure').val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso50001a2sure').val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso50001gsure').val(parseFloat(result['gsure']).toFixed(1));
        $('#iso50001ybsure').val(parseFloat(result['ybsure']).toFixed(1));

        indartHesapla50001();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso50001SureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartHesapla50001() {
  var form = document.getElementById('write50001IndArt-form');
  var oran = parseFloat(0);
  var ana = parseFloat($('#iso50001hamsure').val());
  var artimax = 30;
  var eksimax = -30;
  var totoran50001 = $('#totoran50001');

  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.substring(0, 15) === 'chb_indart50001') {
      if (form.elements[i].checked === true) {
        oran += parseFloat(form.elements[i].value);
      }
    }
  }

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  // console.log("50001 ind oran:::" + oran + ":::ana:::" + ana);
  if (oran > 0 && oran <= artimax) {
    totoran50001.removeClass('bg-primary');
    totoran50001.removeClass('bg-danger');
    totoran50001.addClass('bg-success');
    $('#indart50001varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran50001.removeClass('bg-primary');
    totoran50001.removeClass('bg-success');
    totoran50001.removeClass('bg-danger');
    $('#indart50001varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran50001.removeClass('bg-primary');
    totoran50001.removeClass('bg-danger');
    totoran50001.removeClass('bg-success');
    totoran50001.addClass('bg-success');
    $('#indart50001varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran50001.removeClass('bg-primary');
    totoran50001.removeClass('bg-success');
    totoran50001.removeClass('bg-danger');
    totoran50001.addClass('bg-danger');
    $('#indart50001varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran50001.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);
  // console.log("50001 ind oran:::" + oran + ":::miktar:::" + miktar);

  kalansure = ana + miktar;
  if (miktar === 0) {
    $('#iso50001indart').val(miktar.toFixed(1));
    $('#iso50001azartsure').val(ana.toFixed(1));
    $('#iso50001kalansure').val(ana.toFixed(1));
  } else {
    $('#iso50001indart').val(miktar.toFixed(1));
    $('#iso50001azartsure').val(kalansure.toFixed(1));
    $('#iso50001kalansure').val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso50001a1sure').val(a1sure);
    $('#iso50001a2sure').val(a2sure);
    $('#iso50001gsure').val(gsure);
    $('#iso50001ybsure').val(ybsure);
  }

  // var subesayisi = $('#inceleneceksahasayisi').val() //p
  // for (var i = 1; i <= subesayisi; i++) {
  //   indartSahaHesapla50001(i);
  // }
  indartToplamHesapla();
}

function iso50001SahaSureHesapla(subeno) {
  var iso5000118varyok = $('#iso5000118varyok').val();
  if (iso5000118varyok === '1') {

    var yillikenerjituketimi = $('#yillikenerjituketimi').val();
    var enerjikaynaksayisi = $('#enerjikaynaksayisi').val();
    var oeksayisi = $('#oeksayisi').val();

    var calsay = $('#sube' + subeno + 'calsay').val();

    var postData = 'calsay=' + calsay + '&yet=' + yillikenerjituketimi.toString() + '&keks=' + enerjikaynaksayisi.toString() + '&oeks=' + oeksayisi.toString();

    var formURL = $('#iso50001SureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso14001SureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso1400115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip50001' + subeno).attr('title', result['tooltip']);
        $('#spantip50001' + subeno).text(result['tooltip']);
        $('#iso50001hamsure' + subeno).val(result['sonuc']);
        $('#iso50001kalansure' + subeno).val(parseFloat(result['sonuc'])/2);
        $('#iso50001a1sure' + subeno).val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso50001a2sure' + subeno).val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso50001gsure' + subeno).val(parseFloat(result['gsure']).toFixed(1));
        $('#iso50001ybsure' + subeno).val(parseFloat(result['ybsure']).toFixed(1));

        //indartSahaHesapla50001(subeno);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso50001SahaSureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartSahaHesapla50001(subeno) {
  var form = document.getElementById('write50001IndArt-form');
  var oran = parseFloat(0);
  var ana = parseFloat($('#iso50001hamsure' + subeno).val());
  var artimax = 30;
  var eksimax = -30;
  var totoran50001 = $('#totoran50001');

  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.substring(0, 15) === 'chb_indart50001') {
      if (form.elements[i].checked === true) {
        oran += parseFloat(form.elements[i].value);
      }
    }
  }

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  // console.log("45001 ind oran:::" + oran + ":::ana:::" + ana);
  if (oran > 0 && oran <= artimax) {
    totoran50001.removeClass('bg-primary');
    totoran50001.removeClass('bg-danger');
    totoran50001.addClass('bg-success');
    $('#indart50001varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran50001.removeClass('bg-primary');
    totoran50001.removeClass('bg-success');
    totoran50001.removeClass('bg-danger');
    $('#indart50001varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran50001.removeClass('bg-primary');
    totoran50001.removeClass('bg-danger');
    totoran50001.removeClass('bg-success');
    totoran50001.addClass('bg-success');
    $('#indart50001varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran50001.removeClass('bg-primary');
    totoran50001.removeClass('bg-success');
    totoran50001.removeClass('bg-danger');
    totoran50001.addClass('bg-danger');
    $('#indart50001varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran50001.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);
  // console.log("45001 ind oran:::" + oran + ":::ana:::" + ana + ":::miktar:::" + miktar);

  kalansure = ana + miktar;
  if (miktar === 0) {
    $('#iso50001indart' + subeno).val(miktar.toFixed(1));
    $('#iso50001azartsure' + subeno).val(ana.toFixed(1));
    $('#iso50001kalansure' + subeno).val(ana.toFixed(1));
  } else {
    $('#iso50001indart' + subeno).val(miktar.toFixed(1));
    $('#iso50001azartsure' + subeno).val(kalansure.toFixed(1));
    $('#iso50001kalansure' + subeno).val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso50001a1sure' + subeno).val(a1sure);
    $('#iso50001a2sure' + subeno).val(a2sure);
    $('#iso50001gsure' + subeno).val(gsure);
    $('#iso50001ybsure' + subeno).val(ybsure);
  }

  indartSahaToplamHesapla();
  // indartToplamHesapla();
}

/* ISO 27001 functions */
function iso27001SureHesapla() {
  var iso27001varyok = $('#iso27001varyok').val();
  if (iso27001varyok === '1') {
    var calsay = $('#bgyseffectiveemployee').val();

    var postData = 'calsay=' + calsay;
    var formURL = $('#iso27001SureHesaplaRoute').val();

    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip27001').attr('title', result['tooltip']);
        $('#spantip27001').text(result['tooltip']);
        $('#iso27001hamsure').val(result['sonuc']);
        $('#iso27001kalansure').val(result['sonuc']);
        $('#iso27001a1sure').val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso27001a2sure').val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso27001gsure').val(parseFloat(result['gsure']).toFixed(1));
        $('#iso27001ybsure').val(parseFloat(result['ybsure']).toFixed(1));

        // $('#modal27001indart').modal('show');

        // indartHesapla27001();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso27001SureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function isFaktorEtkiHesapla() {
  let $inp1 = $('#isturu1').is(':checked') ? $('#isturu1').val() : $('#isturu2').is(':checked') ? $('#isturu2').val() : $('#isturu3').is(':checked') ? $('#isturu3').val() : 0;
  let $inp2 = $('#prosesler1').is(':checked') ? $('#prosesler1').val() : $('#prosesler2').is(':checked') ? $('#prosesler2').val() : $('#prosesler3').is(':checked') ? $('#prosesler3').val() : 0;
  let $inp3 = $('#ysolusmaseviyesi1').is(':checked') ? $('#ysolusmaseviyesi1').val() : $('#ysolusmaseviyesi2').is(':checked') ? $('#ysolusmaseviyesi2').val() : $('#ysolusmaseviyesi3').is(':checked') ? $('#ysolusmaseviyesi3').val() : 0;
  isFaktor = parseInt($inp1) + parseInt($inp2) + parseInt($inp3);
  $('#iskartoplam').html(isFaktor);
  // console.log("isFaktorEtkiHesapla::" + isFaktor);
}

function btFaktorEtkiHesapla() {
  let $inp1 = $('#btaltyapi1').is(':checked') ? $('#btaltyapi1').val() : $('#btaltyapi2').is(':checked') ? $('#btaltyapi2').val() : $('#btaltyapi3').is(':checked') ? $('#btaltyapi3').val() : 0;
  let $inp2 = $('#diskaynak1').is(':checked') ? $('#diskaynak1').val() : $('#diskaynak2').is(':checked') ? $('#diskaynak2').val() : $('#diskaynak3').is(':checked') ? $('#diskaynak3').val() : 0;
  let $inp3 = $('#bilgisistemgelisimi1').is(':checked') ? $('#bilgisistemgelisimi1').val() : $('#bilgisistemgelisimi2').is(':checked') ? $('#bilgisistemgelisimi2').val() : $('#bilgisistemgelisimi3').is(':checked') ? $('#bilgisistemgelisimi3').val() : 0;
  btFaktor = parseInt($inp1) + parseInt($inp2) + parseInt($inp3);
  $('#btkartoplam').html(btFaktor);
  // console.log("btFaktorEtkiHesapla::" + btFaktor);
}

function bgysFaktorEtkiHesapla() {
  var postData = 'isFaktor=' + isFaktor + '&btFaktor=' + btFaktor;
  var formURL = $('#bgysFaktorDenetimEtkisiRoute').val();
  // console.log(postData);
  $.ajax({
    url: formURL,
    type: 'GET',
    data: postData,
    success: function (html) {
      if (html === -100 || html === 'err..') {
        alert('bgysFaktorEtkiHesapla::indirim/arttırım yüzdeliği lınamadı...');
        return false;
      } else {
        $('#totoran27001').val(html);
        // console.log("totoran27001:::::" + html);

        indartHesapla27001();
        // var subesayisi = $('#inceleneceksahasayisi').val();
        // for (var i = 1; i <= subesayisi; i++) {
        //   indartSahaHesapla27001(i);
        // }
      }
    },
    failure: function () {
      console.log('bgysFaktorEtkiHesapla::hata oldu hesaplanamadı...');
    }
  });
}

function indartHesapla27001() {
  var totoran27001 = $('#totoran27001');
  var oran = parseFloat(totoran27001.val().replace('%+-', ''));
  var ana = parseFloat($('#iso27001hamsure').val());
  var artimax = 100;
  var eksimax = -30;

  oran = parseFloat(oran);
  // console.log("27001 ind oran:::" + oran + ":::ana:::" + ana);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  if (oran > 0 && oran <= artimax) {
    totoran27001.removeClass('bg-primary');
    totoran27001.removeClass('bg-danger');
    totoran27001.addClass('bg-success');
    $('#indart27001varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran27001.removeClass('bg-primary');
    totoran27001.removeClass('bg-success');
    totoran27001.removeClass('bg-danger');
    $('#indart27001varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran27001.removeClass('bg-primary');
    totoran27001.removeClass('bg-danger');
    totoran27001.removeClass('bg-success');
    totoran27001.addClass('bg-success');
    $('#indart27001varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran27001.removeClass('bg-primary');
    totoran27001.removeClass('bg-success');
    totoran27001.removeClass('bg-danger');
    totoran27001.addClass('bg-danger');
    $('#indart27001varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran27001.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);
  // console.log("indartHesapla27001 ind oran:::" + oran + ":::ana:::" + ana + ":::miktar:::" + miktar);

  kalansure = ana + miktar;
  if (miktar === 0) {
    $('#iso27001indart').val(miktar.toFixed(1));
    $('#iso27001azartsure').val(ana.toFixed(1));
    $('#iso27001kalansure').val(ana.toFixed(1));
  } else {
    $('#iso27001indart').val(miktar.toFixed(1));
    $('#iso27001azartsure').val(kalansure.toFixed(1));
    $('#iso27001kalansure').val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso27001a1sure').val(a1sure);
    $('#iso27001a2sure').val(a2sure);
    $('#iso27001gsure').val(gsure);
    $('#iso27001ybsure').val(ybsure);
  }

  toplamHesapla();
}

function iso27001SahaSureHesapla(subeno) {
  var iso27001varyok = $('#iso27001varyok').val();
  if (iso27001varyok === '1') {

    var calsay = $('#sube' + subeno + 'calsay').val();

    var postData = 'calsay=' + calsay;
    var formURL = $('#iso27001SureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso14001SahaSureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso1400115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip27001' + subeno).attr('title', result['tooltip']);
        $('#spantip27001' + subeno).text(result['tooltip']);
        $('#iso27001hamsure' + subeno).val(result['sonuc']);
        $('#iso27001kalansure' + subeno).val(parseFloat(result['sonuc'])/2);
        $('#iso27001a1sure' + subeno).val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso27001a2sure' + subeno).val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso27001gsure' + subeno).val(parseFloat(result['gsure']).toFixed(1));
        $('#iso27001ybsure' + subeno).val(parseFloat(result['ybsure']).toFixed(1));

        //indartSahaHesapla27001(subeno);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso27001SahaSureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartSahaHesapla27001(subeno) {
  var totoran27001 = $('#totoran27001');
  var oran = parseFloat(totoran27001.val().replace('%', ''));
  var ana = parseFloat($('#iso27001hamsure' + subeno).val());
  var artimax = 100;
  var eksimax = -30;
  // console.log("27001 saha ind oran:::" + oran + ":::ana:::" + ana);

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  if (oran > 0 && oran <= artimax) {
    totoran27001.removeClass('bg-primary');
    totoran27001.removeClass('bg-danger');
    totoran27001.addClass('bg-success');
    $('#indart27001varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran27001.removeClass('bg-primary');
    totoran27001.removeClass('bg-success');
    totoran27001.removeClass('bg-danger');
    $('#indart27001varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran27001.removeClass('bg-primary');
    totoran27001.removeClass('bg-danger');
    totoran27001.removeClass('bg-success');
    totoran27001.addClass('bg-success');
    $('#indart27001varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran27001.removeClass('bg-primary');
    totoran27001.removeClass('bg-success');
    totoran27001.removeClass('bg-danger');
    totoran27001.addClass('bg-danger');
    $('#indart27001varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran27001.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);
  // console.log("indartHesapla27001 ind oran:::" + oran + ":::ana:::" + ana + ":::miktar:::" + miktar);

  kalansure = ana + miktar;
  if (miktar === 0) {
    $('#iso27001indart' + subeno).val(miktar.toFixed(1));
    $('#iso27001azartsure' + subeno).val(ana.toFixed(1));
    $('#iso27001kalansure' + subeno).val(ana.toFixed(1));
  } else {
    $('#iso27001indart' + subeno).val(miktar.toFixed(1));
    $('#iso27001azartsure' + subeno).val(kalansure.toFixed(1));
    $('#iso27001kalansure' + subeno).val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso27001a1sure' + subeno).val(a1sure);
    $('#iso27001a2sure' + subeno).val(a2sure);
    $('#iso27001gsure' + subeno).val(gsure);
    $('#iso27001ybsure' + subeno).val(ybsure);
  }

  toplamHesapla();
}

/* ISO 22000 functions */
function iso22000SureHesapla() {
  var iso2200018varyok = $('#iso2200018varyok').val();
  if (iso2200018varyok === '1') {
    var calsay = $('#toplamcalisansayisi').val();
    var cat = $('#gizlikat').val();
    var bb = $('#gizlikatbb').val();
    var cc = $('#gizlikatcc').val();
    var haccpsayisi = $('#haccpcalismasisayisi').val();
    // var mysvarmi = ($('#chb_mysvarmi').is(':checked') === true) ? 'EVET' : 'HAYIR'; //$("#chb_mysvarmi").val();
    var sahasayisi = $('#sahasayisi22').val();

    var postData = 'calsay=' + calsay + '&cat=' + cat + '&bb=' + bb + '&cc=' + cc + '&haccpsayisi=' + haccpsayisi + '&sahasayisi=' + sahasayisi;

    var formURL = $('#iso22000SureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso14001SureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso1400115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltip22000').attr('title', result['tooltip']);
        $('#spantip22000').text(result['tooltip']);
        $('#iso22000hamsure').val(result['sonuc']);
        $('#iso22000kalansure').val(result['sonuc']);
        $('#iso22000a1sure').val(parseFloat(result['a1sure']).toFixed(1));
        $('#iso22000a2sure').val(parseFloat(result['a2sure']).toFixed(1));
        $('#iso22000gsure').val(parseFloat(result['gsure']).toFixed(1));
        $('#iso22000ybsure').val(parseFloat(result['ybsure']).toFixed(1));

        indartHesapla22000();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[iso22000SureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartHesapla22000() {
  var form = document.getElementById('write22000IndArt-form');
  var oran = parseFloat(0);
  var ana = parseFloat($('#iso22000hamsure').val());
  var artimax = 30;
  var eksimax = -30;
  var totoran22000 = $('#totoran22000');

  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.substring(0, 15) === 'chb_indart22000') {
      if (form.elements[i].checked === true) {
        oran += parseFloat(form.elements[i].value);
      }
    }
  }

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  // console.log("22000 ind oran:::" + oran + ":::ana:::" + ana);
  if (oran > 0 && oran <= artimax) {
    totoran22000.removeClass('bg-primary');
    totoran22000.removeClass('bg-danger');
    totoran22000.addClass('bg-success');
    $('#indart22000varmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoran22000.removeClass('bg-primary');
    totoran22000.removeClass('bg-success');
    totoran22000.removeClass('bg-danger');
    $('#indart22000varmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoran22000.removeClass('bg-primary');
    totoran22000.removeClass('bg-danger');
    totoran22000.removeClass('bg-success');
    totoran22000.addClass('bg-success');
    $('#indart22000varmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoran22000.removeClass('bg-primary');
    totoran22000.removeClass('bg-success');
    totoran22000.removeClass('bg-danger');
    totoran22000.addClass('bg-danger');
    $('#indart22000varmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoran22000.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);
  // console.log("22000 ind oran:::" + oran + ":::miktar:::" + miktar);

  kalansure = ana + miktar;
  if (miktar === 0) {
    $('#iso22000indart').val(miktar.toFixed(1));
    $('#iso22000azartsure').val(ana.toFixed(1));
    $('#iso22000kalansure').val(ana.toFixed(1));
  } else {
    $('#iso22000indart').val(miktar.toFixed(1));
    $('#iso22000azartsure').val(kalansure.toFixed(1));
    $('#iso22000kalansure').val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#iso22000a1sure').val(a1sure);
    $('#iso22000a2sure').val(a2sure);
    $('#iso22000gsure').val(gsure);
    $('#iso22000ybsure').val(ybsure);
  }

  indartToplamHesapla();
}

/* ISO oicsmiic functions */
function isoOicSmiicSureHesapla() {
  var helalvaryok = $('#helalvaryok').val();
  var oicsmiik6varyok = $('#oicsmiik6varyok').val();
  var oicsmiik9varyok = $('#oicsmiik9varyok').val();
  var oicsmiik171varyok = $('#oicsmiik171varyok').val();
  var oicsmiik23varyok = $('#oicsmiik23varyok').val();
  var oicsmiik24varyok = $('#oicsmiik24varyok').val();
  if (helalvaryok === '1' || oicsmiik6varyok === '1' || oicsmiik9varyok === '1' || oicsmiik171varyok === '1' || oicsmiik23varyok === '1' || oicsmiik24varyok === '1') {
    var calsay = $('#toplamcalisansayisi').val();
    var cat = $('#gizlioickat').val();
    var bb = $('#gizlikatbb').val();
    var cc = $('#gizlikatcc').val();
    var haccpsayisi = parseInt($('#haccpcalismasisayisismiic').val());
    var cck = $('#oicsmiickk').val();
    var pv = $('#helalurunsayisi').val();
    var alansayisi = $('#oic_sahasayisi22').val();
    var havuzsayisi = $('#havuzsayisi').val();
    var mutfaksayisi = $('#mutfaksayisi').val();
    var odasayisi = $('#odasayisi').val();
    var hizmetkategorisi = $('#hizmetkategorisi').val();
    var aracsayisi = $('#aracsayisi').val();

    var postData = 'calsay=' + calsay + '&cat=' + cat + '&bb=' + bb + '&cc=' + cc + '&haccpsayisi=' + haccpsayisi + '&cck=' + cck + '&pv=' + pv + '&sahasayisi=' + alansayisi + '&havuzsayisi=' + havuzsayisi + '&mutfaksayisi=' + mutfaksayisi + '&odasayisi=' + odasayisi + '&hizmetkategorisi=' + hizmetkategorisi + '&aracsayisi=' + aracsayisi;

    var formURL = $('#isoOicSmiicSureHesaplaRoute').val();

    // console.log("formKaydet::route:: ");
    // console.log("iso14001SureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso1400115varyok);
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        var result = $.parseJSON(html);
        $('#tooltipoicsmiic').attr('title', result['tooltip']);
        $('#spantipoicsmiic').text(result['tooltip']);
        $('#oicsmiichamsure').val(result['sonuc']);
        $('#oicsmiickalansure').val(result['sonuc']);
        $('#oicsmiica1sure').val(parseFloat(result['a1sure']).toFixed(1));
        $('#oicsmiica2sure').val(parseFloat(result['a2sure']).toFixed(1));
        $('#oicsmiicgsure').val(parseFloat(result['gsure']).toFixed(1));
        $('#oicsmiicybsure').val(parseFloat(result['ybsure']).toFixed(1));

        indartHesaplaOicsmiic();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#formkaydetsonucerror').html('[isoOicSmiicSureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
        // window.console.log("formKaydet: " + formURL + "?" + postData);
        $('#myModalError').modal('show');
      }
    });

  }
}

function indartHesaplaOicsmiic() {
  var form = document.getElementById('writeSmiicIndArt-form');
  var oran = parseFloat(0);
  var ana = parseFloat($('#oicsmiichamsure').val());
  var artimax = 30;
  var eksimax = -30;
  var totoranoicsmiic = $('#totoranoicsmiic');

  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.substring(0, 15) === 'chb_indartsmiic') {
      if (form.elements[i].checked === true) {
        oran += parseFloat(form.elements[i].value);
      }
    }
  }

  oran = parseFloat(oran);
  if (oran <= eksimax) {
    oran = eksimax;
  }
  if (oran >= artimax) {
    oran = artimax;
  }
  // console.log("oicsmiic ind oran:::" + oran + ":::ana:::" + ana);
  if (oran > 0 && oran <= artimax) {
    totoranoicsmiic.removeClass('bg-primary');
    totoranoicsmiic.removeClass('bg-danger');
    totoranoicsmiic.addClass('bg-success');
    $('#indartoicsmiicvarmi').val('1');
    // console.log("1:::" + eksimax);
  }
  if (oran === 0) {
    totoranoicsmiic.removeClass('bg-primary');
    totoranoicsmiic.removeClass('bg-success');
    totoranoicsmiic.removeClass('bg-danger');
    $('#indartoicsmiicvarmi').val('0');
    // console.log("2:::" + eksimax);
  }

  if (oran < 0 && oran >= eksimax) {
    totoranoicsmiic.removeClass('bg-primary');
    totoranoicsmiic.removeClass('bg-danger');
    totoranoicsmiic.removeClass('bg-success');
    totoranoicsmiic.addClass('bg-success');
    $('#indartoicsmiicvarmi').val('1');
    // console.log("3:::" + eksimax);
  }
  if (oran > artimax || oran < eksimax) {
    totoranoicsmiic.removeClass('bg-primary');
    totoranoicsmiic.removeClass('bg-success');
    totoranoicsmiic.removeClass('bg-danger');
    totoranoicsmiic.addClass('bg-danger');
    $('#indartoicsmiicvarmi').val('1');

    oran = (oran < 0) ? oran = eksimax : oran = artimax;
    // console.log("4:::" + eksimax);
  }

  totoranoicsmiic.val('%' + oran.toString());

  var miktar = parseFloat((ana * oran / 100).toFixed(1));
  var kalansure = parseFloat(0);
  // console.log("oicsmiic ind oran:::" + oran + ":::miktar:::" + miktar);

  kalansure = ana + miktar;
  if (miktar === 0) {
    $('#oicsmiicindart').val(miktar.toFixed(1));
    $('#oicsmiicazartsure').val(ana.toFixed(1));
    $('#oicsmiickalansure').val(ana.toFixed(1));
  } else {
    $('#oicsmiicindart').val(miktar.toFixed(1));
    $('#oicsmiicazartsure').val(kalansure.toFixed(1));
    $('#oicsmiickalansure').val(kalansure.toFixed(1));
  }

  if (kalansure > 0) {
    var a1sure = parseFloat(kalansure * 30 / 100).toFixed(1);
    var a2sure = parseFloat(kalansure * 70 / 100).toFixed(1);
    var gsure = parseFloat(kalansure / 3).toFixed(1);
    var ybsure = parseFloat(kalansure * 2 / 3).toFixed(1);

    $('#oicsmiica1sure').val(a1sure);
    $('#oicsmiica2sure').val(a2sure);
    $('#oicsmiicgsure').val(gsure);
    $('#oicsmiicybsure').val(ybsure);
  }

  indartToplamHesapla();
}

function indartHesaplaEntegreYenile() {
  indartHesaplaEntegre();
  indartHesaplaEntegre();

  // var subesayisi = $('#inceleneceksahasayisi').val();
  // for (var i = 1; i <= subesayisi; i++){
  //   indartHesaplaEntegreSaha(i);
  // }

}

function indartHesaplaEntegre() {
  var iso9001 = $('#iso900115varyok').val() === '1',
    iso14001 = $('#iso1400115varyok').val() === '1',
    iso22000 = $('#iso2200018varyok').val() === '1',
    iso45001 = $('#iso4500118varyok').val() === '1',
    iso50001 = $('#iso5000118varyok').val() === '1',
    iso27001 = $('#iso27001varyok').val() === '1',
    oicsmiik = $('#helalvaryok').val() === '1',
    oicsmiik6 = $('#oicsmiik6varyok').val() === '1',
    oicsmiik9 = $('#oicsmiik9varyok').val() === '1',
    oicsmiik171 = $('#oicsmiik171varyok').val() === '1',
    oicsmiik23 = $('#oicsmiik23varyok').val() === '1',
    oicsmiik24 = $('#oicsmiik24varyok').val() === '1',
    isOicsmiic = (oicsmiik || oicsmiik6 || oicsmiik9 || oicsmiik171 || oicsmiik23 || oicsmiik24) ?? false;
  var entindvarmi = $('#indartentvarmi').val() === '1';
  var sistemsay = parseFloat(0);
  var totoranEntegre = $('#totoranEntegre');

  var ana9001 = (iso9001) ? parseFloat($('#iso9001azartsure').val()) : parseFloat(0);
  var ana14001 = (iso14001) ? parseFloat($('#iso14001azartsure').val()) : parseFloat(0);
  var ana45001 = (iso45001) ? parseFloat($('#iso45001azartsure').val()) : parseFloat(0);
  var ana50001 = (iso50001) ? parseFloat($('#iso50001azartsure').val()) : parseFloat(0);
  var ana27001 = (iso27001) ? parseFloat($('#iso27001azartsure').val()) : parseFloat(0);
  var ana22000 = (iso22000) ? parseFloat($('#iso22000azartsure').val()) : parseFloat(0);
  var anaOicsmiic = (oicsmiik || oicsmiik6 || oicsmiik9 || oicsmiik171 || oicsmiik23 || oicsmiik24) ? parseFloat($('#oicsmiicazartsure').val()) : parseFloat(0);

  if (iso9001) {
    sistemsay++;
  }
  if (iso14001) {
    sistemsay++;
  }
  if (iso45001) {
    sistemsay++;
  }
  if (iso50001) {
    sistemsay++;
  }
  if (iso27001) {
    sistemsay++;
  }
  if (iso22000) {
    sistemsay++;
  }
  if (isOicsmiic) {
    sistemsay++;
  }
  var formplan = document.getElementById('writeEntegreIndArt-form');
  var xekseni = parseFloat(0);
  var yekseni = parseFloat(0);
  var ana = parseFloat($('#toplamazart').val());
  var denetcisay = $('#denetcisay').val();

  var i;
  for (i = 0; i < formplan.length; i++) {
    if (formplan.elements[i].type === 'checkbox' && formplan.elements[i].name.substring(0, 17) === 'chb_indartentegre') {
      if (formplan.elements[i].checked === true) {
        yekseni += parseFloat(formplan.elements[i].value);
      }
    }
  }

  var pay = 0;
  var payda = 0;
  for (var j = 0; j < densisyet.length; j++) {
    var say1 = densisyet[j];
    // console.log('say1: ' + say1);

    pay += say1 - 1;
  }
  pay = pay * 100;
  payda = denetcisay * (sistemsay - 1);
  xekseni = parseFloat(pay / payda).toFixed(0);

  // console.log("pay: " + pay);
  // console.log("payda: " + payda);
  // console.log("Kabiliyeti(x): " + xekseni);
  // console.log("entegre duzeyi(y): " + yekseni);

  getEntegreOran(purifyEksen(xekseni), purifyEksen(yekseni));

  totoranEntegre.val('%' + duzey.toString());

  // console.log("ana: " + ana);
  // console.log("xekseni: " + purifyEksen(xekseni));
  // console.log("yekseni: " + purifyEksen(yekseni));
  // console.log("entegre duzeyi: " + duzey);

  if (duzey >= 0 && duzey <= 20) {
    totoranEntegre.removeClass('bg-primary');
    totoranEntegre.removeClass('bg-danger');
    totoranEntegre.addClass('bg-success');
    $('#indartentvarmi').val('1');
  }
  if (duzey === 0) {
    totoranEntegre.removeClass('bg-success');
    totoranEntegre.removeClass('bg-danger');
    totoranEntegre.addClass('bg-primary');
    $('#indartentvarmi').val('0');
  }

  var miktar = (-1) * parseFloat((ana * duzey / 100).toFixed(1));
  var miktar9001 = parseFloat((ana9001 * duzey / 100).toFixed(1));
  var miktar14001 = parseFloat((ana14001 * duzey / 100).toFixed(1));
  var miktar45001 = parseFloat((ana45001 * duzey / 100).toFixed(1));
  var miktar50001 = parseFloat((ana50001 * duzey / 100).toFixed(1));
  var miktar27001 = parseFloat((ana27001 * duzey / 100).toFixed(1));
  var miktar22000 = parseFloat((ana22000 * duzey / 100).toFixed(1));
  var miktarOicsmiic = parseFloat((anaOicsmiic * duzey / 100).toFixed(1));
  var kalansure = parseFloat(0);
  $('#denetimentegreindirim').val(miktar.toFixed(1));

  // console.log("ana9001: " + ana900115);
  // console.log("ana14001: " + ana1400115);
  //  console.log("miktar22000: " + miktar22000);
  //  console.log("miktar50001: " + miktar50001);
  // console.log("kalansure: " + kalansure);
  // console.log("miktar: " + miktar);
  kalansure = ana + miktar;
  // console.log("kalansure: " + kalansure);
  if (miktar === 0) {
    $('#toplamkalansure').val(ana.toFixed(1));
    $('#iso9001entindart').val(ana9001.toFixed(1));
    $('#iso14001entindart').val(ana14001.toFixed(1));
    $('#iso45001entindart').val(ana45001.toFixed(1));
    $('#iso50001entindart').val(ana50001.toFixed(1));
    $('#iso27001entindart').val(ana27001.toFixed(1));
    $('#iso22000entindart').val(ana22000.toFixed(1));
    $('#oicsmiicentindart').val(anaOicsmiic.toFixed(1));

    $('#iso9001kalansure').val(ana9001.toFixed(1));
    $('#iso14001kalansure').val(ana14001.toFixed(1));
    $('#iso45001kalansure').val(ana45001.toFixed(1));
    $('#iso50001kalansure').val(ana50001.toFixed(1));
    $('#iso27001kalansure').val(ana27001.toFixed(1));
    $('#iso22000kalansure').val(ana22000.toFixed(1));
    $('#oicsmiickalansure').val(anaOicsmiic.toFixed(1));
  } else {
    $('#toplamkalansure').val(kalansure.toFixed(1));
    $('#iso9001entindart').val((miktar9001).toFixed(1));
    $('#iso14001entindart').val((miktar14001).toFixed(1));
    $('#iso45001entindart').val((miktar45001).toFixed(1));
    $('#iso50001entindart').val((miktar50001).toFixed(1));
    $('#iso27001entindart').val((miktar27001).toFixed(1));
    $('#iso22000entindart').val((miktar22000).toFixed(1));
    $('#oicsmiicentindart').val((miktarOicsmiic).toFixed(1));

    $('#iso9001kalansure').val((ana9001 - miktar9001).toFixed(1));
    $('#iso14001kalansure').val((ana14001 - miktar14001).toFixed(1));
    $('#iso45001kalansure').val((ana45001 - miktar45001).toFixed(1));
    $('#iso50001kalansure').val((ana50001 - miktar50001).toFixed(1));
    $('#iso27001kalansure').val((ana27001 - miktar27001).toFixed(1));
    $('#iso22000kalansure').val((ana22000 - miktar22000).toFixed(1));
    $('#oicsmiickalansure').val((anaOicsmiic - miktarOicsmiic).toFixed(1));
  }


  // var subesayisi = $('#inceleneceksahasayisi').val();
  // for (var i = 1; i <= subesayisi; i++){
  //   indartHesaplaEntegreSaha(i);
  // }

  toplamHesapla();
  // indirArttirSebepler();
}

function indartHesaplaEntegreSaha(sube) {
  var iso9001 = $('#iso900115varyok').val() === '1',
    iso14001 = $('#iso1400115varyok').val() === '1',
    iso22000 = $('#iso2200018varyok').val() === '1',
    iso45001 = $('#iso4500118varyok').val() === '1',
    iso50001 = $('#iso5000118varyok').val() === '1',
    iso27001 = $('#iso27001varyok').val() === '1',
    oicsmiik = $('#helalvaryok').val() === '1',
    oicsmiik6 = $('#oicsmiik6varyok').val() === '1',
    oicsmiik9 = $('#oicsmiik9varyok').val() === '1',
    oicsmiik171 = $('#oicsmiik171varyok').val() === '1',
    oicsmiik23 = $('#oicsmiik23varyok').val() === '1',
    oicsmiik24 = $('#oicsmiik24varyok').val() === '1',
    isOicsmiic = (oicsmiik || oicsmiik6 || oicsmiik9 || oicsmiik171 || oicsmiik23 || oicsmiik24) ?? false;
  var entindvarmi = $('#indartentvarmi').val() === '1';
  var sistemsay = parseFloat(0);
  var totoranEntegre = $('#totoranEntegre');

  var ana9001 = (iso9001) ? parseFloat($('#iso9001azartsure' + sube).val()) : parseFloat(0);
  var ana14001 = (iso14001) ? parseFloat($('#iso14001azartsure' + sube).val()) : parseFloat(0);
  var ana45001 = (iso45001) ? parseFloat($('#iso45001azartsure' + sube).val()) : parseFloat(0);
  var ana50001 = (iso50001) ? parseFloat($('#iso50001azartsure' + sube).val()) : parseFloat(0);
  var ana27001 = (iso27001) ? parseFloat($('#iso27001azartsure' + sube).val()) : parseFloat(0);
  var ana22000 = (iso22000) ? parseFloat($('#iso22000azartsure' + sube).val()) : parseFloat(0);
  var anaOicsmiic = (oicsmiik || oicsmiik6 || oicsmiik9 || oicsmiik171 || oicsmiik23 || oicsmiik24) ? parseFloat($('#oicsmiicazartsure' + sube).val()) : parseFloat(0);

  if (iso9001) {
    sistemsay++;
  }
  if (iso14001) {
    sistemsay++;
  }
  if (iso45001) {
    sistemsay++;
  }
  if (iso50001) {
    sistemsay++;
  }
  if (iso27001) {
    sistemsay++;
  }
  if (iso22000) {
    sistemsay++;
  }
  if (isOicsmiic) {
    sistemsay++;
  }
  var formplan = document.getElementById('writeEntegreIndArt-form');
  var xekseni = parseFloat(0);
  var yekseni = parseFloat(0);
  var ana = parseFloat($('#toplamazart').val());
  var denetcisay = $('#denetcisay').val();

  var i;
  for (i = 0; i < formplan.length; i++) {
    if (formplan.elements[i].type === 'checkbox' && formplan.elements[i].name.substring(0, 17) === 'chb_indartentegre') {
      if (formplan.elements[i].checked === true) {
        yekseni += parseFloat(formplan.elements[i].value);
      }
    }
  }

  var pay = 0;
  var payda = 0;
  for (var j = 0; j < densisyet.length; j++) {
    var say1 = densisyet[j];
    // console.log('say1: ' + say1);

    pay += say1 - 1;
  }
  pay = pay * 100;
  payda = denetcisay * (sistemsay - 1);
  xekseni = parseFloat(pay / payda).toFixed(0);

  // console.log("pay: " + pay);
  // console.log("payda: " + payda);
  // console.log("Kabiliyeti(x): " + xekseni);
  // console.log("entegre duzeyi(y): " + yekseni);

  getEntegreOran(purifyEksen(xekseni), purifyEksen(yekseni));

  totoranEntegre.val('%' + duzey.toString());

  // console.log("ana: " + ana);
  // console.log("xekseni: " + purifyEksen(xekseni));
  // console.log("yekseni: " + purifyEksen(yekseni));
  // console.log("entegre duzeyi: " + duzey);

  if (duzey >= 0 && duzey <= 20) {
    totoranEntegre.removeClass('bg-primary');
    totoranEntegre.removeClass('bg-danger');
    totoranEntegre.addClass('bg-success');
    $('#indartentvarmi').val('1');
  }
  if (duzey === 0) {
    totoranEntegre.removeClass('bg-success');
    totoranEntegre.removeClass('bg-danger');
    totoranEntegre.addClass('bg-primary');
    $('#indartentvarmi').val('0');
  }

  var miktar = (-1) * parseFloat((ana * duzey / 100).toFixed(1));
  var miktar9001 = parseFloat((ana9001 * duzey / 100).toFixed(1));
  var miktar14001 = parseFloat((ana14001 * duzey / 100).toFixed(1));
  var miktar45001 = parseFloat((ana45001 * duzey / 100).toFixed(1));
  var miktar50001 = parseFloat((ana50001 * duzey / 100).toFixed(1));
  var miktar27001 = parseFloat((ana27001 * duzey / 100).toFixed(1));
  var miktar22000 = parseFloat((ana22000 * duzey / 100).toFixed(1));
  var miktarOicsmiic = parseFloat((anaOicsmiic * duzey / 100).toFixed(1));
  var kalansure = parseFloat(0);
  $('#denetimentegreindirim').val(miktar.toFixed(1));

  // console.log("ana9001: " + ana900115);
  // console.log("ana14001: " + ana1400115);
  //  console.log("miktar22000: " + miktar22000);
  //  console.log("miktar50001: " + miktar50001);
  // console.log("kalansure: " + kalansure);
  // console.log("miktar: " + miktar);
  kalansure = ana + miktar;
  // console.log("kalansure: " + kalansure);
  if (miktar === 0) {
    $('#toplamkalansure').val(ana.toFixed(1));
    $('#iso9001entindart' + sube).val(ana9001.toFixed(1));
    $('#iso14001entindart' + sube).val(ana14001.toFixed(1));
    $('#iso45001entindart' + sube).val(ana45001.toFixed(1));
    $('#iso50001entindart' + sube).val(ana50001.toFixed(1));
    $('#iso27001entindart' + sube).val(ana27001.toFixed(1));
    $('#iso22000entindart' + sube).val(ana22000.toFixed(1));
    $('#oicsmiicentindart' + sube).val(anaOicsmiic.toFixed(1));

    $('#iso9001kalansure' + sube).val(ana9001.toFixed(1));
    $('#iso14001kalansure' + sube).val(ana14001.toFixed(1));
    $('#iso45001kalansure' + sube).val(ana45001.toFixed(1));
    $('#iso50001kalansure' + sube).val(ana50001.toFixed(1));
    $('#iso27001kalansure' + sube).val(ana27001.toFixed(1));
    $('#iso22000kalansure' + sube).val(ana22000.toFixed(1));
    $('#oicsmiickalansure' + sube).val(anaOicsmiic.toFixed(1));
  } else {
    $('#toplamkalansure').val(kalansure.toFixed(1));
    $('#iso9001entindart' + sube).val((miktar9001).toFixed(1));
    $('#iso14001entindart' + sube).val((miktar14001).toFixed(1));
    $('#iso45001entindart' + sube).val((miktar45001).toFixed(1));
    $('#iso50001entindart' + sube).val((miktar50001).toFixed(1));
    $('#iso27001entindart' + sube).val((miktar27001).toFixed(1));
    $('#iso22000entindart' + sube).val((miktar22000).toFixed(1));
    $('#oicsmiicentindart' + sube).val((miktarOicsmiic).toFixed(1));

    $('#iso9001kalansure' + sube).val((ana9001 - miktar9001).toFixed(1));
    $('#iso14001kalansure' + sube).val((ana14001 - miktar14001).toFixed(1));
    $('#iso45001kalansure' + sube).val((ana45001 - miktar45001).toFixed(1));
    $('#iso50001kalansure' + sube).val((ana50001 - miktar50001).toFixed(1));
    $('#iso27001kalansure' + sube).val((ana27001 - miktar27001).toFixed(1));
    $('#iso22000kalansure' + sube).val((ana22000 - miktar22000).toFixed(1));
    $('#oicsmiickalansure' + sube).val((anaOicsmiic - miktarOicsmiic).toFixed(1));
  }

  // toplamHesapla();
  // indirArttirSebepler();
}

function getEntegreOran(x, y) {
  var formURL = $('#entegreDuzeyleriRoute').val();

  $.ajax({
    type: 'GET',
    url: formURL,
    cache: false,
    success: function (html) {
      // console.log("getEntegreOran::jsondata: " + jsondata);
      var result = $.parseJSON(html);
      $.each(result, function (k, v) {
        if (k === (x + '-' + y)) {
          $('#totoranEntegre').val(v);
          duzey = v;
        }
      });
    }
  });

}

function purifyEksen(eks) {
  var eksen = eks;
  if (eks > 0 && eks < 20) {
    eksen = 0;
  }
  if (eks > 20 && eks < 40) {
    eksen = 40;
  }
  if (eks > 40 && eks < 60) {
    eksen = 60;
  }
  if (eks > 60 && eks < 80) {
    eksen = 80;
  }
  if (eks > 80 && eks < 100) {
    eksen = 100;
  }
  if (eks === 100) {
    eksen = 100;
  }
  if (eks > 100) {
    eksen = 100;
  }
  return eksen;
}

function indartToplamHesapla() {
  var iso9001 = $('#iso900115varyok').val() === '1',
    iso14001 = $('#iso1400115varyok').val() === '1',
    iso22000 = $('#iso2200018varyok').val() === '1',
    iso45001 = $('#iso4500118varyok').val() === '1',
    iso50001 = $('#iso5000118varyok').val() === '1',
    // iso27001 = $('#iso27001varyok').val() === "1",
    oicsmiik = $('#helalvaryok').val() === '1',
    oicsmiik6 = $('#oicsmiik6varyok').val() === '1',
    oicsmiik9 = $('#oicsmiik9varyok').val() === '1',
    oicsmiik171 = $('#oicsmiik171varyok').val() === '1',
    oicsmiik23 = $('#oicsmiik23varyok').val() === '1',
    oicsmiik24 = $('#oicsmiik24varyok').val() === '1';

  var ana9001 = (iso9001) ? parseFloat($('#iso9001hamsure').val()) : parseFloat(0);
  var ana14001 = (iso14001) ? parseFloat($('#iso14001hamsure').val()) : parseFloat(0);
  var ana45001 = (iso45001) ? parseFloat($('#iso45001hamsure').val()) : parseFloat(0);
  var ana50001 = (iso50001) ? parseFloat($('#iso50001hamsure').val()) : parseFloat(0);
  // var ana27001 = (iso27001) ? parseFloat($("#iso27001hamsure").val()) : parseFloat(0);
  var ana22000 = (iso22000) ? parseFloat($('#iso22000hamsure').val()) : parseFloat(0);
  var anaOicsmiic = (oicsmiik || oicsmiik6 || oicsmiik9 || oicsmiik171 || oicsmiik23 || oicsmiik24) ? parseFloat($('#oicsmiichamsure').val()) : parseFloat(0);

  // console.log("ana9001: " + ana9001 + " ana14001: " + ana14001 + " ana45001: " + ana45001 + " ana50001: " + ana50001 + " ana27001: " + ana27001 + " ana22000: " + ana22000 + " anaOicsmiic: " + anaOicsmiic);

  var topind9001 = parseFloat(0);
  var topart9001 = parseFloat(0);
  var topind14001 = parseFloat(0);
  var topart14001 = parseFloat(0);
  var topind45001 = parseFloat(0);
  var topart45001 = parseFloat(0);
  var topind50001 = parseFloat(0);
  var topart50001 = parseFloat(0);
  var topart22000 = parseFloat(0);
  var topindOicsmiic = parseFloat(0);
  var topartOicsmiic = parseFloat(0);
  // var topind27001 = (iso27001) ? parseFloat($("#iso27001indart").val()) : parseFloat(0);
  // var topart27001 = (iso27001) ? parseFloat($("#iso27001azartsure").val()) : parseFloat(0);
  //
  // console.log("ind-art 27001::0::: " + $("#denetimgunarttirilmasi").val());

  var form = document.getElementById('write9001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart9001')) {
      if (form.elements[i].checked === true) {
        if (parseFloat(form.elements[i].value) < 0) {
          topind9001 += parseInt(form.elements[i].value);
        }
        if (parseFloat(form.elements[i].value) > 0) {
          topart9001 += parseInt(form.elements[i].value);
        }
      }
    }
  }

  form = document.getElementById('write14001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart14001')) {
      if (form.elements[i].checked === true) {
        if (parseFloat(form.elements[i].value) < 0) {
          topind14001 += parseFloat(form.elements[i].value);
        }
        if (parseFloat(form.elements[i].value) > 0) {
          topart14001 += parseFloat(form.elements[i].value);
        }
      }
    }
  }

  form = document.getElementById('write45001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart45001')) {
      if (form.elements[i].checked === true) {
        if (parseFloat(form.elements[i].value) < 0) {
          topind45001 += parseFloat(form.elements[i].value);
        }
        if (parseFloat(form.elements[i].value) > 0) {
          topart45001 += parseFloat(form.elements[i].value);
        }
      }
    }
  }

  form = document.getElementById('write50001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart50001')) {
      if (form.elements[i].checked === true) {
        if (parseFloat(form.elements[i].value) < 0) {
          topind50001 += parseFloat(form.elements[i].value);
        }
        if (parseFloat(form.elements[i].value) > 0) {
          topart50001 += parseFloat(form.elements[i].value);
        }
      }
    }
  }

  form = document.getElementById('write22000IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart22000')) {
      if (form.elements[i].checked === true) {
        if (parseFloat(form.elements[i].value) > 0) {
          topart22000 += parseFloat(form.elements[i].value);
        }
      }
    }
  }

  form = document.getElementById('writeSmiicIndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indartsmiic')) {
      if (form.elements[i].checked === true) {
        if (parseFloat(form.elements[i].value) < 0) {
          topindOicsmiic += parseInt(form.elements[i].value);
        }
        if (parseFloat(form.elements[i].value) > 0) {
          topartOicsmiic += parseFloat(form.elements[i].value);
        }
      }
    }
  }


  topind9001 = parseFloat(topind9001);
  topart9001 = parseFloat(topart9001);
  topind14001 = parseFloat(topind14001);
  topart14001 = parseFloat(topart14001);
  topind45001 = parseFloat(topind45001);
  topart45001 = parseFloat(topart45001);
  topind50001 = parseFloat(topind50001);
  topart50001 = parseFloat(topart50001);
  topart22000 = parseFloat(topart22000);
  topindOicsmiic = parseFloat(topindOicsmiic);
  topartOicsmiic = parseFloat(topartOicsmiic);

  if (topind9001 > 30 || topind9001 < -30) {
    topind9001 = (topind9001 < 0) ? topind9001 = -30 : topind9001 = 30;
  }

  if (topart9001 > 30 || topart9001 < -30) {
    topart9001 = (topart9001 < 0) ? topart9001 = -30 : topart9001 = 30;
  }

  if (topind14001 > 30 || topind14001 < -30) {
    topind14001 = (topind14001 < 0) ? topind14001 = -30 : topind14001 = 30;
  }

  if (topart14001 > 30 || topart14001 < -30) {
    topart14001 = (topart14001 < 0) ? topart14001 = -30 : topart14001 = 30;
  }

  if (topind45001 > 30 || topind45001 < -30) {
    topind45001 = (topind45001 < 0) ? topind45001 = -30 : topind45001 = 30;
  }

  if (topart45001 > 30 || topart45001 < -30) {
    topart45001 = (topart45001 < 0) ? topart45001 = -30 : topart45001 = 30;
  }

  if (topind50001 > 30 || topind50001 < -30) {
    topind50001 = (topind50001 < 0) ? topind50001 = -30 : topind50001 = 30;
  }

  if (topart50001 > 30 || topart50001 < -30) {
    topart50001 = (topart50001 < 0) ? topart50001 = -30 : topart50001 = 30;
  }

  if (topart22000 > 30 || topart22000 < -30) {
    topart22000 = (topart22000 < 0) ? topart22000 = -30 : topart22000 = 30;
  }

  if (topindOicsmiic > 30 || topindOicsmiic < -30) {
    topindOicsmiic = (topindOicsmiic < 0) ? topindOicsmiic = -30 : topindOicsmiic = 30;
  }

  if (topartOicsmiic > 30 || topartOicsmiic < -30) {
    topartOicsmiic = (topartOicsmiic < 0) ? topartOicsmiic = -30 : topartOicsmiic = 30;
  }

  // if (topind27001 > 100 || topind27001 < -30) {
  //   topind27001 = (topind27001 < 0) ? topind27001 = -30 : topind27001 = 100;
  // }
  //
  // if (topart27001 > 100 || topart27001 < -30) {
  //   topart27001 = (topart27001 < 0) ? topart27001 = -30 : topart27001 = 100;
  // }

  var indSonuc9001 = parseFloat((ana9001 * topind9001 / 100).toFixed(1));
  var indSonuc14001 = parseFloat((ana14001 * topind14001 / 100).toFixed(1));
  var indSonuc45001 = parseFloat((ana45001 * topind45001 / 100).toFixed(1));
  var indSonuc50001 = parseFloat((ana50001 * topind50001 / 100).toFixed(1));
  // var indSonuc27001 = parseFloat((ana27001 * topind27001 / 100).toFixed(1));
  var indSonucOicsmiic = parseFloat((anaOicsmiic * topindOicsmiic / 100).toFixed(1));

  var artSonuc9001 = parseFloat((ana9001 * topart9001 / 100).toFixed(1));
  var artSonuc14001 = parseFloat((ana14001 * topart14001 / 100).toFixed(1));
  var artSonuc45001 = parseFloat((ana45001 * topart45001 / 100).toFixed(1));
  var artSonuc50001 = parseFloat((ana50001 * topart50001 / 100).toFixed(1));
  // var artSonuc27001 = parseFloat((ana27001 * topart27001 / 100).toFixed(1));
  var artSonuc22000 = parseFloat((ana22000 * topart22000 / 100).toFixed(1));
  var artSonucOicsmiic = parseFloat((anaOicsmiic * topartOicsmiic / 100).toFixed(1));

  // console.log("ind-art 9001::::: " + topind9001 + " ::::: " + topart9001 + " ::::: " + indSonuc9001 + " ::::: " + artSonuc9001);
  // console.log("ind-art 14001::::: " + topind14001 + " ::::: " + topart14001 + " ::::: " + indSonuc14001 + " ::::: " + artSonuc14001);
  // console.log("ind-art 45001::::: " + topind45001 + " ::::: " + topart45001 + " ::::: " + indSonuc45001 + " ::::: " + artSonuc45001);
  // console.log("ind-art 50001::::: " + topind50001 + " ::::: " + topart50001 + " ::::: " + indSonuc50001 + " ::::: " + artSonuc50001);
  // console.log("ind-art 27001::::: " + ana27001 + " ::::: " + topind27001 + " ::::: " + topart27001 + " ::::: " + indSonuc27001 + " ::::: " + artSonuc27001);
  // console.log("ind-art 22000::::: " + topart22000 + " ::::: " + artSonuc22000);
  // console.log("ind-art smiic::::: " + topindOicsmiic + " ::::: " + topartOicsmiic + " ::::: " + indSonucOicsmiic + " ::::: " + artSonucOicsmiic);

  var ind = indSonuc9001 + indSonuc14001 + indSonuc45001 + indSonuc50001 + indSonucOicsmiic;
  $('#denetimgunazaltilmasi').val(ind.toFixed(1));

  var art = artSonuc9001 + artSonuc14001 + artSonuc45001 + artSonuc50001 + artSonuc22000 + artSonucOicsmiic;
  $('#denetimgunarttirilmasi').val(art.toFixed(1));

  indirArttirSebepler();
  toplamHesapla();
}

function indartSahaToplamHesapla(sube) {
  var iso9001 = $('#iso900115varyok').val() === '1',
    iso14001 = $('#iso1400115varyok').val() === '1',
    iso45001 = $('#iso4500118varyok').val() === '1',
    iso50001 = $('#iso5000118varyok').val() === '1',
    iso27001 = $('#iso27001varyok').val() === "1";

  var ana9001 = (iso9001) ? parseFloat($('#iso9001hamsure' + sube).val()) : parseFloat(0);
  var ana14001 = (iso14001) ? parseFloat($('#iso14001hamsure' + sube).val()) : parseFloat(0);
  var ana45001 = (iso45001) ? parseFloat($('#iso45001hamsure' + sube).val()) : parseFloat(0);
  var ana50001 = (iso50001) ? parseFloat($('#iso50001hamsure' + sube).val()) : parseFloat(0);
  // var ana27001 = (iso27001) ? parseFloat($("#iso27001hamsure").val() + sube) : parseFloat(0);

  // console.log("ana9001: " + ana9001 + " ana14001: " + ana14001 + " ana45001: " + ana45001 + " ana50001: " + ana50001 + " ana27001: " + ana27001 + " ana22000: " + ana22000 + " anaOicsmiic: " + anaOicsmiic);

  var topind9001 = parseFloat(0);
  var topart9001 = parseFloat(0);
  var topind14001 = parseFloat(0);
  var topart14001 = parseFloat(0);
  var topind45001 = parseFloat(0);
  var topart45001 = parseFloat(0);
  var topind50001 = parseFloat(0);
  var topart50001 = parseFloat(0);
  // var topind27001 = (iso27001) ? parseFloat($("#iso27001indart").val()) : parseFloat(0);
  // var topart27001 = (iso27001) ? parseFloat($("#iso27001azartsure").val()) : parseFloat(0);
  //
  // console.log("ind-art 27001::0::: " + $("#denetimgunarttirilmasi").val());

  var form = document.getElementById('write9001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart9001')) {
      if (form.elements[i].checked === true) {
        if (parseFloat(form.elements[i].value) < 0) {
          topind9001 += parseInt(form.elements[i].value);
        }
        if (parseFloat(form.elements[i].value) > 0) {
          topart9001 += parseInt(form.elements[i].value);
        }
      }
    }
  }

  form = document.getElementById('write14001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart14001')) {
      if (form.elements[i].checked === true) {
        if (parseFloat(form.elements[i].value) < 0) {
          topind14001 += parseFloat(form.elements[i].value);
        }
        if (parseFloat(form.elements[i].value) > 0) {
          topart14001 += parseFloat(form.elements[i].value);
        }
      }
    }
  }

  form = document.getElementById('write45001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart45001')) {
      if (form.elements[i].checked === true) {
        if (parseFloat(form.elements[i].value) < 0) {
          topind45001 += parseFloat(form.elements[i].value);
        }
        if (parseFloat(form.elements[i].value) > 0) {
          topart45001 += parseFloat(form.elements[i].value);
        }
      }
    }
  }

  form = document.getElementById('write50001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart50001')) {
      if (form.elements[i].checked === true) {
        if (parseFloat(form.elements[i].value) < 0) {
          topind50001 += parseFloat(form.elements[i].value);
        }
        if (parseFloat(form.elements[i].value) > 0) {
          topart50001 += parseFloat(form.elements[i].value);
        }
      }
    }
  }

  topind9001 = parseFloat(topind9001);
  topart9001 = parseFloat(topart9001);
  topind14001 = parseFloat(topind14001);
  topart14001 = parseFloat(topart14001);
  topind45001 = parseFloat(topind45001);
  topart45001 = parseFloat(topart45001);
  topind50001 = parseFloat(topind50001);
  topart50001 = parseFloat(topart50001);

  if (topind9001 > 30 || topind9001 < -30) {
    topind9001 = (topind9001 < 0) ? topind9001 = -30 : topind9001 = 30;
  }

  if (topart9001 > 30 || topart9001 < -30) {
    topart9001 = (topart9001 < 0) ? topart9001 = -30 : topart9001 = 30;
  }

  if (topind14001 > 30 || topind14001 < -30) {
    topind14001 = (topind14001 < 0) ? topind14001 = -30 : topind14001 = 30;
  }

  if (topart14001 > 30 || topart14001 < -30) {
    topart14001 = (topart14001 < 0) ? topart14001 = -30 : topart14001 = 30;
  }

  if (topind45001 > 30 || topind45001 < -30) {
    topind45001 = (topind45001 < 0) ? topind45001 = -30 : topind45001 = 30;
  }

  if (topart45001 > 30 || topart45001 < -30) {
    topart45001 = (topart45001 < 0) ? topart45001 = -30 : topart45001 = 30;
  }

  if (topind50001 > 30 || topind50001 < -30) {
    topind50001 = (topind50001 < 0) ? topind50001 = -30 : topind50001 = 30;
  }

  if (topart50001 > 30 || topart50001 < -30) {
    topart50001 = (topart50001 < 0) ? topart50001 = -30 : topart50001 = 30;
  }

  // if (topind27001 > 100 || topind27001 < -30) {
  //   topind27001 = (topind27001 < 0) ? topind27001 = -30 : topind27001 = 100;
  // }
  //
  // if (topart27001 > 100 || topart27001 < -30) {
  //   topart27001 = (topart27001 < 0) ? topart27001 = -30 : topart27001 = 100;
  // }

  var indSonuc9001 = parseFloat((ana9001 * topind9001 / 100).toFixed(1));
  var indSonuc14001 = parseFloat((ana14001 * topind14001 / 100).toFixed(1));
  var indSonuc45001 = parseFloat((ana45001 * topind45001 / 100).toFixed(1));
  var indSonuc50001 = parseFloat((ana50001 * topind50001 / 100).toFixed(1));
  // var indSonuc27001 = parseFloat((ana27001 * topind27001 / 100).toFixed(1));

  var artSonuc9001 = parseFloat((ana9001 * topart9001 / 100).toFixed(1));
  var artSonuc14001 = parseFloat((ana14001 * topart14001 / 100).toFixed(1));
  var artSonuc45001 = parseFloat((ana45001 * topart45001 / 100).toFixed(1));
  var artSonuc50001 = parseFloat((ana50001 * topart50001 / 100).toFixed(1));
  // var artSonuc27001 = parseFloat((ana27001 * topart27001 / 100).toFixed(1));

  // console.log("ind-art 9001::::: " + topind9001 + " ::::: " + topart9001 + " ::::: " + indSonuc9001 + " ::::: " + artSonuc9001);
  // console.log("ind-art 14001::::: " + topind14001 + " ::::: " + topart14001 + " ::::: " + indSonuc14001 + " ::::: " + artSonuc14001);
  // console.log("ind-art 45001::::: " + topind45001 + " ::::: " + topart45001 + " ::::: " + indSonuc45001 + " ::::: " + artSonuc45001);
  // console.log("ind-art 50001::::: " + topind50001 + " ::::: " + topart50001 + " ::::: " + indSonuc50001 + " ::::: " + artSonuc50001);
  // console.log("ind-art 27001::::: " + ana27001 + " ::::: " + topind27001 + " ::::: " + topart27001 + " ::::: " + indSonuc27001 + " ::::: " + artSonuc27001);

  var ind = indSonuc9001 + indSonuc14001 + indSonuc45001 + indSonuc50001;
  var indm = parseFloat($('#denetimgunazaltilmasi').val());
  $('#denetimgunazaltilmasi').val((indm + ind).toFixed(1));

  var art = artSonuc9001 + artSonuc14001 + artSonuc45001 + artSonuc50001;
  var artm = parseFloat($('#denetimgunarttirilmasi').val());
  $('#denetimgunarttirilmasi').val((artm + art).toFixed(1));

  indirArttirSebepler();
  toplamHesapla();
}

function sahaHesapla() {
  var subesayisi = $('#inceleneceksahasayisi').val() //p
  for (var i = 1; i <= subesayisi; i++) {
    iso9001SahaSureHesapla(i);
    iso14001SahaSureHesapla(i);
    iso45001SahaSureHesapla(i);
    iso27001SahaSureHesapla(i);
    iso50001SahaSureHesapla(i);
  }
}

function hesapla() {
  var spnr = $('#sureHesaplaSpinner');
  spnr.show();

  iso9001SureHesapla();
  iso14001SureHesapla();
  iso45001SureHesapla();
  iso50001SureHesapla();
  iso27001SureHesapla();
  iso22000SureHesapla();
  isoOicSmiicSureHesapla();

  sahaHesapla();
  runBgysCalculation();
  // indartToplamHesapla();
  toplamHesapla();
  spnr.hide();

}

function toplamHesapla() {
  var iso9001 = $('#iso900115varyok').val() === '1',
    iso14001 = $('#iso1400115varyok').val() === '1',
    iso22000 = $('#iso2200018varyok').val() === '1',
    iso45001 = $('#iso4500118varyok').val() === '1',
    iso50001 = $('#iso5000118varyok').val() === '1',
    iso27001 = $('#iso27001varyok').val() === '1',
    oicsmiik = $('#helalvaryok').val() === '1',
    oicsmiik6 = $('#oicsmiik6varyok').val() === '1',
    oicsmiik9 = $('#oicsmiik9varyok').val() === '1',
    oicsmiik171 = $('#oicsmiik171varyok').val() === '1',
    oicsmiik23 = $('#oicsmiik23varyok').val() === '1',
    oicsmiik24 = $('#oicsmiik24varyok').val() === '1',
    isOicsmiic = (oicsmiik || oicsmiik6 || oicsmiik9 || oicsmiik171 || oicsmiik23 || oicsmiik24) ?? false;

// Diğer standartların herhangi birinin seçili olup olmadığını kontrol et
  var hasOtherStandards = iso9001 || iso14001 || iso22000 || iso45001 || iso50001 || isOicsmiic;

  var ana = parseFloat(0);
  var indart = parseFloat(0);
  var azart = parseFloat(0);
  var entindart = parseFloat(0);
  var kalan = parseFloat(0);
  var a1sure = parseFloat(0);
  var a2sure = parseFloat(0);
  var gozsure = parseFloat(0);
  var ybsure = parseFloat(0);

  var anasube = parseFloat(0);
  var indartsube = parseFloat(0);
  var azartsube = parseFloat(0);
  var entindartsube = parseFloat(0);
  var kalansube = parseFloat(0);
  var a1suresube = parseFloat(0);
  var a2suresube = parseFloat(0);
  var gozsuresube = parseFloat(0);
  var ybsuresube = parseFloat(0);
  var subesayisi = $('#inceleneceksahasayisi').val() //p

  if (iso9001) {
    for (var i = 1; i <= subesayisi; i++) {
      anasube += parseFloat($('#iso9001hamsure' + i).val());
      indartsube += parseFloat($('#iso9001indart' + i).val());
      azartsube += parseFloat($('#iso9001azartsure' + i).val());
      entindartsube += parseFloat($('#iso9001entindart' + i).val());
      kalansube += parseFloat($('#iso9001kalansure' + i).val());
      a1suresube += parseFloat($('#iso9001a1sure' + i).val());
      a2suresube += parseFloat($('#iso9001a2sure' + i).val());
      gozsuresube += parseFloat($('#iso9001gsure' + i).val());
      ybsuresube += parseFloat($('#iso9001ybsure' + i).val());
    }

    ana += parseFloat($('#iso9001hamsure').val());
    indart += parseFloat($('#iso9001indart').val());
    azart += parseFloat($('#iso9001azartsure').val());
    entindart += parseFloat($('#iso9001entindart').val());
    kalan += parseFloat($('#iso9001kalansure').val());
    a1sure += parseFloat($('#iso9001a1sure').val());
    a2sure += parseFloat($('#iso9001a2sure').val());
    gozsure += parseFloat($('#iso9001gsure').val());
    ybsure += parseFloat($('#iso9001ybsure').val());
  }

  if (iso14001) {
    for (var i = 1; i <= subesayisi; i++) {
      anasube += parseFloat($('#iso14001hamsure' + i).val());
      indartsube += parseFloat($('#iso14001indart' + i).val());
      azartsube += parseFloat($('#iso14001azartsure' + i).val());
      entindartsube += parseFloat($('#iso14001entindart' + i).val());
      kalansube += parseFloat($('#iso14001kalansure' + i).val());
      a1suresube += parseFloat($('#iso14001a1sure' + i).val());
      a2suresube += parseFloat($('#iso14001a2sure' + i).val());
      gozsuresube += parseFloat($('#iso14001gsure' + i).val());
      ybsuresube += parseFloat($('#iso14001ybsure' + i).val());
    }
    ana += parseFloat($('#iso14001hamsure').val());
    indart += parseFloat($('#iso14001indart').val());
    azart += parseFloat($('#iso14001azartsure').val());
    entindart += parseFloat($('#iso14001entindart').val());
    kalan += parseFloat($('#iso14001kalansure').val());
    a1sure += parseFloat($('#iso14001a1sure').val());
    a2sure += parseFloat($('#iso14001a2sure').val());
    gozsure += parseFloat($('#iso14001gsure').val());
    ybsure += parseFloat($('#iso14001ybsure').val());
  }

  if (iso45001) {
    for (var i = 1; i <= subesayisi; i++) {
      anasube += parseFloat($('#iso45001hamsure' + i).val());
      indartsube += parseFloat($('#iso45001indart' + i).val());
      azartsube += parseFloat($('#iso45001azartsure' + i).val());
      entindartsube += parseFloat($('#iso45001entindart' + i).val());
      kalansube += parseFloat($('#iso45001kalansure' + i).val());
      a1suresube += parseFloat($('#iso45001a1sure' + i).val());
      a2suresube += parseFloat($('#iso45001a2sure' + i).val());
      gozsuresube += parseFloat($('#iso45001gsure' + i).val());
      ybsuresube += parseFloat($('#iso45001ybsure' + i).val());
    }
    ana += parseFloat($('#iso45001hamsure').val());
    indart += parseFloat($('#iso45001indart').val());
    azart += parseFloat($('#iso45001azartsure').val());
    entindart += parseFloat($('#iso45001entindart').val());
    kalan += parseFloat($('#iso45001kalansure').val());
    a1sure += parseFloat($('#iso45001a1sure').val());
    a2sure += parseFloat($('#iso45001a2sure').val());
    gozsure += parseFloat($('#iso45001gsure').val());
    ybsure += parseFloat($('#iso45001ybsure').val());
  }

  if (iso50001) {
    for (var i = 1; i <= subesayisi; i++) {
      anasube += parseFloat($('#iso50001hamsure' + i).val());
      indartsube += parseFloat($('#iso50001indart' + i).val());
      azartsube += parseFloat($('#iso50001azartsure' + i).val());
      entindartsube += parseFloat($('#iso50001entindart' + i).val());
      kalansube += parseFloat($('#iso50001kalansure' + i).val());
      a1suresube += parseFloat($('#iso50001a1sure' + i).val());
      a2suresube += parseFloat($('#iso50001a2sure' + i).val());
      gozsuresube += parseFloat($('#iso50001gsure' + i).val());
      ybsuresube += parseFloat($('#iso50001ybsure' + i).val());
    }
    ana += parseFloat($('#iso50001hamsure').val());
    indart += parseFloat($('#iso50001indart').val());
    azart += parseFloat($('#iso50001azartsure').val());
    entindart += parseFloat($('#iso50001entindart').val());
    kalan += parseFloat($('#iso50001kalansure').val());
    a1sure += parseFloat($('#iso50001a1sure').val());
    a2sure += parseFloat($('#iso50001a2sure').val());
    gozsure += parseFloat($('#iso50001gsure').val());
    ybsure += parseFloat($('#iso50001ybsure').val());
  }

  if (iso27001) {
    for (var i = 1; i <= subesayisi; i++) {
      anasube += parseFloat($('#iso27001hamsure' + i).val());
      indartsube += parseFloat($('#iso27001indart' + i).val());
      azartsube += parseFloat($('#iso27001azartsure' + i).val());
      entindartsube += parseFloat($('#iso27001entindart' + i).val());
      kalansube += parseFloat($('#iso27001kalansure' + i).val());
      a1suresube += parseFloat($('#iso27001a1sure' + i).val());
      a2suresube += parseFloat($('#iso27001a2sure' + i).val());
      gozsuresube += parseFloat($('#iso27001gsure' + i).val());
      ybsuresube += parseFloat($('#iso27001ybsure' + i).val());
    }
    ana += parseFloat($('#iso27001hamsure').val());
    indart += parseFloat($('#iso27001indart').val());
    azart += parseFloat($('#iso27001azartsure').val());
    entindart += parseFloat($('#iso27001entindart').val());
    kalan += parseFloat($('#iso27001kalansure').val());
    a1sure += parseFloat($('#iso27001a1sure').val());
    a2sure += parseFloat($('#iso27001a2sure').val());
    gozsure += parseFloat($('#iso27001gsure').val());
    ybsure += parseFloat($('#iso27001ybsure').val());
  }

  if (iso22000) {
    ana += parseFloat($('#iso22000hamsure').val());
    indart += parseFloat($('#iso22000indart').val());
    azart += parseFloat($('#iso22000azartsure').val());
    entindart += parseFloat($('#iso22000entindart').val());
    kalan += parseFloat($('#iso22000kalansure').val());
    a1sure += parseFloat($('#iso22000a1sure').val());
    a2sure += parseFloat($('#iso22000a2sure').val());
    gozsure += parseFloat($('#iso22000gsure').val());
    ybsure += parseFloat($('#iso22000ybsure').val());
  }

  if (isOicsmiic) {
    ana += parseFloat($('#oicsmiichamsure').val());
    indart += parseFloat($('#oicsmiicindart').val());
    azart += parseFloat($('#oicsmiicazartsure').val());
    entindart += parseFloat($('#oicsmiicentindart').val());
    kalan += parseFloat($('#oicsmiickalansure').val());
    a1sure += parseFloat($('#oicsmiica1sure').val());
    a2sure += parseFloat($('#oicsmiica2sure').val());
    gozsure += parseFloat($('#oicsmiicgsure').val());
    ybsure += parseFloat($('#oicsmiicybsure').val());
  }

  kalan = (iso9001 || iso14001 || iso45001) ? parseFloat(roundNearest5(kalan)) : parseFloat(kalan);

  a1sure = parseFloat(kalan * 30 / 100);
  a2sure = parseFloat(kalan * 70 / 100);
  gozsure = parseFloat(roundNearest5(kalan / 3));
  ybsure = parseFloat(roundNearest5(kalan * 2 / 3));

  $('#toplamhamsure').val(ana.toFixed(1));
  $('#toplamindart').val(indart.toFixed(1));
  $('#toplamazart').val(azart.toFixed(1));
  $('#toplamentindart').val(entindart.toFixed(1));

  $('#toplamkalansure').val(kalan.toFixed(1));

  $('#toplama1sure').val(a1sure.toFixed(1));
  $('#toplama2sure').val(a2sure.toFixed(1));
  $('#toplamgsure').val(gozsure.toFixed(1));
  $('#toplamybsure').val(ybsure.toFixed(1));

  $('#toplamhamsuretmp').val(ana.toFixed(1));
  $('#toplamindarttmp').val(indart.toFixed(1));
  $('#toplamazarttmp').val(azart.toFixed(1));
  $('#toplamentindarttmp').val(entindart.toFixed(1));

  $('#toplamkalansuretmp').val(kalan.toFixed(1));

  $('#toplama1suretmp').val(a1sure.toFixed(1));
  $('#toplama2suretmp').val(a2sure.toFixed(1));
  $('#toplamgsuretmp').val(gozsure.toFixed(1));
  $('#toplamybsuretmp').val(ybsure.toFixed(1));

// Sadece iso27001 true diğerleri hepsi false ise carpan = 0.7, aksi halde carpan = 0.8
  var carpan = (iso27001 && !hasOtherStandards) ? 0.7 : 0.8;

  $('#toplama1suresaat').val(parseFloat(roundNearest5(a1sure * 8 * carpan)).toFixed(1) + " Saat");
  $('#toplama2suresaat').val(parseFloat(roundNearest5(a2sure * 8 * carpan)).toFixed(1) + " Saat");
  $('#toplamgsuresaat').val(parseFloat(roundNearest5(gozsure * 8 * carpan)).toFixed(1) + " Saat");
  $('#toplamybsuresaat').val(parseFloat(roundNearest5(ybsure * 8 * carpan)).toFixed(1) + " Saat");

  $('#toplama1suresaattmp').val(parseFloat(roundNearest5(a1sure * 8 * carpan)).toFixed(1) + " Saat");
  $('#toplama2suresaattmp').val(parseFloat(roundNearest5(a2sure * 8 * carpan)).toFixed(1) + " Saat");
  $('#toplamgsuresaattmp').val(parseFloat(roundNearest5(gozsure * 8 * carpan)).toFixed(1) + " Saat");
  $('#toplamybsuresaattmp').val(parseFloat(roundNearest5(ybsure * 8 * carpan)).toFixed(1) + " Saat");

  $('#sureHesaplaSpinner').hide();

  denetimUcretiHesapla();
}

function denetimUcretiHesapla() {
  var iso9001 = $('#iso900115varyok').val() === '1',
    iso14001 = $('#iso1400115varyok').val() === '1',
    iso22000 = $('#iso2200018varyok').val() === '1',
    iso45001 = $('#iso4500118varyok').val() === '1',
    iso50001 = $('#iso5000118varyok').val() === '1',
    iso27001 = $('#iso27001varyok').val() === '1',
    oicsmiik = $('#helalvaryok').val() === '1',
    oicsmiik6 = $('#oicsmiik6varyok').val() === '1',
    oicsmiik9 = $('#oicsmiik9varyok').val() === '1',
    oicsmiik171 = $('#oicsmiik171varyok').val() === '1',
    oicsmiik23 = $('#oicsmiik23varyok').val() === '1',
    oicsmiik24 = $('#oicsmiik24varyok').val() === '1',
    oicSmiic = (oicsmiik || oicsmiik6 || oicsmiik9 || oicsmiik171 || oicsmiik23 || oicsmiik24) ?? false;

  var denetimsuresi = parseFloat($('#toplamkalansure').val());
  var oicdenetimsuresi = parseFloat(0);
  var kalan9001 = parseFloat(0);
  var kalan14001 = parseFloat(0);
  var kalan22000 = parseFloat(0);
  var kalanOicsmiic = parseFloat(0);
  var kalan45001 = parseFloat(0);
  var kalan50001 = parseFloat(0);
  var kalan27001 = parseFloat(0);

  var basvuruucreti = parseFloat(3000);
  var kullanimucreti = parseFloat(1000);
  var gunlukucret = parseFloat(1750);
  var gozgunlukucret = parseFloat(1000);

  var oicbasvuruucreti = (oicsmiik9) ? parseFloat(15000) : parseFloat(10000);
  var oickullanimucreti = parseFloat(2000);
  var oicgunlukucret = parseFloat(1000);

  var kacsistem = parseFloat(0);
  var toplamdenetimucreti = parseFloat(0);
  var toplamgozetimucreti = parseFloat(0);

  var oickacsistem = parseFloat(0);
  var oictoplamdenetimucreti = parseFloat(0);
  var oictoplamgozetimucreti = parseFloat(0);

  if (iso9001) {
    kalan9001 = parseFloat($('#iso9001kalansure').val());
    kacsistem++;
  }
  if (iso14001) {
    kalan14001 = parseFloat($('#iso14001kalansure').val());
    kacsistem++;
  }
  if (iso45001) {
    kalan45001 = parseFloat($('#iso45001kalansure').val());
    kacsistem++;
  }
  if (iso50001) {
    kalan50001 = parseFloat($('#iso50001kalansure').val());
    kacsistem++;
  }
  if (iso27001) {
    kalan27001 = parseFloat($('#iso27001kalansure').val());
    kacsistem++;
  }
  if (iso22000) {
    kalan22000 = parseFloat($('#iso22000kalansure').val());
    kacsistem++;
  }
  if (oicSmiic) {
    kalanOicsmiic = parseFloat($('#oicsmiickalansure').val());
    oickacsistem++;
  }
  var oicgozetimsuresi = (oicSmiic) ? parseFloat($('#oicsmiicgsure').val()).toFixed(1) : parseFloat(0);
  var isogozetimsuresi = (parseFloat($('#toplamgsure').val()) - oicgozetimsuresi).toFixed(1);

  basvuruucreti = basvuruucreti * kacsistem;
  denetimsuresi = denetimsuresi - (kalanOicsmiic);

  oicbasvuruucreti = oicbasvuruucreti * oickacsistem;
  oicdenetimsuresi = kalanOicsmiic;
  // console.log(kacsistem);
  // console.log("denetimUcretiHesapla:::kullanimucreti:::" + kullanimucreti + ":::basvuruucreti:::" + basvuruucreti + ":::isogozetimsuresi:::" + isogozetimsuresi + ":::oicgozetimsuresi:::" + oicgozetimsuresi + ":::gunlukucret:::" + gunlukucret);

  toplamdenetimucreti = basvuruucreti + (denetimsuresi * gunlukucret);
  toplamgozetimucreti = kullanimucreti + basvuruucreti + (isogozetimsuresi * gozgunlukucret);

  $('#belgelendirmedenetimucreti').val(toplamdenetimucreti.toFixed(0));
  $('#gozetimdenetimucreti').val(toplamgozetimucreti.toFixed(0));

  oictoplamdenetimucreti = oicbasvuruucreti + (oicdenetimsuresi * oicgunlukucret);
  oictoplamgozetimucreti = oickullanimucreti + oicbasvuruucreti + (oicgozetimsuresi * oicgunlukucret);

  $('#oicbelgelendirmedenetimucreti').val(oictoplamdenetimucreti.toFixed(0));
  $('#oicgozetimdenetimucreti').val(oictoplamgozetimucreti.toFixed(0));

}

function indirArttirSebepler() {
  var nedentmp = "";
  var entnedentmp = "";
  var neden = [], nedenler = [];

  var form = document.getElementById('write9001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart9001')) {
      if (form.elements[i].checked === true) {
        nedentmp = $('label[for="' + form.elements[i].id + '"]').html().replace("(-10)", "").replace("(+10)", "").replace("(-)", "").trim();
        nedentmp = nedentmp.replace("(-10)", "").replace("(+10)", "").replace("(10)", "").replace("(-30)", "").replace("(-)", "").trim();
        // console.log("9001:::"+nedentmp);
        if (nedentmp === "") continue;
        if (neden.length < 0) {
          neden.push(nedentmp);
        } else {
          if (neden.indexOf(nedentmp) === -1) {
            neden.push(nedentmp);
          }
        }
      }
    }
  }

  form = document.getElementById('write14001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart14001')) {
      if (form.elements[i].checked === true) {
        nedentmp = $('label[for="' + form.elements[i].id + '"]').html().replace("(-10)", "").replace("(+10)", "").replace("(-)", "").trim();
        nedentmp = nedentmp.replace("(-10)", "").replace("(+10)", "").replace("(10)", "").replace("(-30)", "").replace("(-)", "").trim();
        // console.log("14001:::"+nedentmp);
        if (nedentmp === "") continue;
        if (neden.length < 0) {
          neden.push(nedentmp);
        } else {
          if (neden.indexOf(nedentmp) === -1) {
            neden.push(nedentmp);
          }
        }
      }
    }
  }

  form = document.getElementById('write45001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart45001')) {
      if (form.elements[i].checked === true) {
        nedentmp = $('label[for="' + form.elements[i].id + '"]').html().replace("(-10)", "").replace("(+10)", "").replace("(-)", "").trim();
        nedentmp = nedentmp.replace("(-10)", "").replace("(+10)", "").replace("(10)", "").replace("(-30)", "").replace("(-)", "").trim();
        // console.log("45001:::"+nedentmp);
        if (nedentmp === "") continue;
        if (neden.length < 0) {
          neden.push(nedentmp);
        } else {
          if (neden.indexOf(nedentmp) === -1) {
            neden.push(nedentmp);
          }
        }
      }
    }
  }

  form = document.getElementById('write50001IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart50001')) {
      if (form.elements[i].checked === true) {
        nedentmp = $('label[for="' + form.elements[i].id + '"]').html().replace("(-10)", "").replace("(+10)", "").replace("(-)", "").trim();
        nedentmp = nedentmp.replace("(-10)", "").replace("(+10)", "").replace("(10)", "").replace("(-30)", "").replace("(-)", "").trim();
        // console.log("50001:::"+nedentmp);
        if (nedentmp === "") continue;

        if (neden.length < 0) {
          neden.push(nedentmp);
        } else {
          if (neden.indexOf(nedentmp) === -1) {
            neden.push(nedentmp);
          }
        }
      }
    }
  }

  form = document.getElementById('write22000IndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indart22000')) {
      if (form.elements[i].checked === true) {
        nedentmp = $('label[for="' + form.elements[i].id + '"]').html().replace("(-10)", "").replace("(+10)", "").replace("(-)", "").trim();
        nedentmp = nedentmp.replace("(-10)", "").replace("(+10)", "").replace("(10)", "").replace("(-30)", "").replace("(-)", "").trim();
        // console.log("22000:::"+nedentmp);
        if (nedentmp === "") continue;
        if (neden.length < 0) {
          neden.push(nedentmp);
        } else {
          if (neden.indexOf(nedentmp) === -1) {
            neden.push(nedentmp);
          }
        }
      }
    }
  }

  form = document.getElementById('writeSmiicIndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indartsmiic')) {
      if (form.elements[i].checked === true) {
        nedentmp = $('label[for="' + form.elements[i].id + '"]').html().replace("(-10)", "").replace("(+10)", "").replace("(-)", "").trim();
        nedentmp = nedentmp.replace("(-10)", "").replace("(+10)", "").replace("(10)", "").replace("(-30)", "").replace("(-)", "").trim();
        // console.log("smiic:::"+nedentmp);
        if (nedentmp === "") continue;
        if (neden.length < 0) {
          neden.push(nedentmp);
        } else {
          if (neden.indexOf(nedentmp) === -1) {
            neden.push(nedentmp);
          }
        }
      }
    }
  }

  form = document.getElementById('writeEntegreIndArt-form');
  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type === 'checkbox' && form.elements[i].name.startsWith('chb_indartentegre')) {
      if (form.elements[i].checked === true) {
        entnedentmp = "Entegre denetim";

        if (neden.length < 0) {
          neden.push(entnedentmp);
        } else {
          if (neden.indexOf(entnedentmp) === -1) {
            neden.push(entnedentmp);
          }
        }
      }
    }
  }

  neden.forEach(function (n) {
    // console.log("forEach:::: " + n);
    if (n.trim() !== "" || n.trim() !== null || !n.isNull()) nedenler.push(n);
  });
  // if (neden === "")
  //   neden = entneden;
  // else
  //   neden += entneden;

  // neden = neden.toString().substring(0, neden.toString().length - 2)

  $("#indartneden").val(nedenler);
  // console.log("indirim/arttırım sebepleri: " + nedenler);
}

function changeCalendarMonth(donem) {
  if ($("#divdenetimtakvimi").length > 0) {
    var datastring = "";
    var ay = $("#curmonth").html();
    var yil = $("#curyear").html();
    var m = parseInt(ay);
    var y = parseInt(yil);

    console.log(ay + " : " + yil);
    if (donem === "prev") {
      m--;
      // y--;

      if (m < 1) y--;
      if (m < 1) m = 12;

      datastring = "ay=" + m + "&yil=" + y;
      $("#curmonth").html(m);
      $("#curyear").html(y);
      console.log(m + " : " + y);
    }
    if (donem === "next") {
      m++;
      // y++;

      if (m > 12) y++;
      if (m > 12) m = 1;

      datastring = "ay=" + m + "&yil=" + y;
      $("#curmonth").html(m);
      $("#curyear").html(y);
      console.log(m + " : " + y);
    }
    if (donem === "current") {
      m = d.getMonth() + 1;
      y = d.getFullYear();

      datastring = "ay=" + m + "&yil=" + y;
      $("#curmonth").html(m);
      $("#curyear").html(y);
      console.log(m + " : " + y);
    }
    console.log("datastring: " + datastring);
    $.ajax({
      type: "GET",
      url: denetimTakvimiRoutePath,
      data: datastring,
      cache: false,
      success: function (html) {
        $("#divdenetimtakvimi").html(html);

        //console.log("Ay: "+ay);
        $("#divcalendarmonth").html(m + "." + y);
      }
    });
  }
}

function denetimSetiHazirla() {
  var form = $('#planlama-form');
  var disabled = form.find(':input:disabled').removeAttr('disabled');

  var postData = form.serialize();
  var formURL = $('#formPlanlamaRoute').val();
  $('#setSetHazirlaSpinner').show();
  // console.log("formKaydet::route:: ");
  // console.log("iso9001SureHesapla::postData:: " + formURL + "?" + postData + " ::::: " + iso900115varyok);
  $.ajax({
    url: formURL,
    type: 'POST',
    data: postData,
    success: function (html) {
      $('#btnAsRaporYazdirLink').html(html);
      disabled.attr('disabled', 'disabled');
      $('#setSetHazirlaSpinner').hide();
      denetimRaporuYukle();

    },
    error: function (jqXHR, textStatus, errorThrown) {
      $('#formkaydetsonucerror').html('[iso9001SureHesapla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + '<br>formKaydet: ' + formURL + '?' + postData);
      // window.console.log("formKaydet: " + formURL + "?" + postData);
      $('#myModalError').modal('show');
      $('#setSetHazirlaSpinner').hide();
    }
  });
}

function kararHazirla() {
  var form = $('#karar-form');
  var dktarih = $('#kdegerlendirmekarartarih');

  if(dktarih.val() == ""){
    $("#btnAsRaporYazdirLink").html("Değerlendirme Karar tarihi seçiniz.");
    return false;
  }
  var postData = form.serialize();
  var formURL = $('#formKararRoute').val();
  console.log("Karar hazırla: " + postData);
  $.ajax(
    {
      url: formURL,
      type: 'POST',
      data: postData,
      success: function (html) {
        $("#btnAsRaporYazdirLink").html(html);
        $("#dvloader").hide();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#btnAsRaporYazdirLink").html('[kararHazirla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown);
        $("#dvloader").hide();
      }
    });
}

function sertifikaKaydet() {
  var form = $('#sertifika-form');

  var postData = form.serialize();
  var formURL = $('#formSertifikaKaydetRoute').val();
  console.log("sertifika-form kaydet: " + postData);
  $.ajax(
    {
      url: formURL,
      type: 'POST',
      data: postData,
      success: function (html) {
        $("#btnAsRaporKaydetLink").html(html);
        $("#dvloader").hide();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#btnAsRaporKaydetLink").html('[sertifikaKaydet]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown);
        $("#dvloader").hide();
      }
    });
}

function sertifikaHazirla() {
  var form = $('#sertifika-form');
  var formWrite9001IndArt = $('#write9001IndArt-form');
  var formWrite14001IndArt = $('#write14001IndArt-form');
  var formWrite45001IndArt = $('#write45001IndArt-form');

  var postData = form.serialize() + "&" + formWrite9001IndArt.serialize() + "&" + formWrite14001IndArt.serialize() + "&" + formWrite45001IndArt.serialize();
  var formURL = $('#formSertifikaRoute').val();
  console.log("sertifika-form hazırla: " + postData);
  $.ajax(
    {
      url: formURL,
      type: 'POST',
      data: postData,
      success: function (html) {
        $("#btnAsRaporYazdirLink").html(html);
        $("#dvloader").hide();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#btnAsRaporYazdirLink").html('[sertifikaHazirla]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown);
        $("#dvloader").hide();
      }
    });
}

function subeSurelerEkle1(alan, sube) {
  // Retrieve default total values from data attributes or define them directly
  var defaultAna = parseFloat($('#toplamhamsuretmp').val()) || 0;
  var defaultIndart = parseFloat($('#toplamindarttmp').val()) || 0;
  var defaultAzart = parseFloat($('#toplamazarttmp').val()) || 0;
  var defaultEntindart = parseFloat($('#toplamentindarttmp').val()) || 0;
  var defaultKalan = parseFloat($('#toplamkalansuretmp').val()) || 0;

  // Initialize totals with default values
  var ana = defaultAna;
  var indart = defaultIndart;
  var azart = defaultAzart;
  var entindart = defaultEntindart;
  var kalan = defaultKalan;

  // Initialize other variables
  var a1sure = 0;
  var a2sure = 0;
  var gozsure = 0;
  var ybsure = 0;

  // Subtotal variables
  var anasube = 0;
  var indartsube = 0;
  var azartsube = 0;
  var entindartsube = 0;
  var kalansube = 0;
  var a1suresube = 0;
  var a2suresube = 0;
  var gozsuresube = 0;
  var ybsuresube = 0;
  var subesayisi = $('#inceleneceksahasayisi').val();

  for (var i = 1; i <= subesayisi; i++) {
    var chk = $('#chkSube' + i + alan).is(':checked');
    if (chk) {
      // Accumulate subtotals from checked checkboxes
      anasube += parseFloat($('#' + alan + 'hamsure' + i).val()) || 0;
      indartsube += parseFloat($('#' + alan + 'indart' + i).val()) || 0;
      azartsube += parseFloat($('#' + alan + 'azartsure' + i).val()) || 0;
      entindartsube += parseFloat($('#' + alan + 'entindart' + i).val()) || 0;
      kalansube += parseFloat($('#' + alan + 'kalansure' + i).val()) || 0;
      a1suresube += parseFloat($('#' + alan + 'a1sure' + i).val()) || 0;
      a2suresube += parseFloat($('#' + alan + 'a2sure' + i).val()) || 0;
      gozsuresube += parseFloat($('#' + alan + 'gsure' + i).val()) || 0;
      ybsuresube += parseFloat($('#' + alan + 'ybsure' + i).val()) || 0;
    }
  }

  // Add subtotals to the default totals
  ana += anasube;
  indart += indartsube;
  azart += azartsube;
  entindart += entindartsube;
  kalan += kalansube;

  // Calculate other totals based on the updated 'kalan'
  a1sure = parseFloat(roundNearest5(kalan * 30 / 100));
  a2sure = parseFloat(roundNearest5(kalan * 70 / 100));
  gozsure = parseFloat(roundNearest5(kalan / 3));
  ybsure = parseFloat(roundNearest5(kalan * 2 / 3));

  // Update the total input fields
  $('#toplamhamsure').val(ana.toFixed(1));
  $('#toplamindart').val(indart.toFixed(1));
  $('#toplamazart').val(azart.toFixed(1));
  $('#toplamentindart').val(entindart.toFixed(1));
  $('#toplamkalansure').val(kalan.toFixed(1));

  $('#toplama1sure').val(a1sure.toFixed(1));
  $('#toplama2sure').val(a2sure.toFixed(1));
  $('#toplamgsure').val(gozsure.toFixed(1));
  $('#toplamybsure').val(ybsure.toFixed(1));

  $('#sureHesaplaSpinner').hide();

  denetimUcretiHesapla();
}

function subeSurelerEkle() {
  // Initialize default total values from hidden inputs in the footer
  var defaultAna = parseFloat($('#toplamhamsuretmp').val()) || 0;
  var defaultIndart = parseFloat($('#toplamindarttmp').val()) || 0;
  var defaultAzart = parseFloat($('#toplamazarttmp').val()) || 0;
  var defaultEntindart = parseFloat($('#toplamentindarttmp').val()) || 0;
  var defaultKalan = parseFloat($('#toplamkalansuretmp').val()) || 0;

  var ana = defaultAna;
  var indart = defaultIndart;
  var azart = defaultAzart;
  var entindart = defaultEntindart;
  var kalan = defaultKalan;
  var subesayisi = $('#inceleneceksahasayisi').val();

  // Loop through each row in the tbody
  $('#denetimZamanHesaplari tr').each(function () {
    var row = $(this);

    // console.log("ana1:::" + ana);
    // Check if the row contains a checkbox and if it is checked
    for (var i = 1; i <= subesayisi; i++) {
      var checkbox = row.find('input[id^="chkSube"][type="checkbox"]');
      // console.log("ana2:::" + ana);
      // console.log(checkbox.attr("id"), checkbox.is(':checked'));
      if (checkbox.length > 0 && checkbox.is(':checked')) {
        // console.log("ana3:::" + ana);
        // Add the values of the corresponding inputs to the totals
        ana += parseFloat(row.find('input[name$="hamsure' + i + '"]').val()) || 0;
        indart += parseFloat(row.find('input[name$="indart' + i + '"]').val()) || 0;
        azart += parseFloat(row.find('input[name$="azartsure' + i + '"]').val()) || 0;
        entindart += parseFloat(row.find('input[name$="entindart' + i + '"]').val()) || 0;
        kalan += parseFloat(row.find('input[name$="kalansure' + i + '"]').val()) || 0;
      }
    }
  });
  kalan = parseFloat(roundNearest5(kalan));

  // Calculate Aşama 1, Aşama 2, Gözetim, and Yeniden Belgelendirme based on updated 'kalan'
  var a1sure = parseFloat(kalan * 30 / 100);
  var a2sure = parseFloat(kalan * 70 / 100);
  var gozsure = parseFloat(roundNearest5(kalan / 3));
  var ybsure = parseFloat(roundNearest5(kalan * 2 / 3));

  // Update the total inputs in the footer
  $('#toplamhamsure').val(ana.toFixed(1));
  $('#toplamindart').val(indart.toFixed(1));
  $('#toplamazart').val(azart.toFixed(1));
  $('#toplamentindart').val(entindart.toFixed(1));
  $('#toplamkalansure').val(kalan.toFixed(1));

  $('#toplama1sure').val(a1sure.toFixed(1));
  $('#toplama2sure').val(a2sure.toFixed(1));
  $('#toplamgsure').val(gozsure.toFixed(1));
  $('#toplamybsure').val(ybsure.toFixed(1));
}

function runBgysCalculation(){

  var iso27001varyok = $('#iso27001varyok').val();
  if (iso27001varyok === '1') {
    isFaktorEtkiHesapla();
    btFaktorEtkiHesapla();
    bgysFaktorEtkiHesapla();
  }
}

