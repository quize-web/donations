<?php

namespace app\Core;


class cURL
{


  /**
   * @var array DEFAULT_OPTIONS
   */
  const DEFAULT_OPTIONS = [
    CURLOPT_RETURNTRANSFER => true
  ];


  /**
   * @var null|string $endpoint
   */
  private $endpoint = null;


  /**
   * cURL constructor.
   *
   * @param  string  $endpoint
   *
   * @return void
   */
  function __construct($endpoint)
  {
    $this->endpoint = $endpoint;
  }


  /**
   * @param  array        $data
   * @param  null|string  $endpoint
   *
   * @return string|boolean
   */
  public function sendAsPost($data, $endpoint = null)
  {
    if ($endpoint) $endpoint = $this->endpoint;

    $options = [
      CURLOPT_URL => $endpoint,
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_POST => true
    ];
    $ch = curl_init();
    curl_setopt_array($ch, (self::DEFAULT_OPTIONS + $options));

    $response = curl_exec($ch);
    return $response;
  }


}