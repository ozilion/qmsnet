/**
 * Form Picker
 */

'use strict';


const previewTemplateDenetimPaketi = `<div class="dz-preview dz-file-preview">
<div class="dz-details">
  <div class="dz-thumbnail">
    <img data-dz-thumbnail>
    <span class="dz-nopreview">Önizleme yok</span>
    <div class="dz-success-mark"></div>
    <div class="dz-error-mark"></div>
    <div class="dz-error-message"><span data-dz-errormessage></span></div>
    <div class="progress">
      <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
    </div>
  </div>
  <div class="dz-filename" data-dz-name></div>
  <div class="dz-size" data-dz-size></div>
</div>
</div>`;

$(document).ready(function() {
  if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;

    let myDenetimDropzone;
    const pno = $('#pno').val();
    const asama = $('#asama').val();
    const dropzoneElement = document.getElementById('dropzone-multi-denetim');

    if (dropzoneElement) {
      const formURL = $('#formDenetimPaketiDosyasiYukleRoute').val();
      console.log("Form URL:", formURL);

      // Dropzone instance'ını direkt oluşturuyoruz (if condition kaldırılmış)
      myDenetimDropzone = new Dropzone(dropzoneElement, {
        url: formURL,
        previewTemplate: previewTemplateDenetimPaketi,
        dictDefaultMessage: "Dosyalarınızı buraya sürükleyin veya tıklayın",
        dictRemoveFile: "Dosyayı Sil",
        dictCancelUpload: "Yüklemeyi İptal Et",
        dictMaxFilesExceeded: "Maksimum dosya sayısını aştınız.",
        parallelUploads: 3,
        autoProcessQueue: false,
        acceptedFiles: 'image/jpeg,image/png,image/gif,image/svg+xml,application/pdf',
        addRemoveLinks: true,
        instantUpload: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        init: function () {
          console.log("[dropzone-multi-denetim] Dropzone init çağrıldı.");

          // Dosya eklendiğinde log yazdırıyoruz
          this.on("addedfile", function (file) {
            console.log("[dropzone-multi-denetim] Dosya eklendi:", file.name);
          });

          // Tek dosya gönderimi için "sending" eventini dinleyin.
          this.on("sending", function (file, xhr, formData) {
            console.log("[dropzone-multi-denetim] Sending (tek) file:", file.name);
            formData.append("pno", pno);
            formData.append("asama", asama);
            for (var pair of formData.entries()) {
              console.log(pair[0] + ': ' + pair[1]);
            }
          });

          // Çoklu gönderimler için "sendingmultiple" eventini dinleyin.
          this.on("sendingmultiple", function (files, xhr, formData) {
            console.log("[dropzone-multi-denetim] Sending multiple files:");
            formData.append("pno", pno);
            formData.append("asama", asama);
            files.forEach(function (file) {
              console.log(" -", file.name);
            });
            for (var pair of formData.entries()) {
              console.log(pair[0] + ': ' + pair[1]);
            }
          });

          this.on("successmultiple", function (files, response) {
            console.log("[dropzone-multi-denetim] Dosyalar yüklendi:", response);
          });
          this.on("errormultiple", function (files, response) {
            console.error("[dropzone-multi-denetim] Yükleme hatası:", response);
          });
          this.on("removedfile", function (file) {
            console.log("[dropzone-multi-denetim] Dosya silindi:", file.name);
          });
        }
      });
      console.log("[dropzone-multi-denetim] Dropzone instance oluşturuldu:", myDenetimDropzone);
    } else {
      console.error("Dropzone element bulunamadı!");
    }

    document.getElementById('uploadDenetimPaketButton').addEventListener('click', function (e) {
      e.preventDefault();
      console.log("Upload button clicked.");
      if (myDenetimDropzone) {
        console.log("Queued files:", myDenetimDropzone.getQueuedFiles());
        myDenetimDropzone.processQueue();
      }
    });
  }
});

