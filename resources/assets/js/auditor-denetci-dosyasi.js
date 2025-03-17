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

// Geliştirilmiş Dropzone önizleme şablonu
const previewTemplateDenetci = `
<div class="dz-preview dz-file-preview">
  <div class="card mb-2">
    <div class="card-body p-3">
      <div class="d-flex align-items-center">
        <div class="file-preview me-3">
          <img data-dz-thumbnail class="rounded" width="48" height="48" onerror="this.src='${baseUrl}assets/img/icons/misc/file-icon.png'; this.onerror=null;">
        </div>
        <div class="file-info flex-grow-1">
          <h6 class="mb-1" data-dz-name></h6>
          <div class="d-flex align-items-center gap-3">
            <small class="text-muted" data-dz-size></small>
            <div class="dz-progress w-50">
              <span class="dz-upload progress progress-bar-primary" role="progressbar" style="height: 6px;">
                <span class="progress-bar bg-primary" role="progressbar" data-dz-uploadprogress style="width: 0%"></span>
              </span>
            </div>
          </div>
        </div>
        <div class="file-actions ms-2">
          <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-dz-remove>
            <i class="mdi mdi-delete-outline mdi-20px"></i>
          </button>
        </div>
      </div>
      <div class="dz-error-message mt-1"><small class="text-danger" data-dz-errormessage></small></div>
    </div>
  </div>
</div>`;

// Dropzone konfigürasyonu ve başlatma
$(document).ready(function() {
  if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;

    const dropzoneMulti = document.getElementById('dropzone-multi-denetci');
    if (dropzoneMulti && !dropzoneMulti.dropzone) {
      const klasor = $('#name').val();
      var myDropzone = new Dropzone("#dropzone-multi-denetci", {
        previewTemplate: previewTemplateDenetci,
        previewsContainer: "#dropzone-previews",
        clickable: ".dropzone-area",
        dictDefaultMessage: "Dosyalarınızı buraya sürükleyin veya tıklayın",
        dictRemoveFile: "Dosyayı Sil",
        dictCancelUpload: "Yüklemeyi İptal Et",
        dictMaxFilesExceeded: "Maksimum dosya sayısını aştınız.",
        dictFileTooBig: "Dosya çok büyük ({{filesize}}MB). Maksimum dosya boyutu: {{maxFilesize}}MB.",
        dictInvalidFileType: "Bu dosya tipini yükleyemezsiniz.",
        parallelUploads: 3,
        maxFilesize: 10, // MB
        autoProcessQueue: false, // Otomatik yüklemeyi devre dışı bırak
        acceptedFiles: 'image/jpeg,image/png,image/gif,image/svg+xml,application/pdf',
        addRemoveLinks: true,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        init: function () {
          // Dropzone'u global scope'a ekleyelim
          window.myDropzone = this;

          // Önizleme konteynerini yönet
          this.on("addedfile", function(file) {
            // Önizleme konteynerini göster
            $("#preview-container").removeClass("d-none");

            // Resim dosyaları için önizleme ayarlamaları
            if (file.type.startsWith('image/')) {
              // Resim dosyaları için normal önizleme yeterli
            } else {
              // PDF dosyaları için varsayılan ikon kullan
              $(file.previewElement).find('[data-dz-thumbnail]').attr('src', baseUrl + 'assets/img/icons/misc/pdf-icon.png');
            }
          });

          // Tüm dosyalar silindiğinde konteyner gizlensin
          this.on("removedfile", function() {
            if (this.files.length === 0) {
              $("#preview-container").addClass("d-none");
            }
          });

          // Dosya gönderimi sırasında gerekli alanları ekle
          this.on("sending", function (file, xhr, formData) {
            formData.append("klasor", klasor);
            formData.append("altklasor", $('#altklasor').val());
            formData.append("uid", $('#uid').val());
          });

          // Başarılı yükleme sonrası
          this.on("success", function (file, response) {
            // Başarılı mesajı göster
            toastr.success('Dosya başarıyla yüklendi', 'Başarılı', {
              closeButton: true,
              tapToDismiss: false,
              progressBar: true
            });

            // Yükleme tamamlandıktan 1.5 saniye sonra Offcanvas'ı kapat
            setTimeout(function() {
              $('#offcanvasDenetcidosyasiUpload').offcanvas('hide');
              // Buton ikonlarını güncelle
              initButtonIcons();
            }, 1500);
          });

          // Yükleme hatası
          this.on("error", function (file, errorMessage) {
            // Hata mesajı göster
            toastr.error(errorMessage, 'Hata', {
              closeButton: true,
              tapToDismiss: false,
              progressBar: true
            });
          });

          // Yükleme tamamlandı
          this.on("queuecomplete", function () {
            // İsteğe bağlı: Yükleme tamamlandığında bir şey yapabilirsiniz
          });
        }
      });

      // Yükleme butonuna tıklandığında
      document.getElementById('uploadButton').addEventListener('click', function(e) {
        e.preventDefault();

        if (myDropzone.files.length === 0) {
          // Dosya seçilmemişse uyarı göster
          toastr.warning('Lütfen yüklemek için dosya seçiniz', 'Uyarı', {
            closeButton: true,
            tapToDismiss: false
          });
          return;
        }

        // Dosya seçildiyse yükleme işlemini başlat
        myDropzone.processQueue();
        setupDropzoneEvents(myDropzone);

      });
    }
  }
});

//denetciAtamaEkleForm
$(document).ready(function(){

  // Sayfa hazır olduğunda atama kontrolünü çalıştır
  setTimeout(atamaKontrolu, 500); // Diğer DOM işlemleri tamamlandıktan sonra
});

// --------------------------------------------------------------------
// Tabloların gizle/göster işlemleri (radiobuttonlara göre)
// --------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
  // Initial setup - tüm tabloları gizle
  hideAllTables();

  // Initial radio seçimine göre ilgili tabloları göster
  setupInitialVisibility();

  // Tüm radio butonlarına event listener ekle
  setupRadioListeners();

  function hideAllTables() {
    const tables = [
      'iso9001Table',
      'iso14001Table',
      'iso45001Table',
      'iso50001-table',
      'iso27001-table',
      'iso22000-table',
      'oicSmiic1Table',
      'oicSmiic6Table',
      'oicSmiic9Table',
      'oicSmiic171Table',
      'oicSmiic23Table',
      'oicSmiic24Table'
    ];

    tables.forEach(tableId => {
      const table = document.getElementById(tableId);
      if (table) {
        table.closest('.table-responsive').style.display = 'none';
      }
    });

    // hr elementlerini de gizle
    const hrs = document.querySelectorAll('.card-body > hr');
    hrs.forEach(hr => {
      hr.style.display = 'none';
    });
  }

  function setupInitialVisibility() {
    const standards = [
      'iso9001',
      'iso14001',
      'iso45001',
      'iso50001',
      'iso27001',
      'iso22000',
      'oicsmiic1',
      'oicsmiic6',
      'oicsmiic9',
      'oicsmiic171',
      'oicsmiic23',
      'oicsmiic24'
    ];

    standards.forEach(standard => {
      const varRadio = document.getElementById(`${standard}_var`);
      if (varRadio && varRadio.checked) {
        showRelevantTable(standard);
      }
    });
  }

  function setupRadioListeners() {
    const radios = document.querySelectorAll('input[type="radio"]');
    radios.forEach(radio => {
      radio.addEventListener('change', function() {
        const standardName = this.name;
        const value = this.value;

        if (value === 'var') {
          showRelevantTable(standardName);
        } else {
          hideRelevantTable(standardName);
        }
      });
    });
  }

  function showRelevantTable(standard) {
    const tableMap = {
      'iso9001': 'iso9001Table',
      'iso14001': 'iso14001Table',
      'iso45001': 'iso45001Table',
      'iso50001': 'iso50001-table',
      'iso27001': 'iso27001-table',
      'iso22000': 'iso22000-table',
      'oicsmiic1': 'oicSmiic1Table',
      'oicsmiic6': 'oicSmiic6Table',
      'oicsmiic9': 'oicSmiic9Table',
      'oicsmiic171': 'oicSmiic171Table',
      'oicsmiic23': 'oicSmiic23Table',
      'oicsmiic24': 'oicSmiic24Table'
    };

    const tableId = tableMap[standard];
    if (tableId) {
      const table = document.getElementById(tableId);
      if (table) {
        table.closest('.table-responsive').style.display = 'block';
        const tableContainer = table.closest('.table-responsive');
        const nextHr = tableContainer.nextElementSibling;
        if (nextHr && nextHr.tagName === 'HR') {
          nextHr.style.display = 'block';
        }
      }
    }
  }

  function hideRelevantTable(standard) {
    const tableMap = {
      'iso9001': 'iso9001Table',
      'iso14001': 'iso14001Table',
      'iso45001': 'iso45001Table',
      'iso50001': 'iso50001-table',
      'iso27001': 'iso27001-table',
      'iso22000': 'iso22000-table',
      'oicsmiic1': 'oicSmiic1Table',
      'oicsmiic6': 'oicSmiic6Table',
      'oicsmiic9': 'oicSmiic9Table',
      'oicsmiic171': 'oicSmiic171Table',
      'oicsmiic23': 'oicSmiic23Table',
      'oicsmiic24': 'oicSmiic24Table'
    };

    const tableId = tableMap[standard];
    if (tableId) {
      const table = document.getElementById(tableId);
      if (table) {
        table.closest('.table-responsive').style.display = 'none';
        const tableContainer = table.closest('.table-responsive');
        const nextHr = tableContainer.nextElementSibling;
        if (nextHr && nextHr.tagName === 'HR') {
          nextHr.style.display = 'none';
        }
      }
    }
  }

  // Buton ikonlarını yönet
  initButtonIcons();

  // Dropzone başarılı yükleme sonrası ikonu güncelle
  // setupDropzoneEvents();
});

