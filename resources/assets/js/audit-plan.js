/**
 * Cards Actions
 */

'use strict';

$(document).ready(function() {
  // Single form submission handler
  $('#formAuditPlan').on('submit', function(e) {
    e.preventDefault();

    // Group rows by department
    const groupedRows = groupRowsByDepartment();

    // Add grouped data to the form
    const groupedDataInput = $('<input>')
      .attr('type', 'hidden')
      .attr('name', 'groupedData')
      .val(JSON.stringify(groupedRows));

    $(this).append(groupedDataInput);

    // Show loading message
    $('#formMessages').html('<div class="alert alert-info">İşleminiz gerçekleştiriliyor, lütfen bekleyiniz...</div>').show();

    // Submit the form via AJAX
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('#formMessages').html(`<div class="alert alert-success">${response.message}</div>`);
        } else {
          $('#formMessages').html(`<div class="alert alert-danger">${response.message}</div>`);
        }
      },
      error: function(xhr) {
        let errorMessage = 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.';

        if (xhr.responseJSON) {
          if (xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          } else if (xhr.responseJSON.errors) {
            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
          }
        }

        $('#formMessages').html(`<div class="alert alert-danger">${errorMessage}</div>`);
      }
    });
  });

  // Function to group rows by department/time/team
  function groupRowsByDepartment() {
    const groupedRows = {};

    $('#scheduleTable tbody tr').each(function() {
      if ($(this).data('locked') === true) return; // Skip locked rows

      const department = $(this).find('input[name$="[department]"]').val();
      const start = $(this).find('input[name$="[start]"]').val();
      const end = $(this).find('input[name$="[end]"]').val();
      const team = $(this).find('select[name$="[team]"]').val();

      if (!department && !start && !end) return; // Skip empty rows

      const key = `${department}-${start}-${end}-${team}`;

      if (!groupedRows[key]) {
        groupedRows[key] = {
          department,
          start,
          end,
          team,
          standards: []
        };
      }

      const standard = $(this).find('select[name$="[standard]"]').val();
      const maddeNo = $(this).find('select[name$="[madde_no][]"]').val();

      if (standard) {
        groupedRows[key].standards.push({
          standard,
          maddeNo
        });
      }
    });

    return groupedRows;
  }

});

(function () {
  const collapseElementList = [].slice.call(document.querySelectorAll('.card-collapsible'));

  // Collapsible card
  // --------------------------------------------------------------------
  if (collapseElementList) {
    collapseElementList.map(function (collapseElement) {
      collapseElement.addEventListener('click', event => {
        event.preventDefault();
        // Collapse the element
        new bootstrap.Collapse(collapseElement.closest('.card').querySelector('.collapse'));
        // Toggle collapsed class in `.card-header` element
        collapseElement.closest('.card-header').classList.toggle('collapsed');
        // Toggle class mdi-chevron-down & mdi-chevron-up
        Helpers._toggleClass(collapseElement.firstElementChild, 'mdi-chevron-down', 'mdi-chevron-up');
      });
    });
  }
})();

