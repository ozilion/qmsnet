<!-- resources/views/partials/planlama-menu.blade.php -->

<div class="action-btns">
  <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#offcanvasPlanlama"
          aria-controls="offcanvasPlanlama">
    <i class="mdi mdi-cog-refresh-outline me-sm-1 me-0"></i> Menü
  </button>
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPlanlama"
       aria-labelledby="offcanvasPlanlamaLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasPlanlamaLabel" class="offcanvas-title">Planlama Menüsü</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
              aria-label="Close"></button>
    </div>
    <div class="offcanvas-body my-auto mx-0 flex-grow-0">
      <a href="{{ route('crm-planlama', ['asama' => 'basvuru', 'pno' => $pno]) }}"
         class="btn btn-primary btn-sm d-grid w-100 mb-1" type="button">Başvuru Bilgileri</a>

      <a href="{{ route('crm-planlama', ['asama' => 'ilkplan', 'pno' => $pno]) }}"
         class="btn btn-secondary btn-sm d-grid w-100 mb-1" type="button">İlk Belgelendirme</a>
      <hr>

      <a href="{{ route('audit-plan', ['asama' => 'asama1', 'pno' => $pno]) }}"
         class="btn btn-info btn-sm d-grid w-100 mb-1" type="button">Denetim Planı A1</a>

      <a href="{{ route('audit-plan', ['asama' => 'asama2', 'pno' => $pno]) }}"
         class="btn btn-info btn-sm d-grid w-100 mb-1" type="button">Denetim Planı A2</a>
      <hr>

      <a href="{{ route('crm-planlama', ['asama' => 'ilkkarar', 'pno' => $pno]) }}"
         class="btn btn-success btn-sm d-grid w-100 mb-1" type="button">İlk Belgelendirme Kararı</a>

      <a href="{{ route('crm-planlama', ['asama' => 'g1', 'pno' => $pno]) }}"
         class="btn btn-warning btn-sm d-grid w-100 mb-1" type="button">Gözetim 1</a>

      <a href="{{ route('audit-plan', ['asama' => 'gozetim1', 'pno' => $pno]) }}"
         class="btn btn-info btn-sm d-grid w-100 mb-1" type="button">Denetim Planı</a>

      <a href="{{ route('crm-planlama', ['asama' => 'g1karar', 'pno' => $pno]) }}"
         class="btn btn-success btn-sm d-grid w-100 mb-1" type="button">Gözetim 1 Kararı</a>

      <a href="{{ route('crm-planlama', ['asama' => 'g2', 'pno' => $pno]) }}"
         class="btn btn-info btn-sm d-grid w-100 mb-1" type="button">Gözetim 2</a>

      <a href="{{ route('audit-plan', ['asama' => 'gozetim2', 'pno' => $pno]) }}"
         class="btn btn-info btn-sm d-grid w-100 mb-1" type="button">Denetim Planı</a>

      <a href="{{ route('crm-planlama', ['asama' => 'g2karar', 'pno' => $pno]) }}"
         class="btn btn-success btn-sm d-grid w-100 mb-1" type="button">Gözetim 2 Kararı</a>

      <a href="{{ route('crm-planlama', ['asama' => 'yb', 'pno' => $pno]) }}"
         class="btn btn-light btn-sm d-grid w-100 mb-1" type="button">Yeniden Belgelendirme</a>

      <a href="{{ route('audit-plan', ['asama' => 'ybtar', 'pno' => $pno]) }}"
         class="btn btn-info btn-sm d-grid w-100 mb-1" type="button">Denetim Planı</a>

      <a href="{{ route('crm-planlama', ['asama' => 'ybkarar', 'pno' => $pno]) }}"
         class="btn btn-success btn-sm d-grid w-100 mb-1" type="button">Yeniden Belgelendirme Kararı</a>

      <a href="{{ route('crm-planlama', ['asama' => 'ozel', 'pno' => $pno]) }}"
         class="btn btn-dark btn-sm d-grid w-100 mb-1" type="button">Özel</a>

      <a href="{{ route('audit-plan', ['asama' => 'ozeltar', 'pno' => $pno]) }}"
         class="btn btn-info btn-sm d-grid w-100 mb-1" type="button">Denetim Planı</a>

      <a href="{{ route('crm-planlama', ['asama' => 'ozelkarar', 'pno' => $pno]) }}"
         class="btn btn-success btn-sm d-grid w-100 mb-1" type="button">Özel Karar</a>

      <a href="{{ route('crm-planlama', ['asama' => 'sertifika', 'pno' => $pno]) }}"
         class="btn btn-danger btn-sm d-grid w-100 mb-1" type="button">Sertifika</a>

      <button type="button" class="btn btn-outline-secondary d-grid w-100 mt-1"
              data-bs-dismiss="offcanvas">
        Kapat
      </button>
    </div>
  </div>
</div>