// Öncelikle inputların stil düzenlemesi
document.addEventListener('DOMContentLoaded', function() {
  // Stil tanımlamaları
  const style = document.createElement('style');
  style.textContent = `
    .danismanlik-input-container {
      display: flex;
      width: 100%;
      gap: 4px;
    }
    .danismanlik-gun-input {
      width: 40%;
    }
    .danismanlik-yil-input {
      width: 60%;
      background-color: #f8f8f8;
    }
  `;
  document.head.appendChild(style);

  // Danışmanlık hesaplama işlevi
  function setupDanismanlikCalculation() {
    // Tüm danışmanlık gün inputlarını seç
    const gunInputs = document.querySelectorAll('input[name*="_danismanlikTecrubesiGun"]');

    gunInputs.forEach(function(gunInput) {
      // Eğer bu input zaten işlendiyse atlayalım
      if (gunInput.hasAttribute('data-initialized')) {
        return;
      }

      // İlgili standart ve indeksi bul
      const nameParts = gunInput.name.match(/([a-zA-Z0-9]+)_danismanlikTecrubesiGun(\d+)/);
      if (!nameParts || nameParts.length < 3) return;

      const standard = nameParts[1];
      const index = nameParts[2];

      // Yıl inputunu bul
      const yilInput = document.querySelector(`input[name='${standard}_danismanlikTecrubesi${index}']`);
      if (!yilInput) return;

      // Container oluştur
      const cell = gunInput.closest('td');
      if (!cell) return;

      // Hücre içinde container yoksa oluştur
      if (!cell.querySelector('.danismanlik-input-container')) {
        const container = document.createElement('div');
        container.className = 'danismanlik-input-container';

        // Input'ları container'a taşı
        if (gunInput.parentNode === cell) {
          gunInput.parentNode.removeChild(gunInput);

          if (yilInput.parentNode === cell) {
            yilInput.parentNode.removeChild(yilInput);
          }

          // Stil sınıflarını ekle
          gunInput.classList.add('danismanlik-gun-input');
          yilInput.classList.add('danismanlik-yil-input');

          // Placeholder ekle
          gunInput.placeholder = 'Gün';
          yilInput.placeholder = 'Yıl';
          yilInput.readOnly = true;

          // Input'ları container'a ekle
          container.appendChild(gunInput);
          container.appendChild(yilInput);

          // Container'ı hücreye ekle
          cell.appendChild(container);
        }
      }

      // Gün inputuna event listener ekle
      gunInput.addEventListener('input', function() {
        // Sadece sayısal değerlere izin ver
        this.value = this.value.replace(/[^0-9]/g, '');

        // Boş değer kontrolü
        if (this.value === '') {
          yilInput.value = '';
          return;
        }

        // Gün değerini sayıya çevir
        const gunDegeri = parseInt(this.value, 10);

        // Yıl hesaplama: 50 gün = 1 yıl
        const yilDegeri = (gunDegeri / 50).toFixed(1);

        // Hesaplanan değeri yıl inputuna yaz
        yilInput.value = yilDegeri;
      });

      // Başlangıçta değer varsa hesapla
      if (gunInput.value !== '') {
        const event = new Event('input');
        gunInput.dispatchEvent(event);
      }

      // Input işlendi olarak işaretle
      gunInput.setAttribute('data-initialized', 'true');
    });
  }

  // Sayfa yüklendiğinde hesaplamaları kur
  setupDanismanlikCalculation();

  // MutationObserver ile dinamik eklenen satırları izle
  const observer = new MutationObserver(function(mutations) {
    setupDanismanlikCalculation();
  });

  // Tüm tabloları izle
  const tables = document.querySelectorAll('table');
  tables.forEach(function(table) {
    observer.observe(table, { childList: true, subtree: true });
  });

  // Satır ekleme butonlarına tıklandığında da hesaplamaları tekrar kur
  const addRowButtons = document.querySelectorAll('.add-row-btn, [id^="addIso"], [id^="addOicSmiic"]');
  addRowButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      // Satır eklendikten sonra işlem yapmak için setTimeout kullan
      setTimeout(function() {
        setupDanismanlikCalculation();
      }, 100);
    });
  });
});

// İlgili butonu kontrol etmek için JavaScript kodu
function atamaKontrolu() {
  // 10. satırdaki Personel Atama Formu butonunu seçelim
  const atamaButonu = document.querySelector('button[name="kararggj"]');
  if (!atamaButonu) return; // Buton yoksa işlemi sonlandır

  // Denetçi ID'sini alalım
  const uid = document.getElementById('uid').value;
  if (!uid) return; // ID yoksa işlemi sonlandır

  // AJAX ile denetci_atamalar tablosunda kayıt kontrolü yapalım
  $.ajax({
    url: baseUrl + 'denetci/atama/kontrol',
    method: 'POST',
    data: {
      uid: uid,
      _token: $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
      if (response.hasRecords) {
        // Kayıt varsa ikonu değiştir
        updateButtonIcon(atamaButonu, true);

        // İsteğe bağlı: Güncelleme tarihini ekle
        if (response.lastUpdated) {
          const dateCell = atamaButonu.closest('tr').querySelector('td:nth-child(3)');
          if (dateCell) {
            dateCell.textContent = response.lastUpdated;
          }
        }
      } else {
        // Kayıt yoksa varsayılan ikon
        updateButtonIcon(atamaButonu, false);
      }
    },
    error: function(xhr, status, error) {
      console.error("Atama kontrolü hatası:", error);
      // Hata durumunda varsayılan ikonu koruyalım
      updateButtonIcon(atamaButonu, false);
    }
  });
}

// auditor-denetci-dosyasi.js içindeki initButtonIcons fonksiyonuna eklenecek kod
function initButtonIcons() {
  // Tüm denetçi dosya butonlarını seçelim
  const denetciButtons = document.querySelectorAll('#denetciDosyaIcerigiForm .btn-icerik-form');

  // Her buton için dosya kontrolü yapalım
  denetciButtons.forEach(button => {
    // Personel Atama Formu butonu için ayrı işlem yapalım
    if (button.getAttribute('name') === 'kararggj') {
      // Bu buton için özel kontrol zaten atamaKontrolu() fonksiyonunda yapılıyor
      return;
    }

    // İlgili klasör bilgilerini alalım
    const klasorName = document.getElementById('name').value; // Denetçi adı
    const altklasor = button.getAttribute('data-upload-altklasor');
    const uid = document.getElementById('uid').value;

    // Sadece data-upload-altklasor özelliği olan butonları işleme alalım
    if (altklasor) {
      // AJAX ile dosya kontrolü yapalım
      $.ajax({
        url: baseUrl + 'denetci/dosya/kontrol',
        method: 'POST',
        data: {
          klasor: klasorName,
          altklasor: altklasor,
          uid: uid,
          _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          // Eğer dosya varsa ikonu değiştir
          if (response.fileExists) {
            updateButtonIcon(button, true);

            // Güncelleme tarihini de ekleyelim (varsa)
            if (response.lastModified) {
              const dateCell = button.closest('tr').querySelector('td:nth-child(3)');
              if (dateCell) {
                dateCell.textContent = response.lastModified;
              }
            }
          } else {
            updateButtonIcon(button, false);
          }
        },
        error: function(xhr, status, error) {
          console.error("Dosya kontrol hatası:", error);
          // Hata durumunda varsayılan ünlem ikonunu göster
          updateButtonIcon(button, false);
        }
      });
    }
  });
}

