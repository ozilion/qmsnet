<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
  private $apiUrl;
  private $apiKey;
  private $client;

  public function __construct()
  {
    $this->apiUrl = config('services.gemini.api_url', env('GOOGLE_AI_API_KEY', 'https://api.gemini.com/v1'));
    $this->apiKey = env('GEMINI_API_KEY');
    $this->client = new Client();
  }

  /**
   * Perform an API request to Gemini
   */
  public function request(string $endpoint, string $method = 'GET', array $params = [])
  {
    $url = $this->apiUrl . $endpoint;

    $headers = [
      'Content-Type' => 'application/json',
      'X-GEMINI-APIKEY' => $this->apiKey,
    ];

    $options = [
      'headers' => $headers,
    ];

    if ($method === 'GET' && !empty($params)) {
      $url .= '?' . http_build_query($params);
    } elseif ($method === 'POST') {
      $options['json'] = $params;
    }

    $response = $this->client->request($method, $url, $options);

    return json_decode($response->getBody(), true);
  }

  public function handlePrompt1(Request $request)
  {
    $promptData = $request->input('prompt');

    if (empty($promptData['text'])) {
      return response()->json(['success' => false, 'message' => 'Prompt alanı zorunludur.']);
    }


    try {
      // Google Generative Language API isteği
      $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=';
      $apiKey = env('GOOGLE_AI_API_KEY'); // .env dosyasına API anahtarınızı ekleyin

      $response = Http::post($apiUrl . $apiKey, [
        'prompt' => [
          'text' => $promptData['text']
        ]
      ]);

      if ($response->successful()) {
        return response()->json([
          'success' => true,
          'data' => $response->json()
        ]);
      } else {
        return response()->json([
          'success' => false,
          'message' => 'API çağrısı başarısız oldu.',
          'error' => $response->body()
        ]);
      }
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Bir hata oluştu.',
        'error' => $e->getMessage()
      ]);
    }
  }

  public function handlePrompt(Request $request)
  {
    $request->validate([
      'prompt' => 'required|string',
      'model' => 'nullable|string',
    ]);

    $prompt = $request->input('prompt');
    $selectedModel = $request->input('model', 'gemini-pro'); // Varsayılan olarak gemini-pro
    $apiKey = env('GOOGLE_AI_API_KEY');

    if (empty($apiKey)) {
      return response()->json([
        'success' => false,
        'message' => 'API anahtarı tanımlanmamış. .env dosyasında GOOGLE_AI_API_KEY değişkenini ayarlayın.'
      ], 500);
    }

    try {
      // API isteğini oluştur
      $requestData = [
        'contents' => [
          [
            'parts' => [
              [
                'text' => $prompt
              ]
            ]
          ]
        ]
      ];

      // İstek verilerini logla (debug için)
      Log::info('Gemini API isteği: ' . json_encode($requestData));
      Log::info('Seçilen model: ' . $selectedModel);

      // Seçilen model ile API endpoint'ini oluştur
      $apiUrl = "https://generativelanguage.googleapis.com/v1/models/{$selectedModel}:generateContent?key={$apiKey}";
      Log::info('API URL: ' . $apiUrl);

      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
      ])->post($apiUrl, $requestData);

      // Yanıtı logla
      Log::info('API yanıtı: ' . $response->body());

      // Eğer seçilen model ile başarısız olursa, varsayılan modelleri dene
      if (!$response->successful()) {
        $fallbackModels = [
          'gemini-pro',
          'gemini-1.5-pro',
          'gemini-1.0-pro',
          'gemini-ultra'
        ];

        // Zaten denenen modeli çıkar
        $fallbackModels = array_diff($fallbackModels, [$selectedModel]);

        foreach ($fallbackModels as $model) {
          $fallbackUrl = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";
          Log::info('Alternatif model deneniyor: ' . $model);

          $response = Http::withHeaders([
            'Content-Type' => 'application/json',
          ])->post($fallbackUrl, $requestData);

          Log::info('Alternatif yanıt: ' . $response->body());

          if ($response->successful()) {
            Log::info('Başarılı alternatif model bulundu: ' . $model);
            break;
          }
        }
      }

      // Yanıtı analiz et
      if ($response->successful()) {
        $data = $response->json();

        // API yanıt yapısını logla
        Log::info('API yanıt yapısı: ' . json_encode($data));

        // Yanıt verisini çıkar
        $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($responseText) {
          return response()->json([
            'success' => true,
            'response' => $responseText,
            'model' => $selectedModel // Hangi modelin kullanıldığını bildir
          ]);
        } else {
          return response()->json([
            'success' => false,
            'message' => 'API yanıtında beklenen veri yapısı bulunamadı. Yanıt: ' . json_encode($data)
          ], 500);
        }
      }

      return response()->json([
        'success' => false,
        'message' => 'API yanıtı başarısız oldu. Hata: ' . $response->body()
      ], 500);

    } catch (\Exception $e) {
      Log::error('Gemini API hatası: ' . $e->getMessage());
      Log::error($e->getTraceAsString());

      return response()->json([
        'success' => false,
        'message' => 'Bir hata oluştu: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Gemini API modellerini listeler
   */
  public function listModels()
  {
    $apiKey = env('GOOGLE_AI_API_KEY');

    if (empty($apiKey)) {
      return response()->json([
        'success' => false,
        'message' => 'API anahtarı tanımlanmamış. .env dosyasında GOOGLE_AI_API_KEY değişkenini ayarlayın.'
      ], 500);
    }

    try {
      // API'den mevcut modelleri al
      $response = Http::get("https://generativelanguage.googleapis.com/v1/models?key={$apiKey}");

      // API yanıt yapısını logla
      Log::info('Modeller yanıtı: ' . $response->body());

      if ($response->successful()) {
        $modelsData = $response->json();

        // Varsayılan modelleri ekle (API yanıtında olmama ihtimaline karşı)
        $defaultModels = [
          [
            'name' => 'models/gemini-pro',
            'displayName' => 'Gemini Pro',
            'description' => 'Varsayılan Gemini Pro modeli'
          ],
          [
            'name' => 'models/gemini-1.5-pro',
            'displayName' => 'Gemini 1.5 Pro',
            'description' => 'Gemini 1.5 Pro modeli'
          ],
          [
            'name' => 'models/gemini-1.0-pro',
            'displayName' => 'Gemini 1.0 Pro',
            'description' => 'Gemini 1.0 Pro modeli'
          ],
          [
            'name' => 'models/gemini-ultra',
            'displayName' => 'Gemini Ultra',
            'description' => 'Gemini Ultra modeli'
          ]
        ];

        // API'den gelen modeller boşsa veya models özelliği yoksa varsayılanları kullan
        if (!isset($modelsData['models']) || empty($modelsData['models'])) {
          $modelsData['models'] = $defaultModels;
        } else {
          // API'den gelen modellerde olmayan varsayılan modelleri ekle
          $existingModelNames = array_column($modelsData['models'], 'name');
          foreach ($defaultModels as $defaultModel) {
            if (!in_array($defaultModel['name'], $existingModelNames)) {
              $modelsData['models'][] = $defaultModel;
            }
          }
        }

        return response()->json([
          'success' => true,
          'models' => $modelsData
        ]);
      }

      return response()->json([
        'success' => false,
        'message' => 'Modeller alınamadı: ' . $response->body()
      ], 500);

    } catch (\Exception $e) {
      Log::error('Model listesi alınamadı: ' . $e->getMessage());

      // Hata durumunda varsayılan modelleri döndür
      $defaultModels = [
        'models' => [
          [
            'name' => 'models/gemini-pro',
            'displayName' => 'Gemini Pro',
            'description' => 'Varsayılan Gemini Pro modeli'
          ],
          [
            'name' => 'models/gemini-1.5-pro',
            'displayName' => 'Gemini 1.5 Pro',
            'description' => 'Gemini 1.5 Pro modeli'
          ],
          [
            'name' => 'models/gemini-1.0-pro',
            'displayName' => 'Gemini 1.0 Pro',
            'description' => 'Gemini 1.0 Pro modeli'
          ],
          [
            'name' => 'models/gemini-ultra',
            'displayName' => 'Gemini Ultra',
            'description' => 'Gemini Ultra modeli'
          ]
        ]
      ];

      return response()->json([
        'success' => true,
        'models' => $defaultModels,
        'warning' => 'API hatası nedeniyle varsayılan model listesi kullanılıyor: ' . $e->getMessage()
      ]);
    }
  }

  public function getAnythingllmResponse(Request $request)
  {
    // Girdi doğrulama
    $validatedData = $request->validate([
      'message' => 'required|string',
      'mode' => 'required|in:query,chat',
      'sessionId' => 'required|string',
      'attachments' => 'array',
      'attachments.*.name' => 'string',
      'attachments.*.mime' => 'string',
      'attachments.*.contentString' => 'string',
    ]);

    // API anahtarını .env'den al
    $apiKey = env('ANYTHINGLLM_API_KEY');

    // Sabit sessionId değeri
    $sessionId = 'default-session-id';

    if (!$apiKey) {
      return response()->json(['error' => 'API key is missing.'], 500);
    }

    // API isteğini gönder
    $response = Http::withHeaders([
      'Content-Type' => 'application/json',
      'Authorization' => "Bearer $apiKey"
    ])->post('https://20wqjx2n.rpcld.co/api/v1/workspace/qmsnet/chat', $validatedData);

    if ($response->successful()) {
      return response()->json($response->json());
    } else {
      return response()->json([
        'error' => 'API call failed.',
        'details' => $response->json()
      ], $response->status());
    }
  }
}
