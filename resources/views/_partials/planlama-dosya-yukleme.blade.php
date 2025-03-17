<!-- resources/views/partials/planlama-menu.blade.php -->


  <div class="offcanvas offcanvas-end" id="offcanvasPlanlamadosyasiUpload" aria-hidden="true">
    <div class="offcanvas-header mb-3">
      <h5 class="offcanvas-title">Dosya Yükle</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
      <div class="card">
        <h5 class="card-header"> </h5>
        <div class="card-body">
          <form id="dropzone-multi-denetim" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
                      <input type="hidden" id="formDenetimPaketiDosyasiYukleRoute" value="{{route('denetim-dosyasi-upload')}}">
            <input type="text" name="pno" id="pno" class="form-control" value="{{$pno}}">
            <input type="text" name="asama" id="asama" class="form-control" value="{{$asama}}">

            <div class="text-end mb-4">
              <button type="button" class="btn btn-primary" id="uploadDenetimPaketButton">Yükle</button>
            </div>
            <div class="form-floating form-floating-outline mb-4">
              <div class="dz-message needsclick">
                Yükleme işlemi için dosyayı yükleyin veya buraya tıklayın
                <span class="note needsclick"></span>
              </div>
              <div class="fallback">
                <input name="file" type="file" />
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
