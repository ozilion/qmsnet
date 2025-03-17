<?php

namespace App\Services;

use GuzzleHttp\Client;

class GeminiService
{
  private $apiUrl;
  private $apiKey;
  private $client;

  public function __construct()
  {
    $this->apiUrl = config('services.gemini.api_url', env('GEMINI_API_URL', 'https://api.gemini.com/v1'));
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

  /**
   * Example: Get Current Price of a Symbol
   */
  public function getCurrentPrice(string $symbol)
  {
    return $this->request("/pubticker/$symbol", 'GET');
  }
}