// Dropzone olaylarını dinle
function setupDropzoneEvents(myDropzone) {
  // Dropzone nesnesini global scope'da ara
  if (myDropzone) {
    myDropzone.on("success", function(file, response) {
      // Yükleme başarılı olduğunda, hangi buton için yükleme yapıldığını belirle
      const offcanvas = document.getElementById('offcanvasDenetcidosyasiUpload');
      const altklasor = offcanvas.querySelector('#altklasor').value;

      // İlgili butonu bul ve ikonunu güncelle
      const denetciButtons = document.querySelectorAll('#denetciDosyaIcerigiForm .btn-icerik-form');
      denetciButtons.forEach(button => {
        if (button.getAttribute('data-upload-altklasor') === altklasor) {
          // Buton ikonunu güncelle
          updateButtonIcon(button, true);

          // Güncelleme tarihini ekle (bugünün tarihi)
          const dateCell = button.closest('tr').querySelector('td:nth-child(3)');
          if (dateCell) {
            const today = new Date();
            dateCell.textContent = today.toLocaleDateString('tr-TR');
          }
        }
      });

      // İsteğe bağlı: Başarılı yükleme mesajı göster
      Swal.fire({
        title: 'Başarılı!',
        text: 'Dosya başarıyla yüklendi',
        icon: 'success',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    });

    myDropzone.on("error", function(file, errorMessage) {
      // Yükleme hatası göster
      Swal.fire({
        title: 'Hata!',
        text: 'Dosya yüklenirken bir hata oluştu',
        icon: 'error',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    });
  }
}

// --------------------------------------------------------------------
// Tabloya satır ekleme/silme işlemleri
// --------------------------------------------------------------------

// ISO 50001 ve ISO 27001 için tablo ekleme/silme işlemleri
function iso2750Atama() {
  // Mevcut kayıt varsa, sayaçları tablo içindeki satır sayısıyla başlatıyoruz.
  let rowIndex50001 = $('#iso50001-table tbody tr').length;
  let rowIndex27001 = $('#iso27001-table tbody tr').length;

  // ISO 50001 Yeni Satır Ekleme
  const addIso50001RowBtn = document.getElementById('addIso50001Row');
  if (addIso50001RowBtn) {
    addIso50001RowBtn.addEventListener('click', function () {
      rowIndex50001++; // Mevcut sayıya ekleme
      let newRow = `
      <tr>
          <td class="row-index-50001">${rowIndex50001}</td>
          <td><input type="text" name="iso50001_teknikAlan${rowIndex50001}" class="form-control"></td>
          <td><input type="text" name="iso50001_isTecrubesi${rowIndex50001}" class="form-control"></td>
          <td><input type="text" name="iso50001_danismanlikTecrubesi${rowIndex50001}" class="form-control"></td>
          <td><input type="text" name="iso50001_atamaReferansi${rowIndex50001}" class="form-control"></td>
          <td><button type="button" class="btn btn-danger btn-sm delete-row-50001">Sil</button></td>
      </tr>`;
      const tableBody = document.querySelector('#iso50001-table tbody');
      if (tableBody) {
        tableBody.insertAdjacentHTML('beforeend', newRow);
      }
    });
  }

  // ISO 27001 Yeni Satır Ekleme
  const addIso27001RowBtn = document.getElementById('addIso27001Row');
  if (addIso27001RowBtn) {
    addIso27001RowBtn.addEventListener('click', function () {
      rowIndex27001++; // Mevcut satır sayısına ekle
      let newRow = `
      <tr>
          <td class="row-index-27001">${rowIndex27001}</td>
          <td><input type="text" name="iso27001_teknikAlan${rowIndex27001}" class="form-control"></td>
          <td><input type="text" name="iso27001_teknolojikAlan${rowIndex27001}" class="form-control"></td>
          <td><input type="text" name="iso27001_isTecrubesi${rowIndex27001}" class="form-control"></td>
          <td><input type="text" name="iso27001_danismanlikTecrubesi${rowIndex27001}" class="form-control"></td>
          <td><input type="text" name="iso27001_atamaReferansi${rowIndex27001}" class="form-control"></td>
          <td><button type="button" class="btn btn-danger btn-sm delete-row-27001">Sil</button></td>
      </tr>`;
      const tableBody = document.querySelector('#iso27001-table tbody');
      if (tableBody) {
        tableBody.insertAdjacentHTML('beforeend', newRow);
      }
    });
  }

  // Satır silme (event delegation)
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('delete-row-50001')) {
      e.target.closest('tr').remove();
      updateRowNumbers('iso50001-table', 'row-index-50001');
    }
    if (e.target.classList.contains('delete-row-27001')) {
      e.target.closest('tr').remove();
      updateRowNumbers('iso27001-table', 'row-index-27001');
    }
  });

  function updateRowNumbers(tableId, rowClass) {
    let rows = document.querySelectorAll(`#${tableId} tbody tr`);
    let newIndex = 1;
    rows.forEach(row => {
      let indexCell = row.querySelector(`.${rowClass}`);
      if (indexCell) {
        indexCell.textContent = newIndex;
      }
      row.querySelectorAll('input').forEach(input => {
        let name = input.getAttribute('name');
        name = name.replace(/\d+$/, newIndex);
        input.setAttribute('name', name);
      });
      newIndex++;
    });
    if (tableId === 'iso50001-table') {
      rowIndex50001 = rows.length;
    } else {
      rowIndex27001 = rows.length;
    }
  }
}

// ISO 22000 için tablo ekleme/silme işlemleri
function iso22Atama() {
  // Mevcut satır sayısıyla başlatma (veritabanından gelen kayıtlar varsa)
  let rowIndex22000 = $('#iso22000-table tbody tr').length;

  const addIso22000RowBtn = document.getElementById('addIso22000Row');
  if (addIso22000RowBtn) {
    addIso22000RowBtn.addEventListener('click', function () {
      rowIndex22000++;
      let newRow = `
      <tr>
          <td class="row-index-22000">${rowIndex22000}</td>
          <td><input type="text" name="iso22000_kategori${rowIndex22000}" class="form-control"></td>
          <td><input type="text" name="iso22000_altKategori${rowIndex22000}" class="form-control"></td>
          <td><input type="text" name="iso22000_isTecrubesi${rowIndex22000}" class="form-control"></td>
          <td><input type="text" name="iso22000_danismanlikTecrubesi${rowIndex22000}" class="form-control"></td>
          <td><input type="text" name="iso22000_atamaReferansi${rowIndex22000}" class="form-control"></td>
          <td><button type="button" class="btn btn-danger btn-sm delete-row-22000">Sil</button></td>
      </tr>`;
      const tableBody = document.querySelector('#iso22000-table tbody');
      if (tableBody) {
        tableBody.insertAdjacentHTML('beforeend', newRow);
      }
    });
  }

  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('delete-row-22000')) {
      e.target.closest('tr').remove();
      updateRowNumbers('iso22000-table', 'row-index-22000');
    }
  });

  function updateRowNumbers(tableId, rowIndexClass) {
    let rows = document.querySelectorAll(`#${tableId} tbody tr`);
    let newIndex = 1;
    rows.forEach(row => {
      let indexCell = row.querySelector(`.${rowIndexClass}`);
      if (indexCell) {
        indexCell.textContent = newIndex;
      }
      row.querySelectorAll('input').forEach(input => {
        let name = input.getAttribute('name');
        name = name.replace(/\d+$/, newIndex);
        input.setAttribute('name', name);
      });
      newIndex++;
    });
    rowIndex22000 = rows.length;
  }
}

// OIC/SMIIC için tablo ekleme/silme işlemleri
function initializeOicSmiicTable(variant) {
  let tableId = `oicSmiic${variant}Table`;
  let addButtonId = `addOicSmiic${variant}Row`;
  let rowIndexClass = `row-index-oicSmiic${variant}`;
  // Mevcut satır sayısı ile başlat
  let rowIndex = $(`#${tableId} tbody tr`).length;

  const addButton = document.getElementById(addButtonId);
  if (addButton) {
    addButton.addEventListener('click', function () {
      rowIndex++;
      let newRow = `
      <tr>
        <td class="${rowIndexClass}">${rowIndex}</td>
        <td><input type="text" name="oicSmiic${variant}_kategori${rowIndex}" class="form-control"></td>
        <td><input type="text" name="oicSmiic${variant}_altKategori${rowIndex}" class="form-control"></td>
        <td><input type="text" name="oicSmiic${variant}_isTecrubesi${rowIndex}" class="form-control"></td>
        <td><input type="text" name="oicSmiic${variant}_danismanlikTecrubesi${rowIndex}" class="form-control"></td>
        <td><input type="text" name="oicSmiic${variant}_atamaReferansi${rowIndex}" class="form-control"></td>
        <td><button type="button" class="btn btn-danger btn-sm delete-row-oicSmiic${variant}">Sil</button></td>
      </tr>`;
      const tableBody = document.querySelector(`#${tableId} tbody`);
      if (tableBody) {
        tableBody.insertAdjacentHTML('beforeend', newRow);
      }
    });
  }

  document.addEventListener('click', function (e) {
    if (e.target.classList.contains(`delete-row-oicSmiic${variant}`)) {
      e.target.closest('tr').remove();
      updateRowNumbers(tableId, rowIndexClass);
    }
  });

  function updateRowNumbers(tableId, rowIndexClass) {
    let rows = document.querySelectorAll(`#${tableId} tbody tr`);
    let newIndex = 1;
    rows.forEach(row => {
      let indexCell = row.querySelector(`.${rowIndexClass}`);
      if (indexCell) {
        indexCell.textContent = newIndex;
      }
      row.querySelectorAll('input').forEach(input => {
        let name = input.getAttribute('name');
        name = name.replace(/\d+$/, newIndex);
        input.setAttribute('name', name);
      });
      newIndex++;
    });
    rowIndex = rows.length;
  }
}