// Make sure jQuery is loaded before executing any code
document.addEventListener('DOMContentLoaded', function() {
  // Check if jQuery is loaded
  if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded! Please include jQuery before this script.');
    // Add a visible error message on the page
    const errorMsg = document.createElement('div');
    errorMsg.className = 'alert alert-danger';
    errorMsg.textContent = 'jQuery is required but not loaded. Please include jQuery library.';
    document.body.prepend(errorMsg);
    return; // Stop execution if jQuery isn't available
  }

  // Get data passed from PHP
  let standards = window.appData.standards;
  let teams = window.appData.teams;
  let auditData = window.appData.audit;
  let asama = window.appData.asama;

  // Initialize global rowIndex for adding new rows
  window.rowIndex = 3; // Start after the two default rows

  // Function to initialize select2 on an element
  function initSelect2(element) {
    jQuery(element).select2({
      placeholder: "Lütfen Seçiniz",
      allowClear: true,
      width: '100%'
    });
  }

  // Initialize select2 on page load
  jQuery('.select2').each(function() {
    initSelect2(this);
  });

  // Load existing audit plan data if available
  if (auditData) {
    loadExistingAuditPlan(auditData);
  }

  // Normal satır ekleme
  document.getElementById('addRow').addEventListener('click', function() {
    let tbody = document.querySelector('#scheduleTable tbody');
    let lastRow = tbody.lastElementChild;
    let prevEndTime = "";
    if (lastRow) {
      let endInput = lastRow.cells[2].querySelector('input');
      if (endInput) { prevEndTime = endInput.value; }
    }
    let teamOptions = '';
    teams.forEach(function(team) {
      teamOptions += `<option value="${team}">${team}</option>`;
    });
    let standardOptions = '<option value="">Lütfen Seçiniz</option>';
    standards.forEach(function(std) {
      standardOptions += `<option value="${std}">${std}</option>`;
    });
    let tr = document.createElement('tr');
    tr.innerHTML = `
<td><input type="checkbox" class="row-select"></td>
<td><input type="time" name="rows[${rowIndex}][start]" value="${prevEndTime}" class="form-control"></td>
<td><input type="time" name="rows[${rowIndex}][end]" class="form-control"></td>
<td><input type="text" name="rows[${rowIndex}][department]" placeholder="Departman/ Proses/Saha" class="form-control"></td>
<td>
  <select name="rows[${rowIndex}][team]" class="form-control">
    <option value="">Lütfen Seçiniz</option>
    ${teamOptions}
  </select>
</td>
<td>
  <select name="rows[${rowIndex}][standard]" class="form-control">
    ${standardOptions}
  </select>
</td>
<td>
  <select name="rows[${rowIndex}][madde_no][]" class="select2 form-select" multiple>
    <option value="">Lütfen Seçiniz</option>
  </select>
</td>
<td><button type="button" class="btn btn-danger remove-row">Sil</button></td>
`;
    tbody.appendChild(tr);
    rowIndex++;
    // After appending the new row
    jQuery(tr).find('.select2').select2({
      placeholder: "Lütfen Seçiniz"
    });
  });

  // Satır silme (locked satırlar silinemez)
  document.querySelector('#scheduleTable tbody').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-row')) {
      let row = e.target.closest('tr');
      if (row.dataset.locked === "true") {
        alert('Bu satır silinemez.');
      } else {
        row.remove();
      }
    }
  });

  // Birleştirme: Sadece saat (sütun 1 ve 2) ve departman (sütun 3) hücreleri birleştirilecek.
  document.getElementById('mergeRows').addEventListener('click', function() {
    let rows = Array.from(document.querySelectorAll('#scheduleTable tbody tr'));
    let dynamicRows = rows.filter((row, idx) => idx >= 2);
    let selectedRows = dynamicRows.filter(row => row.querySelector('.row-select') && row.querySelector('.row-select').checked);
    if (selectedRows.length < 2) {
      alert('Lütfen birleştirmek için en az iki ardışık satır seçin.');
      return;
    }
    let indices = selectedRows.map(row => dynamicRows.indexOf(row)).sort((a, b) => a - b);
    for (let i = 1; i < indices.length; i++) {
      if (indices[i] !== indices[i - 1] + 1) {
        alert('Seçilen satırlar ardışık değil.');
        return;
      }
    }
    let mergedData = selectedRows.map(row => {
      let start = row.cells[1].querySelector('input') ? row.cells[1].querySelector('input').value : '';
      let end = row.cells[2].querySelector('input') ? row.cells[2].querySelector('input').value : '';
      let department = row.cells[3].querySelector('input') ? row.cells[3].querySelector('input').value : row.cells[3].innerText.trim();
      return { start, end, department };
    });
    let firstRow = selectedRows[0];
    firstRow.dataset.merged = JSON.stringify(mergedData);
    firstRow.cells[1].innerHTML = `<input type="time" name="rows[merged][start]" value="${mergedData[0].start}" class="form-control">`;
    firstRow.cells[2].innerHTML = `<input type="time" name="rows[merged][end]" value="${mergedData[mergedData.length - 1].end}" class="form-control">`;
    firstRow.cells[3].innerHTML = `<input type="text" name="rows[merged][department]" value="${mergedData.map(d => d.department).join(' | ')}" class="form-control">`;
    selectedRows.slice(1).forEach(row => row.remove());
  });

  document.getElementById('splitRow').addEventListener('click', function() {
    let tbody = document.querySelector('#scheduleTable tbody');
    let rows = Array.from(tbody.querySelectorAll('tr'));
    let mergedRow = rows.find(row => row.dataset.merged && rows.indexOf(row) >= 2);
    if (!mergedRow) {
      alert('Birleştirilmiş satır bulunamadı.');
      return;
    }
    let mergedData = JSON.parse(mergedRow.dataset.merged);
    let index = Array.from(tbody.children).indexOf(mergedRow);
    mergedData.forEach((data, idx) => {
      let tr = document.createElement('tr');
      tr.innerHTML = `
<td><input type="checkbox" class="row-select"></td>
<td><input type="time" name="rows[${rowIndex + idx}][start]" value="${data.start}" class="form-control"></td>
<td><input type="time" name="rows[${rowIndex + idx}][end]" value="${data.end}" class="form-control"></td>
<td><input type="text" name="rows[${rowIndex + idx}][department]" value="${data.department || ''}" class="form-control"></td>
<td><input type="text" name="rows[${rowIndex + idx}][team]" placeholder="Denetim Ekibi" class="form-control"></td>
<td><input type="text" name="rows[${rowIndex + idx}][standard]" placeholder="Standard" class="form-control"></td>
<td><input type="text" name="rows[${rowIndex + idx}][madde_no]" placeholder="Standard Madde No" class="form-control"></td>
<td><button type="button" class="btn btn-danger remove-row">Sil</button></td>
`;
      tbody.insertBefore(tr, tbody.children[index]);
      index++;
    });
    mergedRow.remove();
    rowIndex += mergedData.length;
  });

  // Değerlendirme Ekle butonu
  document.getElementById('addDegerlendirme').addEventListener('click', function() {
    let tbody = document.querySelector('#scheduleTable tbody');
    let lastRow = tbody.lastElementChild;
    let prevEndTime = "";
    if (lastRow) {
      let endInput = lastRow.cells[2].querySelector('input');
      if (endInput) { prevEndTime = endInput.value; }
    }
    let tr = document.createElement('tr');
    tr.innerHTML = `
<td></td>
<td><input type="time" name="rows[deg][start]" value="${prevEndTime}" class="form-control"></td>
<td><input type="time" name="rows[deg][end]" value="" class="form-control"></td>
<td colspan="4" class="text-center">Değerlendirme</td>
<td><button type="button" class="btn btn-danger remove-row">Sil</button></td>
`;
    tbody.appendChild(tr);
  });

  // Kapanış Toplantısı Ekle butonu
  document.getElementById('addKapanis').addEventListener('click', function() {
    let tbody = document.querySelector('#scheduleTable tbody');
    let lastRow = tbody.lastElementChild;
    let prevEndTime = "";
    if (lastRow) {
      let endInput = lastRow.cells[2].querySelector('input');
      if (endInput) { prevEndTime = endInput.value; }
    }
    let tr = document.createElement('tr');
    tr.innerHTML = `
<td></td>
<td><input type="time" name="rows[kap][start]" value="${prevEndTime}" class="form-control"></td>
<td><input type="time" name="rows[kap][end]" value="" class="form-control"></td>
<td colspan="4" class="text-center">Kapanış Toplantısı</td>
<td><button type="button" class="btn btn-danger remove-row">Sil</button></td>
`;
    tbody.appendChild(tr);
  });

  // Öğle Arası Ekle butonu
  document.getElementById('addOglen').addEventListener('click', function() {
    let tbody = document.querySelector('#scheduleTable tbody');
    let tr = document.createElement('tr');
    tr.innerHTML = `
<td></td>
<td><input type="time" name="rows[ogle][start]" value="12:00" class="form-control"></td>
<td><input type="time" name="rows[ogle][end]" value="13:00" class="form-control"></td>
<td colspan="4" class="text-center">Öğle Arası</td>
<td><button type="button" class="btn btn-danger remove-row">Sil</button></td>
`;
    tbody.appendChild(tr);
  });

  // Modify the standard change event handler to use the pending values
  jQuery(document).on('change', 'select[name^="rows"][name$="[standard]"]', function() {
    let standard = jQuery(this).val();
    // Get the madde_no select element
    let maddeNoSelect = jQuery(this).closest('tr').find('select[name^="rows"][name$="[madde_no][]"]');

    // Clear existing options
    maddeNoSelect.html('<option value="">Lütfen Seçiniz</option>');

    if (standard) {
      // Store the select element for later reference
      const selectElement = maddeNoSelect[0];
      // Get any pending values that need to be set
      const pendingValues = jQuery(maddeNoSelect).data('pending-values');

      jQuery.ajax({
        url: '/getMaddeNos',
        method: 'GET',
        data: { standard: standard, asama: asama },
        success: function(response) {
          // Add options based on response
          response.forEach(function(madde) {
            maddeNoSelect.append(`<option value="${madde}">${madde}</option>`);
          });

          // If we have pending values, set them now
          if (pendingValues) {
            setMaddeNoValues(selectElement, pendingValues);
            // Clear the pending values to avoid double-setting
            jQuery(maddeNoSelect).removeData('pending-values');
          }
        }
      });
    }
  });

  // Function to set madde_no values with retry mechanism
  function setMaddeNoValues(selectElement, maddeNoData) {
    // First, ensure we have valid madde_no data
    if (!maddeNoData) return;

    // Parse the madde_no values if they're in string format
    let maddeNoValues = [];

    if (typeof maddeNoData === 'string') {
      try {
        // Try to parse as JSON
        maddeNoValues = JSON.parse(maddeNoData);
      } catch (e) {
        // If not valid JSON, split by comma
        maddeNoValues = maddeNoData.split(',').map(item => item.trim());
      }
    } else if (Array.isArray(maddeNoData)) {
      // If already an array, use as is
      maddeNoValues = maddeNoData;
    }

    console.log('Setting madde_no values:', maddeNoValues);

    // Wait a bit longer to ensure options are loaded
    setTimeout(() => {
      // Check if there are options loaded
      const options = jQuery(selectElement).find('option');
      console.log(`Found ${options.length} options in the select`);

      if (options.length > 1) { // More than just the placeholder
        // Select the values
        jQuery(selectElement).val(maddeNoValues).trigger('change');
        console.log('Values selected in dropdown');
      } else {
        // Options not loaded yet, try again after a longer delay
        console.log('Options not loaded yet, trying again...');
        setTimeout(() => {
          jQuery(selectElement).val(maddeNoValues).trigger('change');
          console.log('Values selected in dropdown (retry)');
        }, 1000);
      }
    }, 500);
  }

  // Function to load existing audit plan data
  function loadExistingAuditPlan(auditData) {
    try {
      console.log('Loading audit data:', auditData);

      // First, remove any non-locked rows from the table
      const tbody = document.querySelector('#scheduleTable tbody');
      const existingRows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.dataset.locked !== 'true');
      existingRows.forEach(row => row.remove());

      // Now, parse and load the rows data
      let rowsData;

      if (typeof auditData.rows === 'string') {
        // Parse the JSON string if needed
        rowsData = JSON.parse(auditData.rows);
      } else if (auditData.rows) {
        // Use the object directly if already parsed
        rowsData = auditData.rows;
      } else {
        // Legacy format - doesn't have rows JSON
        console.log('No rows data found in audit record');
        return;
      }

      console.log('Rows data to load:', rowsData);

      // Track the highest numeric index for setting the next row index
      let maxIndex = 2; // Start at 2 since we have default rows 0 and 1

      // Process each row in the data
      Object.keys(rowsData).forEach(key => {
        const rowData = rowsData[key];
        console.log(`Processing row ${key}:`, rowData);

        if (key === '0' || key === '1') {
          // Update the fixed rows (opening and short tour)
          const row = tbody.querySelector(`tr:nth-child(${parseInt(key) + 1})`);
          if (row) {
            const startInput = row.querySelector(`input[name="rows[${key}][start]"]`);
            const endInput = row.querySelector(`input[name="rows[${key}][end]"]`);
            if (startInput && rowData.start) startInput.value = rowData.start;
            if (endInput && rowData.end) endInput.value = rowData.end;
          }
        } else if (key === 'deg' || key === 'kap' || key === 'ogle') {
          // Special rows
          addSpecialRow(key, rowData);
        } else if (key === 'merged') {
          // Merged row
          addMergedRow(rowData);
        } else if (!isNaN(parseInt(key))) {
          // Regular numeric row
          const index = parseInt(key);
          addRegularRow(index, rowData);
          maxIndex = Math.max(maxIndex, index);
        }
      });

      // Update the global row index for future additions
      window.rowIndex = maxIndex + 1;
      console.log('Next row index set to:', window.rowIndex);

      // Call triggerPreselectedStandards after loading data with a delay
      setTimeout(triggerPreselectedStandards, 500);
    } catch (error) {
      console.error('Error loading audit plan data:', error);
    }
  }

  // Helper function to add a special row (evaluation, closing, lunch break)
  function addSpecialRow(key, rowData) {
    const tbody = document.querySelector('#scheduleTable tbody');
    const tr = document.createElement('tr');

    let title = '';
    if (key === 'deg') title = 'Değerlendirme';
    else if (key === 'kap') title = 'Kapanış Toplantısı';
    else if (key === 'ogle') title = 'Öğle Arası';

    tr.innerHTML = `
<td></td>
<td><input type="time" name="rows[${key}][start]" value="${rowData.start || ''}" class="form-control"></td>
<td><input type="time" name="rows[${key}][end]" value="${rowData.end || ''}" class="form-control"></td>
<td colspan="4" class="text-center">${title}</td>
<td><button type="button" class="btn btn-danger remove-row">Sil</button></td>
`;

    tbody.appendChild(tr);
  }

  // Helper function to add a merged row
  function addMergedRow(rowData) {
    const tbody = document.querySelector('#scheduleTable tbody');
    const tr = document.createElement('tr');

    // Prepare merged data attribute
    let mergedData;
    if (rowData.mergedData) {
      mergedData = typeof rowData.mergedData === 'string'
        ? rowData.mergedData
        : JSON.stringify(rowData.mergedData);
    } else {
      mergedData = JSON.stringify([{
        start: rowData.start || '',
        end: rowData.end || '',
        department: rowData.department || ''
      }]);
    }
    tr.dataset.merged = mergedData;

    // Generate team options
    let teamOptions = '<option value="">Lütfen Seçiniz</option>';
    teams.forEach(function(team) {
      const selected = team === rowData.team ? 'selected' : '';
      teamOptions += `<option value="${team}" ${selected}>${team}</option>`;
    });

    // Generate standard options
    let standardOptions = '<option value="">Lütfen Seçiniz</option>';
    standards.forEach(function(std) {
      const selected = std === rowData.standard ? 'selected' : '';
      standardOptions += `<option value="${std}" ${selected}>${std}</option>`;
    });

    tr.innerHTML = `
<td><input type="checkbox" class="row-select"></td>
<td><input type="time" name="rows[merged][start]" value="${rowData.start || ''}" class="form-control"></td>
<td><input type="time" name="rows[merged][end]" value="${rowData.end || ''}" class="form-control"></td>
<td><input type="text" name="rows[merged][department]" value="${rowData.department || ''}" class="form-control"></td>
<td>
  <select name="rows[merged][team]" class="form-control">
    ${teamOptions}
  </select>
</td>
<td>
  <select name="rows[merged][standard]" class="form-control">
    ${standardOptions}
  </select>
</td>
<td>
  <select name="rows[merged][madde_no][]" class="select2 form-select" multiple>
    <option value="">Lütfen Seçiniz</option>
  </select>
</td>
<td><button type="button" class="btn btn-danger remove-row">Sil</button></td>
`;

    tbody.appendChild(tr);

    // Initialize Select2 and handle madde_no selection
    const select2 = jQuery(tr).find('.select2');
    select2.select2({placeholder: "Lütfen Seçiniz"});

    const standardSelect = tr.querySelector('select[name$="[standard]"]');
    const maddeNoSelect = tr.querySelector('select[name$="[madde_no][]"]');

    if (standardSelect && maddeNoSelect && rowData.standard) {
      // Store the madde_no values in a data attribute
      if (rowData.madde_no) {
        jQuery(maddeNoSelect).data('pending-values', rowData.madde_no);
      }

      // Trigger the standard change to load options
      jQuery(standardSelect).trigger('change');
    }
  }

  // Helper function to add a regular row
  function addRegularRow(index, rowData) {
    const tbody = document.querySelector('#scheduleTable tbody');
    const tr = document.createElement('tr');
    tr.classList.add('default-row');
    tr.dataset.locked = 'false';

    // Generate team options
    let teamOptions = '<option value="">Lütfen Seçiniz</option>';
    teams.forEach(function(team) {
      const selected = team === rowData.team ? 'selected' : '';
      teamOptions += `<option value="${team}" ${selected}>${team}</option>`;
    });

    // Generate standard options
    let standardOptions = '<option value="">Lütfen Seçiniz</option>';
    standards.forEach(function(std) {
      const selected = std === rowData.standard ? 'selected' : '';
      standardOptions += `<option value="${std}" ${selected}>${std}</option>`;
    });

    tr.innerHTML = `
<td><input type="checkbox" class="row-select"></td>
<td><input type="time" name="rows[${index}][start]" value="${rowData.start || ''}" class="form-control"></td>
<td><input type="time" name="rows[${index}][end]" value="${rowData.end || ''}" class="form-control"></td>
<td><input type="text" name="rows[${index}][department]" placeholder="Departman/ Proses/Saha" class="form-control" value="${rowData.department || ''}"></td>
<td>
  <select name="rows[${index}][team]" class="form-control">
    ${teamOptions}
  </select>
</td>
<td>
  <select name="rows[${index}][standard]" class="form-control">
    ${standardOptions}
  </select>
</td>
<td>
  <select name="rows[${index}][madde_no][]" class="select2 form-select" multiple>
    <option value="">Lütfen Seçiniz</option>
  </select>
</td>
<td><button type="button" class="btn btn-danger remove-row">Sil</button></td>
`;

    tbody.appendChild(tr);

    // Initialize Select2 for this row
    const select2 = jQuery(tr).find('.select2');
    select2.select2({placeholder: "Lütfen Seçiniz"});

    const standardSelect = tr.querySelector('select[name^="rows"][name$="[standard]"]');
    const maddeNoSelect = tr.querySelector('select[name^="rows"][name$="[madde_no][]"]');

    if (standardSelect && maddeNoSelect && rowData.standard) {
      // Store the madde_no values in a data attribute
      if (rowData.madde_no) {
        jQuery(maddeNoSelect).data('pending-values', rowData.madde_no);
        console.log(`Row ${index} - Standard: ${rowData.standard}, madde_no:`, rowData.madde_no);
      }

      // Trigger the standard change to load options
      jQuery(standardSelect).trigger('change');
    }

    return tr;
  }

  // Function to trigger standards for initial loading
  function triggerPreselectedStandards() {
    console.log('Triggering preselected standards to load madde_no values');

    // Find all standard selects that have a value already selected
    const standardSelects = jQuery('select[name^="rows"][name$="[standard]"]').filter(function() {
      return jQuery(this).val() !== '' && jQuery(this).val() !== null;
    });

    console.log(`Found ${standardSelects.length} preselected standards`);

    // Trigger change on each standard select with a slight delay between each
    // to prevent overwhelming the server with AJAX requests
    standardSelects.each(function(index) {
      const $this = jQuery(this);
      setTimeout(function() {
        console.log(`Triggering change on standard #${index}: ${$this.val()}`);
        $this.trigger('change');
      }, index * 300); // 300ms delay between each trigger
    });
  }

  // Single form submission handler with grouped data
  jQuery('#formAuditPlan').on('submit', function(e) {
    e.preventDefault();

    // Group rows by department
    const groupedRows = {};

    // Find rows with the same department/time/team
    jQuery('#scheduleTable tbody tr').each(function() {
      if (jQuery(this).data('locked') === true) return; // Skip locked rows

      const department = jQuery(this).find('input[name$="[department]"]').val();
      const start = jQuery(this).find('input[name$="[start]"]').val();
      const end = jQuery(this).find('input[name$="[end]"]').val();
      const team = jQuery(this).find('select[name$="[team]"]').val();

      if (!department && !start && !end) return; // Skip empty rows

      const key = btoa(encodeURIComponent(`${department}-${start}-${end}-${team}`));

      if (!groupedRows[key]) {
        groupedRows[key] = {
          department,
          start,
          end,
          team,
          standards: []
        };
      }

      const standard = jQuery(this).find('select[name$="[standard]"]').val();
      const maddeNo = jQuery(this).find('select[name$="[madde_no][]"]').val();

      if (standard) {
        groupedRows[key].standards.push({
          standard,
          maddeNo
        });
      }
    });

    // Add grouped data to the form
    const groupedDataInput = jQuery('<input>')
      .attr('type', 'hidden')
      .attr('name', 'groupedData')
      .val(JSON.stringify(groupedRows));

    jQuery(this).append(groupedDataInput);

    // Show loading message
    jQuery('#formMessages').html('<div class="alert alert-info">İşleminiz gerçekleştiriliyor, lütfen bekleyiniz...</div>').show();

    // Submit the form via AJAX
    jQuery.ajax({
      url: jQuery(this).attr('action'),
      type: 'POST',
      data: jQuery(this).serialize(),
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          jQuery('#formMessages').html(`<div class="alert alert-success">${response.message}</div>`);
        } else {
          jQuery('#formMessages').html(`<div class="alert alert-danger">${response.message}</div>`);
        }
      },
      error: function(xhr) {
        let errorMessage = 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.';

        if (xhr.responseJSON) {
          if (xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          } else if (xhr.responseJSON.errors) {
            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
          }
        }

        jQuery('#formMessages').html(`<div class="alert alert-danger">${errorMessage}</div>`);
      }
    });
  });

  // Also call triggerPreselectedStandards on page load
  setTimeout(triggerPreselectedStandards, 500);
  setupDocumentExport();

  // Re-initialize export buttons if any content loads via AJAX
  document.addEventListener('ajaxComplete', function() {
    setupDocumentExport();
  });
});

