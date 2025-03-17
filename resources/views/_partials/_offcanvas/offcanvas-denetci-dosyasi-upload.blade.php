<!-- Geliştirilmiş Dosya Yükleme Offcanvas -->
<div class="offcanvas offcanvas-end" id="offcanvasDenetcidosyasiUpload" aria-hidden="true">
  <div class="offcanvas-header mb-3">
    <h5 class="offcanvas-title">Dosya Yükle</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body flex-grow-1">
    <div class="card">
      <div class="card-body">
        <form id="dropzone-multi-denetci" method="post" action="{{route('denetci-dosyasi-upload')}}" enctype="multipart/form-data">
          {{ csrf_field() }}
          <input type="hidden" name="klasor" id="klasor" value="">
          <input type="hidden" name="altklasor" id="altklasor" value="">
          <input type="hidden" name="uid" id="uid" value="">

          <div class="form-floating form-floating-outline mb-4">
            <textarea class="form-control" id="atamaeakategori" name="atamaeakategori" style="height: 62px;"></textarea>
            <label for="atamaeakategori">Atanabileceği EA Kodu/Kategori/Teknik Alan</label>
          </div>

          <!-- Modern Dropzone Alanı -->
          <div class="dropzone-area mb-4">
            <div class="dz-message">
              <div class="mb-3">
                <i class="mdi mdi-cloud-upload mdi-36px text-primary"></i>
              </div>
              <h5>Dosyaları buraya sürükleyip bırakın</h5>
              <p class="mb-0">veya dosya seçmek için <span class="text-primary fw-semibold">tıklayın</span></p>
              <p class="text-muted mt-3">İzin verilen dosya tipleri: JPG, PNG, PDF, SVG</p>
            </div>
          </div>

          <!-- Dosya Önizleme Alanı -->
          <div id="preview-container" class="mb-4 d-none">
            <h6 class="mb-2">Yüklenecek Dosyalar</h6>
            <div id="dropzone-previews" class="dropzone-previews"></div>
          </div>

          <!-- Yükleme Butonları -->
          <div class="d-flex justify-content-end align-items-center gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">İptal</button>
            <button type="button" class="btn btn-primary" id="uploadButton">
              <i class="mdi mdi-cloud-upload-outline me-1"></i> Yükle
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