function denetciDosyaIcerikKontrolu(sira) {
  const uid = $('#uid').val();  // The route from input field
  window.location.href = baseUrl + "denetci/ata/" + sira;
}

// --------------------------------------------------------------------
// DataTables ve diğer işlemler
// --------------------------------------------------------------------
$(function () {
  // DataTable örnekleri ve diğer AJAX işlemleri...
  var dt_user_audit_log_table = $('.user-audit-log'),
    statusObj = {
      1: { active: 2, title: 'Pending', class: 'bg-label-warning' },
      2: { active: 1, title: 'Active', class: 'bg-label-success' },
      3: { active: 0, title: 'Inactive', class: 'bg-label-secondary' }
    };

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  if (dt_user_audit_log_table.length) {
    var uid = $('#uid').val()
    var url = baseUrl + 'audit/log/' + uid;

    $('.user-audit-log thead tr').clone(true).appendTo('.user-audit-log thead');
    $('.user-audit-log thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_auditlog.column(i).search() !== this.value) {
          dt_auditlog.column(i).search(this.value).draw();
        }
      });
    });

    var dt_auditlog = dt_user_audit_log_table.DataTable({
      processing: true,
      serverSide: false,
      ajax: { url: url },
      columns: [
        { data: 'fake_id' },
        { data: 'kurulus' },
        { data: 'denetim_standardi' },
        { data: 'teknik_alan' },
        { data: 'denetim_tarihi' },
        { data: 'statu' },
        { data: 'denetim_tipi' },
        { data: 'denetim_gun' }
      ],
      columnDefs: [
        {
          searchable: false,
          orderable: true,
          targets: 0,
          render: function (data, type, full, meta) {
            return `<span>${full.fake_id}</span>`;
          }
        },
        {
          targets: 1,
          responsivePriority: 4,
          orderable: true,
          render: function (data, type, full, meta) {
            var $name = full['kurulus'];
            var $adres = full['adres'];
            var $id = full['id'];
            var userView = getUserViewUrl($id);
            var stateNum = Math.floor(Math.random() * 6);
            var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
            var $state = states[stateNum],
              $name = full['kurulus'],
              $adres = full['adres'],
              $initials = $name.match(/\b\w/g) || [],
              $output;
            $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
            $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';

            var $row_output =
              '<div class="d-flex justify-content-start align-items-center user-name">' +
              '<div class="avatar-wrapper">' +
              '<div class="avatar avatar-sm me-3">' +
              $output +
              '</div>' +
              '</div>' +
              '<div class="d-flex flex-column">' +
              '<span class="fw-medium">' + $name + '</span>' +
              '<span class="fw-small">' + $adres + '</span>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        // Diğer columnDefs...
      ],
      language: {
        "info": "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor",
        "infoEmpty": "Kayıt yok",
        "infoFiltered": "(_MAX_ kayıt içerisinden bulunan)",
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
        "searchPlaceholder": "Arayın..."
      },
      dom:
        '<"row mx-2"' +
        '<"col-md-2"<"me-3"l>>' +
        '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0 gap-3"fB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-primary dropdown-toggle mx-3',
          text: '<i class="mdi mdi-export-variant me-1"></i> <span class="d-none d-sm-inline-block">Dışarı Aktar</span>',
          buttons: [
            {
              extend: 'excelHtml5',
              autoFilter: true,
              text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
              className: 'dropdown-item',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else {
                        result = result + item.innerText;
                      }
                    });
                    return result;
                  }
                }
              }
            },
            // {
            //   extend: 'pdf',
            //   text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
            //   className: 'dropdown-item',
            //   exportOptions: {
            //     columns: [0, 1, 2, 3, 4, 5, 6, 7],
            //     format: {
            //       body: function (inner, coldex, rowdex) {
            //         if (inner.length <= 0) return inner;
            //         var el = $.parseHTML(inner);
            //         var result = '';
            //         $.each(el, function (index, item) {
            //           if (item.classList && item.classList.contains('user-name')) {
            //             result = result + item.lastChild.firstChild.textContent;
            //           } else if (item.innerText === undefined) {
            //             result = result + item.textContent;
            //           } else {
            //             result = result + item.innerText;
            //           }
            //         });
            //         return result;
            //       }
            //     }
            //   }
            // }
          ]
        }
      ],
      order: [[0, 'asc']],
      orderCellsTop: true,
      paging: false,
      scrollX: true,
      scrollY: '400px'
    });
  }

  if ($('#siteMonitoringTable').length) {
    var uid = $('#uid').val();
    var url = baseUrl + 'site/monitoring/' + uid;

    // Uyarı mesajlarını tablonun altında göstermek için bir container oluşturalım
    $('#siteMonitoringTable').parent().append('<div id="warningMessages" class="alert-container mt-3"></div>');

    // Önce thead kısmına tfoot ekleyelim - stiller için özel sınıflar ekledik
    $('#siteMonitoringTable').append(
      '<tfoot>' +
      '<tr class="table-active">' +
      '<th class="text-center align-middle fw-bold">Toplam</th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '<th class="text-center align-middle fw-bold"></th>' +
      '</tr>' +
      '</tfoot>'
    );

    // Footer stillerini tablo stiliyle eşleştirmek için CSS ekle
    $('head').append(`
      <style>
        #siteMonitoringTable tfoot {
          border-color: #1a202c;
        }
        #siteMonitoringTable tfoot th {
          border-color: #1a202c;
          background-color: rgba(67, 89, 113, 0.1);
          color: #566a7f;
          font-weight: 700 !important;
        }
      </style>
    `);

    var dt_site_monitoring = $('#siteMonitoringTable').DataTable({
      processing: true,
      serverSide: false,  // Sunucu tarafı işleme kapalı
      ajax: {
        url: url,
        dataSrc: function(json) {
          if (json.error) {
            // Hata varsa tabloya mesaj ekleyelim
            $('#siteMonitoringTable tbody').html(
              '<tr><td colspan="15" class="text-center">Kayıt bulunamadı</td></tr>'
            );
            return [];
          }
          return json.data;
        },
        error: function(xhr, error, thrown) {
          console.error("Ajax error: ", thrown);
          $('#siteMonitoringTable tbody').html(
            '<tr><td colspan="15" class="text-center">Veri alınamadı</td></tr>'
          );
        }
      },
      columns: [
        { data: 'sira_no' },
        { data: '9001_kritik' },
        { data: '9001_olmayan' },
        { data: '14001_kritik' },
        { data: '14001_olmayan' },
        { data: '45001_kritik' },
        { data: '45001_olmayan' },
        { data: '22000_yuksek' },
        { data: '22000_orta' },
        { data: 'iso50001' },
        { data: 'iso27001' },
        { data: 'oicsmiic' }
      ],
      columnDefs: [
        { targets: '_all', className: 'text-center align-middle' }
      ],
      language: {
        info: "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor",
        infoEmpty: "Kayıt yok",
        infoFiltered: "(_MAX_ kayıt içerisinden bulunan)",
        lengthMenu: "Sayfada _MENU_ kayıt göster",
        loadingRecords: "Yükleniyor...",
        processing: "İşleniyor...",
        zeroRecords: "Eşleşen kayıt bulunamadı",
        paginate: {
          first: "İlk",
          last: "Son",
          next: "Sonraki",
          previous: "Önceki"
        },
        search: "",
        searchPlaceholder: "Arayın..."
      },
      dom:
        '<"row mx-2"' +
        '<"col-md-2"<"me-3"l>>' +
        '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0 gap-3"fB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-primary dropdown-toggle mx-3',
          text: '<i class="mdi mdi-export-variant me-1"></i> <span class="d-none d-sm-inline-block">Dışarı Aktar</span>',
          buttons: [
            {
              extend: 'excelHtml5',
              autoFilter: true,
              text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
              className: 'dropdown-item',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else {
                        result = result + item.innerText;
                      }
                    });
                    return result;
                  }
                },
                footer: true // Excel'e footerı dahil etmek için
              },
              customize: function(xlsx) {
                // Excel dosyasında footer stillerini ayarla
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                $('row:last-child c', sheet).attr('s', '2'); // Kalın yazı stili
              }
            }
          ]
        }
      ],
      order: [[0, 'asc']],
      orderCellsTop: true,
      paging: true,
      pageLength: 25,
      ordering: true,
      scrollX: true,
      scrollY: '500px',
      scrollCollapse: true,
      drawCallback: function(settings) {
        // Her tablo çiziminden sonra footer hücrelerinin stil sınıflarını güçlendir
        $(this).find('tfoot th').addClass('fw-bold text-center');
      },
      footerCallback: function(row, data, start, end, display) {
        var api = this.api();

        // Uyarı mesajı container'ını temizleyelim
        $('#warningMessages').empty();

        // Standartlar ve sütun indeksleri
        var standards = [
          { name: 'ISO 9001', kritikCol: 1, nonKritikCol: 2 },
          { name: 'ISO 14001', kritikCol: 3, nonKritikCol: 4 },
          { name: 'ISO 45001', kritikCol: 5, nonKritikCol: 6 },
          { name: 'ISO 22000', kritikCol: 7, nonKritikCol: 8 }, // 22000 için yüksek ve orta sütunlar
          { name: 'ISO 50001', kritikCol: 9, nonKritikCol: null }, // Sadece tek sütun
          { name: 'ISO 27001', kritikCol: 10, nonKritikCol: null }, // Sadece tek sütun
          { name: 'OIC/SMIIC', kritikCol: 11, nonKritikCol: null }  // Sadece tek sütun
        ];

        // Her sütun için toplam hesapla
        for (var i = 1; i <= 11; i++) {
          // Sütundan veriyi çek ve parsing işlemi yap
          var colData = api.column(i, { search: 'applied' }).data();
          var total = 0;

          // Her satır için parantez içindeki kodları hariç sadece sayıları topla
          colData.each(function(data) {
            // Veriyi parse etmek için HTML elementlerini parçala
            if (data) {
              // Veriyi şu formattan çıkart: "3 (01, 02, 03)"
              var count = data.split(' ')[0];
              if (count && !isNaN(parseInt(count))) {
                total += parseInt(count);
              }
            }
          });

          // Toplamı footer'a aktar
          $(api.column(i).footer()).html(total);

          // Eşik değerlerini aşan hücrelerin arka plan rengini değiştir
          var $cell = $(api.column(i).footer());
          // Kritik sütunları için (indeks 1, 3, 5, 7, 9, 10, 11)
          if ([1, 3, 5, 7, 9, 10, 11].includes(i) && total >= 15) {
            $cell.css('background-color', 'rgba(255, 171, 0, 0.16)');
            $cell.css('color', '#ff9800');
          }
          // Kritik olmayan sütunlar için (indeks 2, 4, 6, 8)
          else if ([2, 4, 6, 8].includes(i) && total >= 30) {
            $cell.css('background-color', 'rgba(255, 171, 0, 0.16)');
            $cell.css('color', '#ff9800');
          }
        }

        // Standartlara göre uyarı mesajlarını kontrol et
        standards.forEach(function(standard) {
          var kritikTotal = parseInt($(api.column(standard.kritikCol).footer()).text()) || 0;
          var nonKritikTotal = standard.nonKritikCol ? (parseInt($(api.column(standard.nonKritikCol).footer()).text()) || 0) : 0;

          var warnings = [];

          // Kritik kod uyarısı (15 ve üzeri)
          if (kritikTotal >= 15) {
            warnings.push(`<b>${standard.name}</b> için kritik kodların sayısı ${kritikTotal} adet olup izleme eşiğini (15) geçmiştir.`);
          }

          // Non-kritik kod uyarısı (30 ve üzeri), eğer non-kritik sütun varsa
          if (standard.nonKritikCol && nonKritikTotal >= 30) {
            warnings.push(`<b>${standard.name}</b> için kritik olmayan kodların sayısı ${nonKritikTotal} adet olup izleme eşiğini (30) geçmiştir.`);
          }

          // Uyarıları ekleyelim
          warnings.forEach(function(warningText) {
            $('#warningMessages').append(
              `<div class="alert alert-warning alert-dismissible mb-2" role="alert">
                <div>${warningText}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>`
            );
          });
        });
      }
    });
  }

  if($('#offcanvasDenetcidosyasiUpload').length) {
    const offcanvasEl = document.getElementById('offcanvasDenetcidosyasiUpload');
    offcanvasEl.addEventListener('show.bs.offcanvas', function (event) {
      const triggerElement = event.relatedTarget;
      if (triggerElement) {
        const uid = $('#uid').val();
        const klasor = $('#name').val();
        const altklasor = triggerElement.getAttribute('data-upload-altklasor') || '';
        document.getElementById('klasor').value = klasor;
        document.getElementById('altklasor').value = altklasor;
        document.getElementById('uid').value = uid;
      }
      const formInside = offcanvasEl.querySelector('form');
      if (formInside) {
        formInside.reset();
      }
    });
  }

  // Birinci formun submit işlemi AJAX ile yapılıyor.
  $('#denetciEkleForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
      url: '/denetciUserKaydet',
      method: 'POST',
      data: formData,
      success: function(response) {
        console.log("Form başarıyla kaydedildi:", response);
        $('#denetciDosyaIcerigiForm').show();
      },
      error: function(xhr) {
        console.error("Kayıt hatası:", xhr.responseText);
      }
    });
  });

  $('.btn-submit').on('click', function() {
    $('#denetciEkleForm').submit();
  });

  if (document.getElementById('addIso50001Row') || document.getElementById('addIso27001Row')) {
    iso2750Atama();
  }

  if (document.getElementById('addIso22000Row')) {
    iso22Atama();
  }

  const oicVariants = ["1", "6", "9", "171", "23", "24"];
  oicVariants.forEach(function(variant) {
    if (document.getElementById(`addOicSmiic${variant}Row`)) {
      initializeOicSmiicTable(variant);
    }
  });
});