// Document export functionality
function setupDocumentExport() {
  // Get all export buttons
  const exportButtons = document.querySelectorAll('a[href*="audit-plan-export"]');

  exportButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault(); // Prevent the default link behavior (page navigation)

      // Show loading message
      $('#formMessages').html('<div class="alert alert-info">Doküman oluşturuluyor, lütfen bekleyiniz...</div>').show();

      // Get the URL from the href attribute
      const url = button.getAttribute('href');

      // Create a hidden iframe for the download
      const downloadFrame = document.createElement('iframe');
      downloadFrame.style.display = 'none';
      document.body.appendChild(downloadFrame);

      // Set iframe source to the export URL
      downloadFrame.src = url;

      // Handle iframe load event
      downloadFrame.onload = function() {
        try {
          // If we can access iframe content, there might be an error (same-origin content)
          const frameContent = downloadFrame.contentDocument || downloadFrame.contentWindow.document;
          const errorContent = frameContent.body.textContent;

          // Check if content contains error message
          if (errorContent && errorContent.indexOf('error') !== -1) {
            try {
              // Try to parse JSON error
              const errorJson = JSON.parse(errorContent);
              $('#formMessages').html(`<div class="alert alert-danger">Hata: ${errorJson.error}</div>`);
            } catch {
              // Not JSON, just show as text
              $('#formMessages').html(`<div class="alert alert-danger">Doküman oluşturulurken bir hata oluştu.</div>`);
            }
          } else {
            // Success message
            $('#formMessages').html('<div class="alert alert-success">Doküman başarıyla oluşturuldu.</div>');
          }
        } catch (e) {
          // If we can't access iframe content due to same-origin policy,
          // it's probably because the download was initiated
          $('#formMessages').html('<div class="alert alert-success">Doküman başarıyla oluşturuldu.</div>');
        }

        // Remove the iframe after a short delay
        setTimeout(() => {
          document.body.removeChild(downloadFrame);
        }, 5000);
      };
    });
  });
}

// Add global AJAX error handler
jQuery(document).ajaxError(function(event, jqXHR, settings, error) {
  console.error('AJAX Error:', error, settings.url);

  if (jqXHR.status === 419) {
    // CSRF token mismatch
    jQuery('#formMessages').html('<div class="alert alert-danger">Oturum zaman aşımına uğradı. Lütfen sayfayı yenileyiniz.</div>').show();
  }
});

