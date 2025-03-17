<!-- File Preview Modal -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filePreviewModalTitle">Dosya Önizleme</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Loading indicator -->
        <div class="text-center py-5" id="filePreviewLoading">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Dosyalar yükleniyor...</p>
        </div>

        <!-- Empty state message -->
        <div class="text-center py-5 d-none" id="filePreviewEmpty">
          <i class="mdi mdi-file-outline mdi-48px text-muted"></i>
          <p class="mt-2">Bu klasörde henüz dosya bulunmamaktadır.</p>
        </div>

        <!-- File container -->
        <div class="row file-preview-container">
          <!-- Files will be dynamically added here -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