// Modified functions for form handling with proper radio button validation

// Main initialization when document is ready
$(document).ready(function() {
  // Select2 ve Bootstrap Select'i uygula
  initializeSelect2();

  // EA Select değişiminde NACE options güncelleme
  setupEAChangeListeners();

  // ISO 9001, 14001, 45001 için tablo satır ekleme/silme işlemleri
  iso91445Atama();

  // Diğer tablolar için satır ekleme işlemleri
  setupOtherTableHandlers();

  // Radio butonların değişiminde ilgili tabloları göster/gizle
  setupRadioListeners();

  // AJAX form gönderimi için ayarlar
  setupFormSubmission();
});

// Tüm selectlere Bootstrap Select uygula
function initializeSelect2() {
  if($('.ea-select').length) {
    $('.ea-select').select2({
      placeholder: "EA Kodu Seçin",
      allowClear: true
    });
  }

  if($('.selectpicker').length) {
    $('.selectpicker').selectpicker({
      liveSearch: true,
      actionsBox: true,
      selectAllText: 'Tümünü Seç',
      deselectAllText: 'Seçimi Temizle',
      noneSelectedText: 'NACE Kodu Seçin',
      selectedTextFormat: 'count > 20'
    });
  }
}

// EA Select değişiminde NACE options güncelleme event listener'larını ekle
function setupEAChangeListeners() {
  // Yeni satır eklerken - select elementleri dinle
  $(document).on('change', '.ea-select', function() {
    const standard = $(this).data('standard');
    const rowIndex = $(this).data('row');
    const eaValue = $(this).val();

    // İlgili NACE selecti bul
    const naceSelect = $(`select[name="${standard}_nace${rowIndex}"]`);

    // NACE seçeneklerini güncelle
    updateNACEOptions(naceSelect, eaValue);
  });
}

// NACE seçeneklerini güncelle ve xx.xx formatında olanları seçilebilir, diğerlerini disabled yap
function updateNACEOptions(naceSelect, eaValue) {
  // Mevcut seçimleri kaydedelim
  const currentSelections = $(naceSelect).val() || [];

  // Select içeriğini temizle
  $(naceSelect).empty();

  // Seçilen EA için NACE kodları varsa ekle
  if (eaValue && naceByEaData[eaValue]) {
    naceByEaData[eaValue].forEach(function(nace) {
      // NACE kodu formatını kontrol et: xx.xx şeklinde olmalı (5 karakter)
      const isValidFormat = /^\d{2}\.\d{2}$/.test(nace);

      // Option elementini oluştur
      const option = new Option(nace, nace, false, currentSelections.includes(nace));

      // Geçerli formatta değilse disabled yap
      if (!isValidFormat) {
        option.disabled = true;
      }

      $(naceSelect).append(option);
    });
  }

  // Bootstrap Select'i güncelle
  $(naceSelect).selectpicker('refresh');
}

