<?php

namespace App\Http\Controllers;

use DeepseekClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatGPTController extends Controller
{
  //$apiKey = "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";
  // Formun gösterileceği sayfa
  public function index()
  {
    return view('content.chatgpt.form');
  }

  // API'den yanıt alma - Model:  o1-preview-2024-09-12
  public function getResponse(Request $request)
  {
    $request->validate([
      'prompt' => 'required|string|max:5000',
    ]);

    $prompt = $request->input('prompt');
    $apiKey = env('OPENAI_API_KEY'); // API anahtarını .env dosyasından al

    $response = Http::withHeaders([
      'Authorization' => "Bearer $apiKey",
      'Content-Type' => 'application/json',
    ])->post('https://api.openai.com/v1/chat/completions', [
      'model' => 'o1-preview-2024-09-12',
      'messages' => [
        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
        ['role' => 'user', 'content' => $prompt],
      ],
    ]);

    if ($response->successful()) {
      $result = $response->json();
      $output = $result['choices'][0]['message']['content'] ?? 'Yanıt bulunamadı.';
      return response()->json(['output' => $output]);
    } else {
      return response()->json(['error' => 'API isteğinde bir hata oluştu.'], 500);
    }
  }

  public function getDeepseekResponse(Request $request)
  {
    $request->validate([
      'prompt' => 'required|string|max:5000',
    ]);

    $prompt = $request->input('prompt');
//    $deepseek = app(DeepseekClient::class);
//    $response = $deepseek->query($prompt)->run();

    $deepseek = app(DeepseekClient::class);

// Another way, with customization
    $response = $deepseek
      ->query('Hello deepseek, how are you ?', 'system')
      ->query($prompt, 'user')
      ->withModel("deepseek-chat")
      ->setTemperature(1.5)
      ->run();

    print_r("deepseek API response : " . $response);
    return response()->json(['output' => $response]);
  }

}
