<?php

namespace app\Services;


class Yandex
{


  /* CONSTANTS */


  /**
   * @var string ENDPOINT
   */
//  const ENDPOINT = "https://money.yandex.ru/eshop.xml";
  const ENDPOINT = "/callback.php"; # отладка

  /**
   * @var integer SHOP_ID
   */
  const SHOP_ID = 25634;

  /**
   * @var integer SCID
   */
  const SCID = 33560;


  /* PROPERTIES */


  /**
   * @var null|Names $names
   */
  private $names = null;


  /**
   * @var array $rawData
   */
  private $rawData = [];


  /**
   * @var array $rawData
   */
  private $data = [];


  /* METHODS */


  function __construct(array $data, ?Names $names = null)
  {
    $this->rawData = $data;
    $this->names = $names;
    self::setData();
  }


  ### public


  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->data;
  }


  ### private


  /**
   * @return void
   */
  private function setData(): void
  {
    $result = [
      "shopId" => $this->rawData["shopId"],
      "scid" => $this->rawData["scid"],
      "sum" => $this->rawData["sum"],
      "orderDetails" => "Пожертвование",
      "customerNumber" => $this->rawData["customer-name"],
      "orderNumber" => ($this->rawData["customer-name"] . " --- " . time()),
    ];

    if ($this->names) {
      $result["sum"] = $this->names->getTotal();
      $result["orderDetails"] = $this->names->getAsString();
      $result["orderNumber"] = $this->names->getFileName();
    }

    $this->data = $result;
  }


}