// ISO 9001, 14001, 45001 için satır ekleme/silme işlemleri
function iso91445Atama() {
  $(".add-row-btn").click(function () {
    const standard = $(this).data("standard");
    // Satır sayısı, tablo boşsa 0+1=1, varsa mevcut satır sayısına göre
    const rowIndex = $(`#${standard}Table tbody tr`).length + 1;

    // EA Select için options
    let eaOptions = '<option value="">EA Kodu Seçin</option>';
    // EA options array'inden dinamik olarak generate edilir
    const eaOptionsArray = $('.ea-select option').map(function() {
      return {
        value: $(this).val(),
        text: $(this).text()
      };
    }).get();

    // Tekrarlanan değerleri çıkar
    const uniqueEaOptions = [];
    const uniqueEaValues = new Set();

    eaOptionsArray.forEach(function(option) {
      if (option.value && !uniqueEaValues.has(option.value)) {
        uniqueEaValues.add(option.value);
        uniqueEaOptions.push(option);
      }
    });

    // EA seçeneklerini hazırla
    uniqueEaOptions.forEach(function(option) {
      if (option.value) {
        eaOptions += `<option value="${option.value}">${option.text}</option>`;
      }
    });

    let newRow = `
      <tr>
          <td class="row-index">${rowIndex}</td>
          <td>
            <select name="${standard}_ea${rowIndex}" class="form-select ea-select" data-standard="${standard}" data-row="${rowIndex}">
              ${eaOptions}
            </select>
          </td>
          <td>
            <select name="${standard}_nace${rowIndex}" class="form-select nace-select selectpicker" data-standard="${standard}" data-row="${rowIndex}" multiple data-live-search="true">
              <option value="">NACE Kodu Seçin</option>
            </select>
          </td>
          <td><input type="text" name="${standard}_isTecrubesi${rowIndex}" class="form-control"></td>
          <td><input type="text" name="${standard}_danismanlikTecrubesiGun${rowIndex}" class="form-control"><input type="text" name="${standard}_danismanlikTecrubesi${rowIndex}" class="form-control" readonly></td>
          <td><input type="text" name="${standard}_atamaReferansi${rowIndex}" class="form-control"></td>
          <td>
              <button type="button" class="btn btn-danger btn-sm delete-row-btn" data-standard="${standard}">Sil</button>
          </td>
      </tr>`;

    $(`#${standard}Table tbody`).append(newRow);

    // Yeni eklenen satırın selectlerini initalize et
    const newSelect2 = $(`#${standard}Table tbody tr:last-child .ea-select`);
    newSelect2.select2({
      placeholder: "EA Kodu Seçin",
      allowClear: true
    });

    const newSelectpicker = $(`#${standard}Table tbody tr:last-child .selectpicker`);

    // Selectpicker'ı initialize etmek
    newSelectpicker.selectpicker({
      liveSearch: true,
      actionsBox: true,
      selectAllText: 'Tümünü Seç',
      deselectAllText: 'Seçimi Temizle',
      noneSelectedText: 'NACE Kodu Seçin',
      selectedTextFormat: 'count > 2'
    });

    // EA değişikliğinde NACE listesinin güncellenmesi için event dinleyici ekle
    newSelect2.on('change', function() {
      const standard = $(this).data('standard');
      const rowIndex = $(this).data('row');
      const eaValue = $(this).val();

      // İlgili NACE selecti bul
      const naceSelect = $(`select[name="${standard}_nace${rowIndex}"]`);

      // NACE seçeneklerini güncelle
      updateNACEOptions(naceSelect, eaValue);
    });

    // Satır numaralarını güncelle
    renumberRows(standard);
  });

  // Satır silme (event delegation)
  $(document).on("click", ".delete-row-btn", function () {
    const row = $(this).closest("tr");
    const standard = $(this).data("standard");
    row.remove();
    renumberRows(standard);
  });
}

// Satır numaralarını güncelle (ISO 9001, 14001, 45001 için)
function renumberRows(standard) {
  const tableId = `#${standard}Table`;
  let currentIndex = 1;
  $(`${tableId} tbody tr`).each(function () {
    $(this).find(".row-index").text(currentIndex);

    // Input ve selectlerin name özniteliklerini güncelle
    $(this).find("input, select").each(function () {
      const nameAttr = $(this).attr("name");
      if (nameAttr) {
        const updatedName = nameAttr.replace(/\d+$/, currentIndex);
        $(this).attr("name", updatedName);
      }
    });

    // data-row özniteliklerini de güncelle
    $(this).find(".ea-select, .nace-select").each(function() {
      $(this).data('row', currentIndex);
      $(this).attr('data-row', currentIndex);
    });

    currentIndex++;
  });

  // İlk satırın sil butonunu devre dışı bırak
  $(`${tableId} tbody tr:first-child .delete-row-btn`).prop('disabled', true);
  $(`${tableId} tbody tr:not(:first-child) .delete-row-btn`).prop('disabled', false);
}

// Radio butonların değişiminde ilgili tabloları göster/gizle
function setupRadioListeners() {
  // Tüm standartların radio butonları için döngü
  ['iso9001', 'iso14001', 'iso45001', 'iso50001', 'iso22000', 'iso27001',
    'oicsmiic1', 'oicsmiic6', 'oicsmiic9', 'oicsmiic171', 'oicsmiic23', 'oicsmiic24'].forEach(function(standard) {

    // Var/yok radio butonlarına event listener ekle
    $(`input[name="${standard}"]`).on('change', function() {
      // Standarda bağlı doğru tablo ID'sini belirle
      const tableId = standard.includes('iso') ? `#${standard}Table` : `#${standard}-table`;
      const isVar = $(this).val() === 'var';

      // Tablo container'ı bul (table-responsive div)
      const tableContainer = $(tableId).closest('.table-responsive');

      if (isVar) {
        // "Var" seçildi - tabloyu göster
        tableContainer.show();
      } else {
        // "Yok" seçildi - tabloyu gizle ve içindeki input'ları temizle
        tableContainer.hide();
        clearTableInputs(tableId);
      }
    });

    // Sayfa yüklendiğinde mevcut radio değerine göre tabloları göster/gizle
    $(`input[name="${standard}"]:checked`).trigger('change');
  });
}

// Tablonun tüm input ve select içeriklerini temizleme
function clearTableInputs(tableId) {
  // Input alanlarını temizle
  $(`${tableId} input[type="text"]`).val('');

  // Select2 selectlerini temizle
  $(`${tableId} select.ea-select`).val(null).trigger('change');

  // Bootstrap-select selectlerini temizle
  $(`${tableId} select.selectpicker`).val('').selectpicker('refresh');
}

