<?php

namespace App\Services;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use stdClass;

class DriveInService
{
    /**
     * @param $word
     *
     * @return array
     */
    public function getSimilarityWords($word): array
    {
        // Actual Request
        $payload = $this->buildSimilarityPayload($word);
        $headers = $this->buildHeaders();
        $response = $this->makeRequest($payload, $headers);
        $results = $response->json('result');
        // Sometimes we get a null from the service...
        if (is_array($results)) {
            return $this->parseSimilarityResults($results);
        } else {
            return [];
        }
    }

    private function parseSimilarityResults(array $results): array
    {
        return array_map(function($item){
            return str_replace("_", " ", $item);
        }, $results);
    }
    private function buildSimilarityPayload($word): string
    {
        $payload = new stdClass();
        $payload->service = 'similarity';
        $payload->text = $word;
        $payload->parameters = new stdClass();
        $payload->parameters->lang = 'en';

        return json_encode($payload);
    }

    private function buildHeaders(): array
    {
        $headers = [];
        $headers['x-api-key'] = config('services.drivein.key');
        $headers['Accept'] = 'application/json';

        return $headers;
    }

    private function makeRequest($payload, $headers): PromiseInterface|Response
    {
        return Http::withHeaders($headers)
                        ->withBody($payload, 'application/json')
                        ->post(config('services.drivein.base'));
    }
}