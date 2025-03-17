/**
 *  Form Wizard
 */

'use strict';

(function () {
  let rowIndex = 3; // Sabit satır sayısı

  const select2 = $('.select2'),
    selectPicker = $('.selectpicker');

  iso50001ekformAc();
  iso27001ekformAc();

  $('#iso5000118varyok, #iso27001varyok, #tamzamanlicalisansayisi, #yarizamanlicalisansayisi, #taseroncalisansayisi, #gecicicalisansayisi, #vasifsizcalisansayisi, #enyscalisansayisi, #bgyscalisansayisi').on('input change', updateTotalEmployees);

  // Toplam çalışan sayısı elle değişince, checkbox durumuna göre enys/bgys'ye yaz
  $('#toplamcalisansayisi').on('input', handleManualTotalChange);

  // Wizard Validation
  // --------------------------------------------------------------------
  const wizardValidation = document.querySelector('#basvuru-wizard-validation');
  if (typeof wizardValidation !== undefined && wizardValidation !== null) {
    // Wizard form
    const wizardValidationForm = wizardValidation.querySelector('#basvuru-wizard-validation-form');
    // Wizard steps
    const wizardValidationFormStep1 = wizardValidationForm.querySelector('#company-details-validation');
    const wizardValidationFormStep2 = wizardValidationForm.querySelector('#personal-info-validation');
    const wizardValidationFormStep3 = wizardValidationForm.querySelector('#social-links-validation');
    // Wizard next prev button
    const wizardValidationNext = [].slice.call(wizardValidationForm.querySelectorAll('.btn-next'));
    const wizardValidationPrev = [].slice.call(wizardValidationForm.querySelectorAll('.btn-prev'));

    const validationStepper = new Stepper(wizardValidation, {
      linear: true
    });

    // Account details
    const FormValidation1 = FormValidation.formValidation(wizardValidationFormStep1, {
      fields: {
        formValidationFirmaadi: {
          validators: {
            notEmpty: {
              message: 'Kuruluş adı zorunludur.'
            }
          }
        },
        formValidationFirmaadresi: {
          validators: {
            notEmpty: {
              message: 'Kuruluş adresi zorunludur.'
            }
          }
        },
        formValidationIlce: {
          validators: {
            notEmpty: {
              message: 'İlçe zorunludur.'
            }
          }
        },
        formValidationSehir: {
          validators: {
            notEmpty: {
              message: 'Şehir zorunludur.'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      },
      init: instance => {
        instance.on('plugins.message.placed', function (e) {
          //* Move the error message out of the `input-group` element
          if (e.element.parentElement.classList.contains('input-group')) {
            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          }
        });
      }
    }).on('core.form.valid', function () {
      // Jump to the next step when all fields in the current step are valid
      validationStepper.next();
    });

    // Personal info
    const FormValidation2 = FormValidation.formValidation(wizardValidationFormStep2, {
      fields: {
        formValidationFirstName: {
          validators: {
            notEmpty: {
              message: 'The first name is required'
            }
          }
        },
        formValidationLastName: {
          validators: {
            notEmpty: {
              message: 'The last name is required'
            }
          }
        },
        formValidationCountry: {
          validators: {
            notEmpty: {
              message: 'The Country is required'
            }
          }
        },
        formValidationLanguage: {
          validators: {
            notEmpty: {
              message: 'The Languages is required'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      }
    }).on('core.form.valid', function () {
      // Jump to the next step when all fields in the current step are valid
      validationStepper.next();
    });

    // Bootstrap Select (i.e Language select)
    if (selectPicker.length) {
      selectPicker.each(function () {
        var $this = $(this);
        $this.selectpicker().on('change', function () {
          FormValidation2.revalidateField('formValidationLanguage');
        });
      });
      handleBootstrapSelectEvents();
    }

    // select2
    if (select2.length) {
      select2.each(function () {
        var $this = $(this);
        select2Focus($this);
        $this.wrap('<div class="position-relative"></div>');
        $this
          .select2({
            placeholder: 'Select an country',
            dropdownParent: $this.parent()
          })
          .on('change.select2', function () {
            // Revalidate the color field when an option is chosen
            FormValidation2.revalidateField('formValidationCountry');
          });
      });
    }

    // Social links
    const FormValidation3 = FormValidation.formValidation(wizardValidationFormStep3, {
      fields: {
        formValidationTwitter: {
          validators: {
            notEmpty: {
              message: 'The Twitter URL is required'
            },
            uri: {
              message: 'The URL is not proper'
            }
          }
        },
        formValidationFacebook: {
          validators: {
            notEmpty: {
              message: 'The Facebook URL is required'
            },
            uri: {
              message: 'The URL is not proper'
            }
          }
        },
        formValidationGoogle: {
          validators: {
            notEmpty: {
              message: 'The Google URL is required'
            },
            uri: {
              message: 'The URL is not proper'
            }
          }
        },
        formValidationLinkedIn: {
          validators: {
            notEmpty: {
              message: 'The LinkedIn URL is required'
            },
            uri: {
              message: 'The URL is not proper'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      }
    }).on('core.form.valid', function () {
      // You can submit the form
      // wizardValidationForm.submit()
      // or send the form data to server via an Ajax request
      // To make the demo simple, I just placed an alert
      // alert('Submitted..!!');

      var form = $('#basvuru-wizard-validation-form');
      var submitted = false;

      var postData = form.serialize();
      var formURL = $("#formBasvuruRoute").val();

      // console.log("formKaydet::route:: " + formURL + "?");
      // console.log("formKaydet::postData:: " + postData);
      if (!submitted) {
        $.ajax({
          url: formURL,
          type: 'POST',
          data: postData,
          success: function (html) {
            var result = $.parseJSON(html);
            // console.log("formSubmitAjax: " + result["mesaj"]);

            if (result["hata"]) {

              $("#formkaydetsonucerror").html(result["mesaj"]);
              $('#myModalError').modal('show');

            } else {
              $("#formkaydetsonucsuccess").html(result["mesaj"]);
              $('#myModalSucces').modal('show');
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $("#formkaydetsonucerror").html('[formKaydet]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown + "<br>formKaydet: " + formURL + "?" + postData);
            // window.console.log("formKaydet: " + formURL + "?" + postData);
            $('#myModalError').modal('show');
          }
        });
        submitted = true;
      }
    });

    wizardValidationNext.forEach(item => {
      item.addEventListener('click', event => {
        // When click the Next button, we will validate the current step
        switch (validationStepper._currentIndex) {
          case 0:
            FormValidation1.validate();
            break;

          case 1:
            FormValidation2.validate();
            break;

          case 2:
            FormValidation3.validate();
            break;

          default:
            break;
        }
      });
    });

    wizardValidationPrev.forEach(item => {
      item.addEventListener('click', event => {
        switch (validationStepper._currentIndex) {
          case 2:
            validationStepper.previous();
            break;

          case 1:
            validationStepper.previous();
            break;

          case 0:

          default:
            break;
        }
      });
    });
  }

  $('#add-row-btn').click(function () {
    rowIndex++;
    const iso50001Checkbox = document.getElementById('iso5000118varyok');
    const vardSuffix = String.fromCharCode(96 + rowIndex); // d, e, f...
    let newRow = `
      <tr>
        <td class="row-index">${rowIndex}</td>
        <td><input type="text" name="subeadresi${rowIndex}" class="form-control" placeholder="" value=""/></td>
        <td><input type="text" name="subevard${vardSuffix}a" class="form-control" placeholder="" value=""/></td>
        <td><input type="text" name="subevard${vardSuffix}b" class="form-control" placeholder="" value=""/></td>
        <td><input type="text" name="subevard${vardSuffix}c" class="form-control" placeholder="" value=""/></td>
<td><input type="text" name="subefaaliyet${rowIndex}" class="form-control" placeholder="" value=""/></td>`;

    // ISO 50001 seçili ise ek sütunları ekle
    if (iso50001Checkbox.checked) {
      newRow += createExtraColumns(rowIndex);
    }

    newRow += `
        <td><button type="button" class="btn btn-danger btn-sm delete-row-btn">Sil</button></td>
      </tr>`;
    $('#dynamic-table tbody').append(newRow);
  });

  // Satır silme
  $(document).on('click', '.delete-row-btn', function () {
    $(this).closest('tr').remove();
    renumberRows(); // Sıra numaralarını yenile
  });

  // Sıra numaralarını yenile
  function renumberRows() {
    let currentIndex = 1; // Başlangıç sıra numarası
    $('#dynamic-table tbody tr').each(function () {
      $(this).find('.row-index').text(currentIndex); // Sıra numarasını güncelle

      // Input alanlarını güncelle
      $(this).find('input').each(function () {
        const nameAttr = $(this).attr('name');
        const idAttr = $(this).attr('id');

        if (nameAttr) {
          const updatedName = nameAttr.replace(/\d+/g, currentIndex); // Sayı kısmını güncelle
          $(this).attr('name', updatedName);
        }

        if (idAttr) {
          const updatedId = idAttr.replace(/\d+/g, currentIndex); // Sayı kısmını güncelle
          $(this).attr('id', updatedId);
        }
      });

      currentIndex++;
    });
    rowIndex = currentIndex - 1; // Güncellenen satır sayısı
  }
})();


// Satıra göre ID ve Name belirle
function createExtraColumns(rowIndex) {
  const suffix = String.fromCharCode(96 + rowIndex); // a, b, c...
  return `
      <td><input type="text" name="subetep${suffix}a" id="subetep${suffix}a" class="form-control" placeholder="" /></td>
      <td><input type="text" name="subeetsay${suffix}b" id="subeetsay${suffix}b" class="form-control" placeholder="" /></td>
      <td><input type="text" name="subeoek${suffix}c" id="subeoek${suffix}c" class="form-control" placeholder="" /></td>
    `;
}

function iso50001ekformAc(){
  const isChecked = $("#iso5000118varyok").is(":checked");

  if (isChecked){
    $('#divenysekform').show();
  }
  if (!isChecked){
    $('#divenysekform').hide();
  }
}

function iso27001ekformAc(){
  const isChecked = $("#iso27001varyok").is(":checked");

  if (isChecked){
    $('#divbgysekform').show();
  }
  if (!isChecked){
    $('#divbgysekform').hide();
  }
}

function updateTotalEmployees() {
  // 1) Temel çalışan sayısı (tam, yarı, taşeron, geçici, vasıfsız)
  let tamZamanli = parseInt($('#tamzamanlicalisansayisi').val()) || 0;
  let yariZamanli = parseInt($('#yarizamanlicalisansayisi').val()) || 0;
  let taseron = parseInt($('#taseroncalisansayisi').val()) || 0;
  let gecici = parseInt($('#gecicicalisansayisi').val()) || 0;
  let vasifsiz = parseInt($('#vasifsizcalisansayisi').val()) || 0;

  let baseTotal = tamZamanli + yariZamanli + taseron + gecici + vasifsiz;

  // 2) Eğer ENYS (iso5000118varyok) veya BGYS (iso27001varyok) işaretli ise,
  //    baseTotal'ın karekökünü alıp yukarı yuvarlıyoruz.
  //    Değilse baseTotal aynı kalabilir (isteğe göre).
  const isEnysChecked = $('#iso5000118varyok').is(':checked');
  const isBgysChecked = $('#iso27001varyok').is(':checked');

  let effectiveTotal = baseTotal;
  if (isEnysChecked || isBgysChecked) {
    effectiveTotal = Math.ceil(Math.sqrt(baseTotal));
  }

  // 3) ENYS için ayrıca eklenecek sayı
  if (isEnysChecked) {
    let enys = parseInt($('#enyscalisansayisi').val()) || 0;
    effectiveTotal += enys;
  }

  // 4) BGYS için ayrıca eklenecek sayı
  if (isBgysChecked) {
    let bgys = parseInt($('#bgyscalisansayisi').val()) || 0;
    effectiveTotal += bgys;
  }

  // 5) Sonucu ilgili alana yaz
  $('#toplamcalisansayisi').val(effectiveTotal);
}

// 2) Toplam çalışan sayısı MANUEL değişirse,
//    checkbox seçili durumlarına göre enys veya bgys alanına yaz.
function handleManualTotalChange() {
  // Kullanıcının girdiği toplam çalışan sayısı
  let manualTotal = parseInt($('#toplamcalisansayisi').val()) || 0;

  // ENYS aktif ise enyscalisansayisi = manualTotal
  if ($('#iso5000118varyok').is(':checked')) {
    $('#enyscalisansayisi').val(manualTotal);
  }

  // BGYS aktif ise bgyscalisansayisi = manualTotal
  if ($('#iso27001varyok').is(':checked')) {
    $('#bgyscalisansayisi').val(manualTotal);
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const iso50001Checkbox = document.getElementById('iso5000118varyok');
  const dynamicTable = document.getElementById('dynamic-table');

  // Ek sütunların HTML yapısını oluşturun
  const extraColumnsHeader = `
    <th class="extra-columns" style="width: 10%">Yıllık tüketim(TEP)</th>
    <th class="extra-columns" style="width: 10%">Enerji Tipi Sayısı</th>
    <th class="extra-columns" style="width: 10%">ÖEK Sayısı</th>
  `;

  // Tabloda ek sütunları göster/gizle
  function toggleExtraColumns() {
    const isChecked = iso50001Checkbox.checked;
    const tableHead = dynamicTable.querySelector('thead tr');
    const tableBody = dynamicTable.querySelectorAll('tbody tr');

    if (isChecked) {
      // Ek sütunları ekle
      if (!tableHead.querySelector('.extra-columns')) {
        tableHead.insertAdjacentHTML('beforeend', extraColumnsHeader);
        tableBody.forEach((row, index) => {
          row.insertAdjacentHTML('beforeend', createExtraColumns(index + 1));
        });
      }
    } else {
      // Ek sütunları kaldır
      // Ek sütunları kaldırırken, sadece satırda fazladan hücre varsa kaldırın
      tableBody.forEach(row => {
        const cells = row.querySelectorAll('td');
        // Temel tablonuz 6 sütunlu olacak şekilde ayarlandıysa, fazladan sütun varsa kaldırın
        if (cells.length > 6) {
          for (let i = 0; i < 3; i++) {
            row.lastElementChild.remove();
          }
        }
      });
    }
  }

  // İlk kontrol ve olay dinleyici
  toggleExtraColumns();
  iso50001Checkbox.addEventListener('change', toggleExtraColumns);
});

// Toplam çalışan sayısını hesaplayan fonksiyon
function updateEmployeeCount() {
  // Değerleri al ve sayıya çevir (geçersiz değerler için 0 kullan)
  var beyazYaka = parseFloat($('#beyazyakacalisansayisi').val()) || 0;
  var ayniIsi = parseFloat($('#ayniisiyapansayisi').val()) || 0;

  // Toplam çalışan sayısını hesapla
  var toplam = beyazYaka;

  // Eğer aynı işi yapan sayısı 0'dan büyükse, karekökünü al ve topla
  if (ayniIsi > 0) {
    toplam += Math.sqrt(ayniIsi);
  }

  // Sonucu toplam çalışan sayısı alanına yaz (ondalık kısmı yuvarlayarak)
  $('#toplamcalisansayisi').val(Math.round(toplam));

  // Hesapla fonksiyonu varsa çağır
  if (typeof hesapla === 'function') {
    hesapla();
  }
}

// Sayfa yüklendiğinde
$(document).ready(function() {
  // Beyaz yaka çalışan sayısı değiştiğinde
  $('#beyazyakacalisansayisi').on('change keyup', function() {
    updateEmployeeCount();
  });

  // Aynı işi yapan sayısı değiştiğinde
  $('#ayniisiyapansayisi').on('change keyup', function() {
    updateEmployeeCount();
  });

  // İlk yüklemede hesapla
  updateEmployeeCount();
});