// ISO 22000, 50001, 27001 ve OIC/SMIIC formları için ortak ekleme/silme fonksiyonları
function setupOtherTableHandlers() {
  // ISO 22000 için satır ekleme
  $("#addIso22000Row").click(function() {
    addTableRow('iso22000', ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi']);
  });

  // ISO 50001 için satır ekleme
  $("#addIso50001Row").click(function() {
    addTableRow('iso50001', ['teknikAlan', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi']);
  });

  // ISO 27001 için satır ekleme
  $("#addIso27001Row").click(function() {
    addTableRow('iso27001', ['teknikAlan', 'teknolojikAlan', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi']);
  });

  // OIC/SMIIC varyantları için satır ekleme
  $("#addOicSmiic1Row").click(function() {
    addTableRow('oicsmiic1', ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi']);
  });

  $("#addOicSmiic6Row").click(function() {
    addTableRow('oicsmiic6', ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi']);
  });

  $("#addOicSmiic9Row").click(function() {
    addTableRow('oicsmiic9', ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi']);
  });

  $("#addOicSmiic171Row").click(function() {
    addTableRow('oicsmiic171', ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi']);
  });

  $("#addOicSmiic23Row").click(function() {
    addTableRow('oicsmiic23', ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi']);
  });

  $("#addOicSmiic24Row").click(function() {
    addTableRow('oicsmiic24', ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi']);
  });

  // Satır silme için event delegation - tüm tablolar için
  $(document).on("click", "[class^='delete-row-']", function() {
    const row = $(this).closest("tr");
    const tableId = row.closest("table").attr('id');
    const standardMatch = tableId.match(/^(\w+)[-]?/);

    if (standardMatch && standardMatch[1]) {
      const standard = standardMatch[1];
      row.remove();
      renumberTableRows(standard);
    }
  });
}

// Genel satır ekleme fonksiyonu
function addTableRow(standard, fields) {
  const tableId = `#${standard}-table`;
  const rowIndex = $(`${tableId} tbody tr`).length + 1;

  let newRow = `
    <tr>
      <td class="row-index-${standard}">${rowIndex}</td>
  `;

  // Her alan için input oluştur
  fields.forEach(field => {
    newRow += `<td><input type="text" name="${standard}_${field}${rowIndex}" class="form-control"></td>`;
  });

  // Sil butonu ekle
  newRow += `
      <td>
        <button type="button" class="btn btn-danger btn-sm delete-row-${standard}">Sil</button>
      </td>
    </tr>
  `;

  $(`${tableId} tbody`).append(newRow);

  // Satırları yeniden numaralandır
  renumberTableRows(standard);
}

// Tablo satırlarını yeniden numaralandırma
function renumberTableRows(standard) {
  const tableId = `#${standard}-table`;
  let currentIndex = 1;

  $(`${tableId} tbody tr`).each(function() {
    $(this).find(`.row-index-${standard}`).text(currentIndex);

    // Input ve selectlerin name özniteliklerini güncelle
    $(this).find("input, select").each(function() {
      const nameAttr = $(this).attr("name");
      if (nameAttr) {
        const updatedName = nameAttr.replace(/\d+$/, currentIndex);
        $(this).attr("name", updatedName);
      }
    });

    currentIndex++;
  });

  // İlk satırın sil butonunu devre dışı bırak
  $(`${tableId} tbody tr:first-child .delete-row-${standard}`).prop('disabled', true);
  $(`${tableId} tbody tr:not(:first-child) .delete-row-${standard}`).prop('disabled', false);
}

// Form gönderim işlemi
function setupFormSubmission() {
  $('#denetciAtamaEkleForm').on('submit', function(e) {
    e.preventDefault();

    // "Yok" seçili olan formların input değerlerini temizle
    // Bu işlem gizlenmiş tablolardaki verilerin formla gönderilmesini engeller
    ['iso9001', 'iso14001', 'iso45001', 'iso50001', 'iso22000', 'iso27001',
      'oicsmiic1', 'oicsmiic6', 'oicsmiic9', 'oicsmiic171', 'oicsmiic23', 'oicsmiic24'].forEach(function(standard) {
      if ($(`input[name="${standard}"]:checked`).val() === 'yok') {
        // Standarda bağlı doğru tablo ID'sini belirle
        const tableId = standard.includes('iso') ? `#${standard}Table` : `#${standard}-table`;
        clearTableInputs(tableId);
      }
    });

    // Form verilerini hazırla
    var formData = new FormData();

    // Form içindeki tüm inputları al
    var inputs = $(this).find('input:not([type="radio"]:not(:checked)), select').not('.selectpicker');
    inputs.each(function() {
      if (this.name) {
        formData.append(this.name, $(this).val());
      }
    });

    // Radio butonları ekle
    var radioInputs = $(this).find('input[type="radio"]:checked');
    radioInputs.each(function() {
      if (this.name) {
        formData.append(this.name, $(this).val());
      }
    });

    // Selectpicker'ları manuel olarak ekle
    $('.selectpicker').each(function() {
      var name = $(this).attr('name');
      var values = $(this).val();

      if (values && values.length > 0) {
        // Seçili değerleri virgülle birleştirilmiş string olarak gönder
        formData.append(name, Array.isArray(values) ? values.join(',') : values);
      }
    });

    // CSRF token ekle
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    // AJAX ile formu gönder
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('.alert-success, .alert-danger').remove();

        if (response.success) {
          // Başarı mesajı
          var successHtml = '<div class="alert alert-success">' + response.success + '</div>';
          $('#denetciAtamaEkleForm').before(successHtml);

          // Sayfanın en üstüne scroll
          $('html, body').animate({
            scrollTop: 0
          }, 500);
        }

        if (response.error) {
          var errorHtml = '<div class="alert alert-danger"><ul>';
          $.each(response.error, function(key, messages) {
            errorHtml += '<li>' + messages + '</li>';
          });
          errorHtml += '</ul></div>';
          $('#denetciAtamaEkleForm').before(errorHtml);

          // Sayfanın en üstüne scroll
          $('html, body').animate({
            scrollTop: 0
          }, 500);
        }
      },
      error: function(xhr) {
        $('.alert-success, .alert-danger').remove();

        // Hata mesajlarını ekle
        var errorHtml = '<div class="alert alert-danger"><ul>';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          $.each(xhr.responseJSON.errors, function(key, messages) {
            errorHtml += '<li>' + messages[0] + '</li>';
          });
        } else {
          errorHtml += '<li>Bir hata oluştu.</li>';
        }
        errorHtml += '</ul></div>';
        $('#denetciAtamaEkleForm').before(errorHtml);

        // Sayfanın en üstüne scroll
        $('html, body').animate({
          scrollTop: 0
        }, 500);
      }
    });
  });
}

/**
 * File Preview Modal JavaScript
 */

// Global variables
let fullImagePreview = null;

document.addEventListener('DOMContentLoaded', function() {
  // File Preview Modal event listeners
  const filePreviewModal = document.getElementById('filePreviewModal');
  if (filePreviewModal) {
    filePreviewModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const folderName = button.getAttribute('data-folder-name');
      const subFolderName = button.getAttribute('data-subfolder-name');
      const modalTitle = document.getElementById('filePreviewModalTitle');

      // Update modal title
      if (modalTitle) {
        modalTitle.textContent = `${folderName} - ${subFolderName} Dosyaları`;
      }

      // Show loading indicator, hide empty message
      document.getElementById('filePreviewLoading').classList.remove('d-none');
      document.getElementById('filePreviewEmpty').classList.add('d-none');

      // Clear previous files
      const fileContainer = document.querySelector('.file-preview-container');
      fileContainer.innerHTML = ''; // Önceki öğeleri temizle

      // Fetch files from the server
      fetchFileList(folderName, subFolderName);
    });
  }

  // Close full image preview when clicking outside
  document.addEventListener('click', function(e) {
    if (fullImagePreview && e.target === fullImagePreview) {
      closeFullImagePreview();
    }
  });

  // Close full image preview with Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && fullImagePreview) {
      closeFullImagePreview();
    }
  });
});

/**
 * Fetch the list of files in a folder
 */
function fetchFileList(folderName, subFolderName) {
  // CSRF Token alımı
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  if (!csrfToken) {
    console.error('CSRF token bulunamadı!');
    showErrorState('CSRF token bulunamadı. Sayfayı yenileyin.');
    return;
  }

  // AJAX request
  $.ajax({
    url: baseUrl + 'denetci/dosya/liste',
    method: 'POST',
    data: {
      klasor: folderName,
      altklasor: subFolderName,
      _token: csrfToken
    },
    success: function(response) {
      // Hide loading indicator
      document.getElementById('filePreviewLoading').classList.add('d-none');

      const fileContainer = document.querySelector('.file-preview-container');

      // Check if response contains files
      if (response.files && response.files.length > 0) {
        // Hide empty message
        document.getElementById('filePreviewEmpty').classList.add('d-none');

        // Display files
        response.files.forEach(file => {
          const fileItem = createFilePreviewItem(file, folderName, subFolderName);
          fileContainer.appendChild(fileItem);
        });
      } else {
        // Show empty message if no files
        document.getElementById('filePreviewEmpty').classList.remove('d-none');
      }
    },
    error: function(xhr, status, error) {
      console.error("Dosya listesi alınırken hata:", error);
      showErrorState('Dosyalar yüklenirken bir hata oluştu. Lütfen tekrar deneyin.');
    }
  });
}

/**
 * Show error state in modal
 */
function showErrorState(message) {
  document.getElementById('filePreviewLoading').classList.add('d-none');
  const emptyEl = document.getElementById('filePreviewEmpty');
  emptyEl.classList.remove('d-none');

  const emptyText = emptyEl.querySelector('p');
  if (emptyText) {
    emptyText.textContent = message;
  }
}

/**
 * Create a file preview item element with thumbnail view
 */
function createFilePreviewItem(file, folderName, subFolderName) {
  const fileItem = document.createElement('div');
  fileItem.className = 'col-md-4 col-sm-6 file-preview-item';

  const fileExtension = getFileExtension(file.name);
  const isImage = isImageFile(fileExtension);
  const fileUrl = `${baseUrl}uploads/denetci/${turkishToEnglish(folderName)}/${turkishToEnglish(subFolderName)}/${file.name}`;

  // Format the file size
  const formattedSize = formatFileSize(file.size);

  // Thumbnail container with constrained size
  const thumbnailContainerStyle = 'height: 140px; display: flex; align-items: center; justify-content: center; overflow: hidden; background-color: #f8f9fa;';

  // Image element with constrained dimensions
  const thumbnailStyle = 'max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain;';

  // Create HTML structure for file preview
  if (isImage) {
    fileItem.innerHTML = `
      <div class="file-preview-icon" style="${thumbnailContainerStyle}">
        <div class="thumbnail-loading">
          <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
          </div>
        </div>
        <img src="${fileUrl}" alt="${file.name}" style="${thumbnailStyle}" class="thumbnail-image d-none">
      </div>
      <div class="file-preview-info">
        <div class="file-preview-name" title="${file.name}">${file.name}</div>
        <div class="file-preview-size">${formattedSize}</div>
        <div class="file-preview-action">
          <a href="${fileUrl}" class="file-preview-download btn btn-sm btn-outline-primary"
             download="${file.name}" onclick="event.stopPropagation();">
            <i class="mdi mdi-download me-1"></i>İndir
          </a>
        </div>
      </div>
    `;

    // Image load handling
    const img = fileItem.querySelector('.thumbnail-image');
    const loadingDiv = fileItem.querySelector('.thumbnail-loading');

    if (img && loadingDiv) {
      img.onload = function() {
        loadingDiv.remove();
        img.classList.remove('d-none');
      };

      img.onerror = function() {
        loadingDiv.innerHTML = '<i class="mdi mdi-image-broken text-muted mdi-24px"></i>';
      };
    }
  } else {
    // Display icon for non-image files
    fileItem.innerHTML = `
      <div class="file-preview-icon" style="${thumbnailContainerStyle}">
        <i class="mdi ${getFileIcon(fileExtension)} mdi-36px"></i>
      </div>
      <div class="file-preview-info">
        <div class="file-preview-name" title="${file.name}">${file.name}</div>
        <div class="file-preview-size">${formattedSize}</div>
        <div class="file-preview-action">
          <a href="${fileUrl}" class="file-preview-download btn btn-sm btn-outline-primary"
             download="${file.name}" onclick="event.stopPropagation();">
            <i class="mdi mdi-download me-1"></i>İndir
          </a>
        </div>
      </div>
    `;
  }

  // Add click event for preview
  if (isImage) {
    fileItem.addEventListener('click', function() {
      openFullImagePreview(fileUrl, file.name);
    });
  } else {
    fileItem.addEventListener('click', function() {
      window.open(fileUrl, '_blank');
    });
  }

  return fileItem;
}

/**
 * Get the appropriate icon for a file type
 */
function getFileIcon(extension) {
  const iconMap = {
    'pdf': 'mdi-file-pdf-box text-danger',
    'doc': 'mdi-file-word-box text-primary',
    'docx': 'mdi-file-word-box text-primary',
    'xls': 'mdi-file-excel-box text-success',
    'xlsx': 'mdi-file-excel-box text-success',
    'ppt': 'mdi-file-powerpoint-box text-warning',
    'pptx': 'mdi-file-powerpoint-box text-warning',
    'zip': 'mdi-zip-box text-info',
    'rar': 'mdi-zip-box text-info',
    'txt': 'mdi-file-document-outline text-secondary',
    'csv': 'mdi-file-delimited-outline text-success'
  };

  return iconMap[extension.toLowerCase()] || 'mdi-file-outline text-muted';
}

/**
 * Check if a file is an image based on extension
 */
function isImageFile(extension) {
  const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
  return imageExtensions.includes(extension.toLowerCase());
}

/**
 * Get the file extension from a filename
 */
function getFileExtension(filename) {
  return filename.split('.').pop();
}

/**
 * Format file size to human-readable format
 */
function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes';

  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));

  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Open full image preview within the modal
 */
