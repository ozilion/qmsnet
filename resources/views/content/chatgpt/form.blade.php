@php use Illuminate\Support\Facades\DB; @endphp
@extends('layouts.layoutMaster')

@section('title', 'ChatGPT Api Test')


@section('vendor-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-select-bs5/select.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.css')}}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.7.0/styles/github.min.css">
@endsection

@section('page-style')
  <!-- Page -->
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-statistics.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-analytics.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/css/style.css')}}">
@endsection

@section('vendor-script')
  <script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/highlight.js@11.7.0/highlight.min.js"></script>
@endsection

@section('page-script')
  <script src="{{asset('assets/js/forms-qmsnet.js')}}"></script>
@endsection

@section('content')
  <div class="row gy-4 mb-4">
    <div class="col-xl-12">
      <!-- Fixed Header -->
      <div class="card">
        <div class="card-header">chatgptFORM</div>
        <div class="card-datatable table-responsive-sm pt-0 text-wrap">
          <form id="chatgptForm" onSubmit="return false">
            @csrf
            <input type="hidden" id="chatgptFormRoute" value="{{route('chatgptFORM')}}">
            <textarea class="form-control h-px-100" id="prompt" name="prompt" placeholder="Bir şeyler yazın...">{{ old('prompt', $prompt ?? '') }}</textarea>
            <label for="prompt">Prompt:</label><br>
            <p id="error" style="color: red; display: none;"></p>
            <br>
            <button type="button" class="btn btn-sm btn-success" onclick="chatgptForm()">Gönder</button>
          </form>

          <hr>

          <h2>API Yanıtı:</h2>
          <div id="responseOutput" style="white-space: pre-wrap;"></div>

        </div>
      </div>
    </div>
  </div>

  <div class="row gy-4 mb-4">
    <div class="col-xl-12">
      <!-- Fixed Header -->
      <div class="card">
        <div class="card-header">geminiFORM</div>
        <div class="card-datatable table-responsive-sm pt-0 text-wrap">
          <style>
            body {
              background-color: #f5f5f5;
            }
            .chat-container {
              /*max-width: 800px;*/
              margin: 0 auto;
              padding: 20px;
            }
            .chat-box {
              height: 400px;
              overflow-y: auto;
              padding: 15px;
              background-color: #fff;
              border-radius: 10px;
              box-shadow: 0 0 10px rgba(0,0,0,0.1);
              margin-bottom: 20px;
            }
            .message {
              margin-bottom: 15px;
              padding: 10px 15px;
              border-radius: 10px;
              width: fit-content;
              max-width: 70%;
            }
            .user-message {
              background-color: #DCF8C6;
              margin-left: auto;
            }
            .bot-message {
              background-color: #E8E8E8;
            }
            .loading {
              text-align: center;
              margin: 20px 0;
            }
            .input-container {
              display: flex;
              gap: 10px;
            }
            pre {
              white-space: pre-wrap;
              margin: 0;
              font-family: inherit;
            }
            /* Mevcut CSS stillerine ek olarak: */
            .markdown-content {
              line-height: 1.6;
            }
            .markdown-content pre {
              background-color: #f4f4f4;
              padding: 10px;
              border-radius: 5px;
              overflow-x: auto;
            }
            .markdown-content code {
              background-color: #f4f4f4;
              padding: 2px 4px;
              border-radius: 3px;
            }
            .markdown-content h1,
            .markdown-content h2,
            .markdown-content h3,
            .markdown-content h4,
            .markdown-content h5,
            .markdown-content h6 {
              margin-top: 16px;
              margin-bottom: 8px;
            }
            .markdown-content p {
              margin-bottom: 16px;
            }
            .markdown-content ul,
            .markdown-content ol {
              margin-left: 20px;
              margin-bottom: 16px;
            }
            .markdown-content blockquote {
              border-left: 4px solid #ddd;
              padding-left: 16px;
              margin-left: 0;
              color: #666;
            }
            .markdown-content img {
              max-width: 100%;
              height: auto;
            }
          </style>
          <!-- HTML kodu - Chat formuna model seçimi ekliyoruz -->
          <div class="container chat-container">
            <h1 class="text-center mb-4">Gemini Chat</h1>

            <!-- Model seçim dropdown'u -->
            <div class="model-selector mb-3">
              <label for="modelSelect" class="form-label">Gemini Modelini Seçin:</label>
              <select class="form-select" id="modelSelect">
                <option value="gemini-pro" selected>Gemini Pro</option>
                <option value="gemini-1.5-pro">Gemini 1.5 Pro</option>
                <option value="gemini-1.0-pro">Gemini 1.0 Pro</option>
                <option value="gemini-ultra">Gemini Ultra</option>
                <!-- Dinamik olarak yüklenecek diğer modeller -->
              </select>
              <div class="form-text">Kullanılabilir modeller otomatik olarak yüklenecektir.</div>
            </div>

            <div class="chat-box" id="chatBox">
              <div class="message bot-message">
                <pre>Merhaba! Bana istediğini sorabilirsin.</pre>
              </div>
            </div>

            <div class="loading d-none" id="loadingIndicator">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
              </div>
            </div>

            <form id="chatForm" onsubmit="return false;">
              @csrf
              <div class="input-container">
                <input type="text" class="form-control" id="promptInput" placeholder="Mesajınızı yazın..." required>
                <button type="button" id="submitGeminiBtn" class="btn btn-primary">Gönder</button>
              </div>
            </form>
          </div>

          <script>
            document.addEventListener('DOMContentLoaded', function() {
              // DOM elementlerini seçelim
              const chatBox = document.getElementById('chatBox');
              const promptInput = document.getElementById('promptInput');
              const loadingIndicator = document.getElementById('loadingIndicator');
              const submitButton = document.getElementById('submitGeminiBtn');
              const modelSelect = document.getElementById('modelSelect');

              // Sayfa yüklendiğinde kullanılabilir modelleri yükle
              loadAvailableModels();

              // Kullanılabilir modelleri yükleme fonksiyonu
              function loadAvailableModels() {
                // API anahtarı doğrudan JavaScript kodunda kullanılmamalı,
                // bunun yerine bir backend endpoint kullanın
                fetch('{{ route("geminiModels") }}')
                  .then(response => response.json())
                  .then(data => {
                    if (data.success && data.models && data.models.models) {
                      // Mevcut seçenekleri temizle (ilk seçenek hariç)
                      while (modelSelect.options.length > 1) {
                        modelSelect.remove(1);
                      }

                      // API'den gelen modelleri ekle
                      data.models.models.forEach(model => {
                        const option = document.createElement('option');
                        option.value = model.name.split('/').pop(); // "models/gemini-pro" -> "gemini-pro"
                        option.textContent = model.displayName || model.name;

                        // Zaten varsa ekleme
                        if (!modelExists(option.value)) {
                          modelSelect.appendChild(option);
                        }
                      });
                    } else {
                      console.error('Modeller yüklenemedi:', data.message || 'Bilinmeyen hata');
                    }
                  })
                  .catch(error => {
                    console.error('Model listesi alınamadı:', error);
                  });
              }

              // Model zaten select'te var mı kontrol et
              function modelExists(modelValue) {
                for (let i = 0; i < modelSelect.options.length; i++) {
                  if (modelSelect.options[i].value === modelValue) {
                    return true;
                  }
                }
                return false;
              }

              // Sohbet kutusu otomatik olarak aşağı kaydırılır
              function scrollToBottom() {
                chatBox.scrollTop = chatBox.scrollHeight;
              }

              // Kullanıcı mesajı ekleme
              function addUserMessage(message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message user-message';

                const preElement = document.createElement('pre');
                preElement.textContent = message;

                messageDiv.appendChild(preElement);
                chatBox.appendChild(messageDiv);

                scrollToBottom();
              }

              // Kod vurgulama işlemi
              function highlightCode() {
                document.querySelectorAll('.markdown-content pre code').forEach(function(el) {
                  hljs.highlightElement(el);
                });
              }

              // Bot mesajı ekleme
              function addBotMessage(message) {
                // Markdown'ı HTML'e dönüştür
                const htmlContent = marked.parse(message);

                const messageDiv = document.createElement('div');
                messageDiv.className = 'message bot-message';

                const contentDiv = document.createElement('div');
                contentDiv.className = 'markdown-content';
                contentDiv.innerHTML = htmlContent;

                messageDiv.appendChild(contentDiv);
                chatBox.appendChild(messageDiv);

                scrollToBottom();
                highlightCode();
              }

              // API isteği gönderme fonksiyonu
              function sendChatRequest(prompt) {
                // Seçilen modeli al
                const selectedModel = modelSelect.value;

                // CSRF token'ı al
                const token = document.querySelector('input[name="_token"]').value;

                // Yükleniyor göstergesini göster
                loadingIndicator.classList.remove('d-none');

                // Form verilerini oluştur
                const formData = new FormData();
                formData.append('prompt', prompt);
                formData.append('model', selectedModel);
                formData.append('_token', token);

                // API isteği
                fetch('{{ route("geminiFORM") }}', {
                  method: 'POST',
                  headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                  },
                  body: formData
                })
                  .then(response => response.json())
                  .then(data => {
                    if (data.success) {
                      addBotMessage(data.response);
                    } else {
                      addBotMessage('Üzgünüm, bir hata oluştu: ' + data.message);
                    }
                  })
                  .catch(error => {
                    addBotMessage('Üzgünüm, bir bağlantı hatası oluştu: ' + error.message);
                  })
                  .finally(() => {
                    // Yükleniyor göstergesini gizle
                    loadingIndicator.classList.add('d-none');
                  });
              }

              // Gönder butonuna tıklama olayı
              if (submitButton) {
                submitButton.addEventListener('click', function() {
                  const prompt = promptInput.value.trim();
                  if (!prompt) return;

                  // Kullanıcı mesajını ekle
                  addUserMessage(prompt);

                  // Input alanını temizle
                  promptInput.value = '';

                  // İsteği gönder
                  sendChatRequest(prompt);
                });
              }

              // Enter tuşuna basıldığında
              if (promptInput) {
                promptInput.addEventListener('keypress', function(e) {
                  if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    if (submitButton) submitButton.click();
                  }
                });
              }
            });
          </script>
        </div>
      </div>
    </div>
  </div>

  <div class="row gy-4 mb-4">
    <div class="col-xl-12">
      <!-- Fixed Header -->
      <div class="card">
        <div class="card-header">deepseekFORM</div>
        <div class="card-datatable table-responsive-sm pt-0 text-wrap">
          <form id="deepseekForm" onSubmit="return false">
            {{ csrf_field() }}
            <input type="hidden" id="deepseekFormRoute" value="{{route('deepseekFORM')}}">
            <textarea class="form-control h-px-100" id="deepseekprompt" name="deepseekprompt" placeholder="Bir şeyler yazın...">{{ old('deepseekprompt', $deepseekprompt ?? '') }}</textarea>
            <label for="deepseekprompt">Prompt:</label><br>
            <p id="deepseekerror" style="color: red; display: none;"></p>
            <br>
            <button type="button" class="btn btn-sm btn-success" onclick="deepseekForm()">Gönder</button>
          </form>

          <hr>

          <h2>API Yanıtı:</h2>
          <div id="deepseekresponseOutput" style="white-space: pre-wrap;"></div>

        </div>
      </div>
    </div>
  </div>

@endsection