(function () {

  // Flat Picker
  // --------------------------------------------------------------------
  const bggtarihi = document.querySelector('#gozdengecirmetarihi'),
    tarihrevasama1 = document.querySelector('#tarihrevasama1'),
    asama1tar = document.querySelector('#asama1tar'),
    tarihrevasama2 = document.querySelector('#tarihrevasama2'),
    asama2tar = document.querySelector('#asama2tar'),
    tarihrevgozetim1 = document.querySelector('#tarihrevgozetim1'),
    gozetim1tar = document.querySelector('#gozetim1tar'),
    tarihrevgozetim2 = document.querySelector('#tarihrevgozetim2'),
    gozetim2tar = document.querySelector('#gozetim2tar'),
    tarihrevyb = document.querySelector('#tarihrevyb'),
    ybtar = document.querySelector('#ybtar'),
    ozeltar = document.querySelector('#ozeltar'),
    degerlendirmekarartarih = document.querySelector('#degerlendirmekarartarih'),
    ilkyayin = document.querySelector('#ilkyayin'),
    yayintarihi = document.querySelector('#yayintarihi'),
    gecerliliktarihi = document.querySelector('#gecerliliktarihi'),
    bitistarihi = document.querySelector('#bitistarihi'),
    certrevtarihi = document.querySelector('#certrevtarihi'),
    selectPicker = $('.selectpicker');
  // bggtarihi
  if (bggtarihi) {
    bggtarihi.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (tarihrevasama1) {
    tarihrevasama1.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (asama1tar) {
    asama1tar.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      mode: 'multiple',
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static',
      onChange: function (selectedDates, dateStr, instance) {
        var r = $('#divbd1').val();
        if (r !== "") {
          denetcikontrol('asama1tar', 'divbd1', 'divd1', 'divtu1', 'divg1', 'diviku1');
          // console.log(selectedDates);  // İsteğe göre seçilen tarihleri inceleyebilirsiniz.
        } else {
          $('#divdenetcikontrol').html("Denetim ekibine en az bir kişi seçmelisiniz...");
          // Geriye false döndürmek flatpickr fonksiyonunu engellemez,
          // ancak bu şekilde kendi kodunuzun devamını durdurabilirsiniz.
          return false;
        }
        return false; // İsteğe bağlı
      },
    });
  }
  if (tarihrevasama2) {
    tarihrevasama2.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (asama2tar) {
    asama2tar.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      mode: 'multiple',
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static',
      onChange: function (selectedDates, dateStr, instance) {
        var r = $('#divbd2').val();
        if (r !== "") {
          denetcikontrol('asama2tar', 'divbd2', 'divd2', 'divtu2', 'divg2', 'diviku2');
          // console.log(selectedDate);
        } else {
          $('#divdenetcikontrol').html("Denetim ekibine en az bir kişi seçmelisiniz...");
          return false;
        }
        return false; // İsteğe bağlı
      },
    });
  }
  if (tarihrevgozetim1) {
    tarihrevgozetim1.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (gozetim1tar) {
    gozetim1tar.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      mode: 'multiple',
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static',
      onChange: function (selectedDates, dateStr, instance) {
        var r = $('#divbdg1').val();
        if (r !== "") {
          denetcikontrol('gozetim1tar', 'divbdg1', 'divgd1', 'divgtu1', 'divgg1', 'divikug1');
          // console.log(selectedDate);
        } else {
          $('#divdenetcikontrol').html("Denetim ekibine en az bir kişi seçmelisiniz...");
          return false;
        }
        return false; // İsteğe bağlı
      },
    });
  }
  if (tarihrevgozetim2) {
    tarihrevgozetim2.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (gozetim2tar) {
    gozetim2tar.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      mode: 'multiple',
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static',
      onChange: function (selectedDates, dateStr, instance) {
        var r = $('#divgbd2').val();
        if (r !== "") {
          denetcikontrol('gozetim2tar', 'divgbd2', 'divgd2', 'divgtu2', 'divgg2', 'divikug2');
          // console.log(selectedDate);
        } else {
          $('#divdenetcikontrol').html("Denetim ekibine en az bir kişi seçmelisiniz...");
          return false;
        }
        return false; // İsteğe bağlı
      },
    });
  }
  if (tarihrevyb) {
    tarihrevyb.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (ybtar) {
    ybtar.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      mode: 'multiple',
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static',
      onChange: function (selectedDates, dateStr, instance) {
        var r = $('#divbdyb').val();
        if (r !== "") {
          denetcikontrol('ybtar', 'divbdyb', 'divdyb', 'divtuyb', 'divgyb', 'divikuyb');
          // console.log(selectedDate);
        } else {
          $('#divdenetcikontrol').html("Denetim ekibine en az bir kişi seçmelisiniz...");
          return false;
        }
        return false; // İsteğe bağlı
      },
    });
  }
  if (ozeltar) {
    ozeltar.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      mode: 'multiple',
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static',
      onChange: function (selectedDates, dateStr, instance) {
        var r = $('#divotbd').val();
        if (r !== "") {
          denetcikontrol('ozeltar', 'divotbd', 'divdot', 'divottu', 'divotg', 'divotiku', 'divotad', 'divsidot');
          // console.log(selectedDate);
        } else {
          $('#divdenetcikontrol').html("Denetim ekibine en az bir kişi seçmelisiniz...");
          return false;
        }
        return false; // İsteğe bağlı
      },
    });
  }
  if (degerlendirmekarartarih) {
    degerlendirmekarartarih.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (ilkyayin) {
    ilkyayin.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (yayintarihi) {
    yayintarihi.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (gecerliliktarihi) {
    gecerliliktarihi.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (bitistarihi) {
    bitistarihi.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }
  if (certrevtarihi) {
    certrevtarihi.flatpickr({
      locale: {
        firstDayOfWeek: 1 // Haftanın ilk gününü Pazartesi yapar
      },
      todayBtn: true, // Bugün butonu ekler
      dateFormat: 'd.m.Y',
      monthSelectorType: 'static'
    });
  }

  // Bootstrap Select
  // --------------------------------------------------------------------
  if (selectPicker.length) {
    selectPicker.selectpicker();
    handleBootstrapSelectEvents();
  }

  // Offcanvas elementini seçelim.
  if ($('#offcanvasPlanlamadosyasiUpload').length) {
    const offcanvasEl = document.getElementById('offcanvasPlanlamadosyasiUpload');
    offcanvasEl.addEventListener('show.bs.offcanvas', function (event) {
      // Tetikleyici elementi event.relatedTarget ile alıyoruz.
      const triggerElement = event.relatedTarget;
      if (triggerElement) {
        // Birden fazla parametre alınır.
        // const uid = triggerElement.getAttribute('data-upload-uid') || '';
        const pno = $('#pno').val();
        const asama = $('#asama').val();

        // Hidden inputlara değer atama
        // document.getElementById('uid').value = uid;
        document.getElementById('pno').value = pno;
        document.getElementById('asama').value = asama;
      }

      // Offcanvas içindeki formu temizle
      const formInside = offcanvasEl.querySelector('form');
      if (formInside) {
        formInside.reset();
      }
    });
  }
})();


function chatgptForm() {

  const prompt = $('#prompt').val();
  const formURL = $('#chatgptFormRoute').val();
  const errorElement = $('#error');
  const outputElement = $('#responseOutput');
  var postData = 'prompt=' + prompt;

  // Hata mesajını ve önceki yanıtı temizle
  errorElement.hide().text('');
  outputElement.text('');

  if (!prompt) {
    errorElement.text('Prompt alanı zorunludur!').show();
    return;
  }

  $.ajax({
    url: formURL,
    type: 'POST',
    data: postData,
    success: function (response) {
      outputElement.text(response.output || 'Yanıt bulunamadı.');
    },
    error: function (xhr) {
      errorElement.text('Bir hata oluştu. Lütfen tekrar deneyin.').show();
      console.error(xhr.responseText);
    }
  });
}

function deepseekForm() {

  const prompt = $('#deepseekprompt').val();
  const formURL = $('#deepseekFormRoute').val();
  const errorElement = $('#deepseekerror');
  const outputElement = $('#deepseekresponseOutput');
  var postData = 'prompt=' + prompt;

  // Hata mesajını ve önceki yanıtı temizle
  errorElement.hide().text('');
  outputElement.text('');

  if (!prompt) {
    errorElement.text('Prompt alanı zorunludur!').show();
    return;
  }

  $.ajax({
    url: formURL,
    type: 'POST',
    data: postData,
    success: function (response) {
      outputElement.text(response || 'Yanıt bulunamadı.');
    },
    error: function (xhr) {
      errorElement.text('Bir hata oluştu. Lütfen tekrar deneyin.').show();
      console.error(xhr.responseText);
    }
  });
}

function geminiForm() {
  const prompt = $('#geminiprompt').val();
  const formURL = $('#geminiFormRoute').val();
  const errorElement = $('#geminierror');
  const outputElement = $('#geminiresponseOutput');

  // Hata mesajını ve önceki yanıtı temizle
  errorElement.hide().text('');
  outputElement.text('');

  // Prompt kontrolü
  if (!prompt) {
    errorElement.text('Prompt alanı zorunludur!').show();
    return;
  }

  // POST verisi
  const postData = {
    prompt: {
      text: prompt
    }
  };

  // Ajax isteği
  $.ajax({
    url: formURL, // Backend endpoint
    type: 'POST',
    data: JSON.stringify(postData),
    contentType: 'application/json',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF koruması
    },
    success: function (response) {
      if (response.success) {
        // Cevap göster
        outputElement.text(response.data || 'Yanıt bulunamadı.');
        console.log('Response:', response.data);
      } else {
        // Hata mesajı
        errorElement.text(response.message || 'Bir hata oluştu.').show();
        console.error('Error:', response.message);
      }
    },
    error: function (xhr) {
      // Genel hata durumu
      errorElement.text('Bir hata oluştu: ' + (xhr.responseText || 'Bilinmeyen hata')).show();
      console.error('API Error:', xhr.responseText);
    }
  });
}


function denetcikontrol(act) {
  var dkontrol = $("#divdenetcikontrol");
  var dkontrolmd = $("#divdenetcikontrolmodal");
  var submitted = false;
  var disabled = $('#planlama-form').find(':input:disabled').removeAttr('disabled');
  var postData = 'asamatar=' + act + '&tarihtar=' + $("#" + act).val() + '&' + $('#planlama-form').serialize();
  // var postData = 'act=denetcikontrol&asama=' + act + '&tarih=' + $("#" + act).val() + '&bd1=' + $("#" + bd).val() + '&d1=' + $("#" + d).val() + '&tu1=' + $("#" + tu).val() + '&g1=' + $("#" + g).val() + '&iku1=' + $("#" + iku).val();
  var formURL = $('#denetciKontrolRoute').val();

  // $("#dvloaderdk").show();
  // console.log(formURL + "?" + postData);
  if (!submitted) {
    $.ajax({
      url: formURL,
      type: 'GET',
      data: postData,
      success: function (html) {
        console.log("denetcikontrol:::?" + html);
        dkontrol.empty();
        dkontrolmd.empty();
        dkontrol.html(html);
        html = html.replaceAll("tabcontent", "tabcontentmd");
        dkontrolmd.html(html);
        // $("#dvloaderdk").hide();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#btnAsRaporYazdirLink").html('[denetcikontrol]<br>Durum: ' + textStatus + '<br>Hata: ' + errorThrown);
        // $("#dvloaderdk").hide();
      }
    });
    submitted = true;
    /* You can clear the input or hide the form, or something */
  }
  disabled.attr('disabled', 'disabled');
}

const questionNames = [
  'kararbda',
  'kararbdb',
  'kararbdc',
  'kararbdd',
  'kararbde',
  'kararbdf',
  'kararbdg',
  'kararbdh',
  'kararbdi',
  'kararbdj'
];

function recalcScore() {
  if ($('#toplampuan').length > 0 || $('#ortalamapuan').length > 0) {
    let sum = 0;
    let maxScore = questionNames.length * 2; // 10 soru * 2 puan = 20

    // Her sorunun seçilmiş değerini bul
    questionNames.forEach(qName => {
      const radios = document.querySelectorAll(`input[name="${qName}"]`);
      radios.forEach(radio => {
        if (radio.checked) {
          sum += parseInt(radio.value, 10);
        }
      });
    });

    // Toplam puanı yaz
    document.getElementById('toplampuan').value = sum;

    // Yüzdelik hesap: (sum / 20) * 100
    let percentage = (sum / maxScore) * 100;
    // 2 ondalık basamak
    let percentageStr = percentage.toFixed(2) + '%';

    // Yüzde değeri ortalamapuan alanına yaz
    document.getElementById('ortalamapuan').value = percentageStr;
  }
}

window.addEventListener('DOMContentLoaded', () => {
  recalcScore();
});