function openFullImagePreview(imageUrl, imageName) {
  // Get the modal content element
  const modalContent = document.querySelector('#filePreviewModal .modal-content');
  const modalBody = document.querySelector('#filePreviewModal .modal-body');

  if (!modalContent || !modalBody) {
    console.error('Modal elements not found');
    return;
  }

  // Save the current state of the modal to restore later
  const originalContent = modalBody.innerHTML;
  const originalHeight = modalContent.style.height;
  const originalMaxHeight = modalContent.style.maxHeight;

  // Create fullscreen view container
  const fullscreenContainer = document.createElement('div');
  fullscreenContainer.className = 'fullscreen-image-container';
  fullscreenContainer.style.position = 'absolute';
  fullscreenContainer.style.top = '0';
  fullscreenContainer.style.left = '0';
  fullscreenContainer.style.width = '100%';
  fullscreenContainer.style.height = '100%';
  fullscreenContainer.style.backgroundColor = 'rgba(0, 0, 0, 0.9)';
  fullscreenContainer.style.zIndex = '1050';
  fullscreenContainer.style.display = 'flex';
  fullscreenContainer.style.alignItems = 'center';
  fullscreenContainer.style.justifyContent = 'center';

  // Create close button
  const closeButton = document.createElement('button');
  closeButton.className = 'btn btn-icon btn-sm btn-outline-light fullscreen-close-btn';
  closeButton.innerHTML = '<i class="mdi mdi-close"></i>';
  closeButton.style.position = 'absolute';
  closeButton.style.top = '15px';
  closeButton.style.right = '15px';
  closeButton.style.zIndex = '1051';
  closeButton.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
  closeButton.style.border = 'none';
  closeButton.style.borderRadius = '50%';
  closeButton.style.width = '32px';
  closeButton.style.height = '32px';
  closeButton.style.display = 'flex';
  closeButton.style.alignItems = 'center';
  closeButton.style.justifyContent = 'center';

  // Loading indicator
  const loadingSpinner = document.createElement('div');
  loadingSpinner.className = 'spinner-border text-light';
  loadingSpinner.style.position = 'absolute';
  loadingSpinner.style.top = '50%';
  loadingSpinner.style.left = '50%';
  loadingSpinner.style.transform = 'translate(-50%, -50%)';
  loadingSpinner.innerHTML = '<span class="visually-hidden">Yükleniyor...</span>';

  // Filename display
  const filenameDisplay = document.createElement('div');
  filenameDisplay.className = 'fullscreen-filename';
  filenameDisplay.textContent = imageName;
  filenameDisplay.style.position = 'absolute';
  filenameDisplay.style.bottom = '15px';
  filenameDisplay.style.left = '0';
  filenameDisplay.style.right = '0';
  filenameDisplay.style.textAlign = 'center';
  filenameDisplay.style.color = 'white';
  filenameDisplay.style.padding = '8px';
  filenameDisplay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
  filenameDisplay.style.fontSize = '14px';

  // Create image element
  const img = new Image();
  img.style.maxWidth = '90%';
  img.style.maxHeight = '80vh';
  img.style.objectFit = 'contain';
  img.style.display = 'none'; // Initially hide until loaded

  // Image load event
  img.onload = function() {
    loadingSpinner.style.display = 'none';
    img.style.display = 'block';
  };

  // Image error event
  img.onerror = function() {
    loadingSpinner.style.display = 'none';
    const errorMsg = document.createElement('div');
    errorMsg.textContent = 'Resim yüklenemedi';
    errorMsg.style.color = 'white';
    errorMsg.style.fontSize = '18px';
    fullscreenContainer.appendChild(errorMsg);
  };

  img.src = imageUrl;
  img.alt = imageName;

  // Close button event
  closeButton.addEventListener('click', function() {
    // Restore original modal content
    modalBody.innerHTML = originalContent;
    modalContent.style.height = originalHeight;
    modalContent.style.maxHeight = originalMaxHeight;

    // Rebind click events to thumbnails after restoring content
    document.querySelectorAll('.file-preview-item').forEach(item => {
      const imgElement = item.querySelector('.file-preview-icon img');
      if (imgElement) {
        const fileUrl = imgElement.src;
        const fileName = item.querySelector('.file-preview-name').textContent;

        item.addEventListener('click', function() {
          openFullImagePreview(fileUrl, fileName);
        });
      }
    });
  });

  // Prepare modal for fullscreen view
  modalContent.style.height = '90vh';
  modalContent.style.maxHeight = '90vh';

  // Clear modal body and add fullscreen view
  modalBody.innerHTML = '';

  // Append elements
  fullscreenContainer.appendChild(img);
  modalBody.appendChild(fullscreenContainer);
  modalBody.appendChild(closeButton);
  modalBody.appendChild(loadingSpinner);
  modalBody.appendChild(filenameDisplay);

  // Add ESC key handler
  const escKeyHandler = function(e) {
    if (e.key === 'Escape') {
      closeButton.click();
      document.removeEventListener('keydown', escKeyHandler);
    }
  };
  document.addEventListener('keydown', escKeyHandler);
}

/**
 * Close full image preview
 */
function closeFullImagePreview() {
  if (fullImagePreview) {
    document.body.removeChild(fullImagePreview);
    fullImagePreview = null;
    document.body.style.overflow = '';
  }
}

/**
 * Convert Turkish characters to English equivalent
 */
function turkishToEnglish(text) {
  if (!text) return '';

  const turkish = ['ç', 'Ç', 'ğ', 'Ğ', 'ı', 'İ', 'ö', 'Ö', 'ş', 'Ş', 'ü', 'Ü'];
  const english = ['c', 'C', 'g', 'G', 'i', 'I', 'o', 'O', 's', 'S', 'u', 'U'];

  let result = text;
  for (let i = 0; i < turkish.length; i++) {
    result = result.replaceAll(turkish[i], english[i]);
  }

  return result;
}

// İkon güncelleme fonksiyonu
function updateButtonIcon(button, fileExists) {
  const iconSpan = button.querySelector('span');
  if (iconSpan) {
    if (fileExists) {
      // Dosya varsa onay işareti
      iconSpan.className = 'tf-icons mdi mdi-checkbox-marked-circle-outline mdi-20px';
    } else {
      // Dosya yoksa ünlem işareti
      iconSpan.className = 'tf-icons mdi mdi-exclamation mdi-20px';
    }
  }

  // Butonun konumunu düzeltmek için:
  button.style.position = 'static';
  button.style.transform = 'none';
}
