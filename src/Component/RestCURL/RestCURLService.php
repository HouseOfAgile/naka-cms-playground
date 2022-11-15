<?php

declare(strict_types=1);

namespace HouseOfAgile\NakaCMSBundle\Component\RestCURL;

use HouseOfAgile\NakaCMSBundle\Helper\LoggerCommandTrait;
use Psr\Log\LoggerInterface;

/**
 * RestCURLService class: useful to interact with rest API through CURL
 */
class RestCURLService
{
    use LoggerCommandTrait;

    protected $accessToken;
    protected $endPoint;
    protected $devMode;

    protected $response;
    protected $rateTotalCpuTime = 0;
    protected $rateTotalTime = 0;

    public function __construct(
        LoggerInterface $logger,
        $devMode
    ) {
        $this->logger = $logger;
        $this->devMode = $devMode;
        $this->endPoint = "http://api.plos.org/search";
    }

    public function getLatestResponse($asJson = true)
    {
        return json_encode($this->response, JSON_THROW_ON_ERROR);
    }

    public function getRateLimitToString()
    {
        return sprintf('[total_time: %s, total_cpu_time: %s]', $this->rateTotalTime, $this->rateTotalCpuTime);
    }

    public function addGetAccessToken()
    {
        return 'access_token=' . $this->accessToken;
    }

    public function addGetParameters()
    {
        return '?' . $this->addGetAccessToken();
    }

    public function updateDataAndHeaders($data, $headers)
    {
        $data['access_token'] = $this->accessToken;

        // By default this curl service works with json request in mind
        $jsonEncodedData = json_encode($data, JSON_THROW_ON_ERROR);

        return [$jsonEncodedData, $headers];
    }

    /**
     * getLatencySleepTime: return time of sleep times $coefficient based on rate data
     *
     * @param integer $coefficient
     * @return integer
     */
    public function getLatencySleepTime($coefficient = 10): int
    {
        if ($this->rateTotalCpuTime < 30 && $this->rateTotalTime < 30) {
            return random_int(12 * $coefficient, 24 * $coefficient);
        } else if ($this->rateTotalCpuTime > 80 || $this->rateTotalTime > 80) {
            return  random_int(60 * $coefficient, 120 * $coefficient);
        } else {
            return  random_int(20 * $coefficient, 40 * $coefficient);
        }
    }

    public function updateRateLimit($headers): bool
    {
        if ($headers) {
            if (!empty($headers['x-app-usage'])) {
                // dump($headers['x-app-usage'][0]["total_cputime"]);
                $rateLimit = json_decode($headers['x-app-usage'][0], null, 512, JSON_THROW_ON_ERROR);
                $this->rateTotalTime = $rateLimit->total_time;
                $this->rateTotalCpuTime = $rateLimit->total_cputime;
                $this->logger->info(sprintf('API rate limit: %s', json_encode($headers['x-app-usage'], JSON_THROW_ON_ERROR)));
            }
        }
        return true;
    }

    protected function postJsonCurl($url, $data = NULL, $headers = NULL)
    {
        list($data, $headers) = $this->updateDataAndHeaders($data, $headers);

        if (empty($headers)) {
            $headers = array();
            $headers[] = 'Content-Type: application/json';
        }

        return $this->executeCurl($url, 'POST', $data, $headers);
    }

    protected function getJsonCurl($url, $data = NULL, $headers = NULL)
    {
        list($data, $headers) = $this->updateDataAndHeaders($data, $headers);
        return $this->executeCurl($url . $this->addGetParameters(), 'GET', $data, $headers);
    }

    protected function deleteCurl($url, $headers = NULL)
    {
        $data = [];
        list($data, $headers) = $this->updateDataAndHeaders($data, $headers);
        return $this->executeCurl($url, 'DELETE',  $data, $headers);
    }

    protected function executeCurl($url, $method, $data = NULL, $headers = NULL)
    {
        if ($this->devMode) {
            dump($url, $method, $headers);
            dd($data);
        }
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        switch ($method) {
            case "GET":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }

        if (!empty($data)) {
            if (is_array($data)) {
                // We have a x-www-form-urlencode type of request, header whould be there
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            } else {
                // This is most likely a standard json post request
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $headers = [];

        // this function is called by curl for each header received
        curl_setopt(
            $ch,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use (&$headers) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;

                $headers[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
            }
        );

        curl_setopt($ch, CURLOPT_VERBOSE, true);

        // Log curl errors here
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $info = curl_getinfo($ch);
            dump($info);
            $this->logger->error(sprintf('Curl Request response [Error]: %s', $response));
            return null;
        }
        $this->logger->info(sprintf('Curl Request response: %s', $response));
        $this->updateRateLimit($headers);
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
