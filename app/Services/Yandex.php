<?php

namespace app\Services;


class Yandex
{


  /* CONSTANTS */


  /**
   * @var string ENDPOINT
   */
  const ENDPOINT = "https://money.yandex.ru/eshop.xml";
//  const ENDPOINT = "/callback.php"; # отладка


  /**
   * @var string SUCCESS
   */
  const SUCCESS = "callback.php";

  /**
   * Приоритет от большего к меньшему:
   * GET-параметр -> env-параметр -> константа класса
   *
   * @var integer SHOP_ID
   */
  const SHOP_ID = 25634;

  /**
   * Приоритет от большего к меньшему:
   * GET-параметр -> env-параметр -> константа класса
   *
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


  /**
   * Приоритет от большего к меньшему:
   * GET-параметр -> env-параметр -> константа класса
   *
   * @var null|integer $shopID
   */
  private static $shopID = null;


  /**
   * Приоритет от большего к меньшему:
   * GET-параметр -> env-параметр -> константа класса
   *
   * @var null|integer $scID
   */
  private static $scid = null;


  /* METHODS */


  /**
   * Yandex constructor.
   *
   * @param  array       $data
   * @param  Names|null  $names
   */
  function __construct($data, $names = null)
  {
    $this->rawData = $data;
    $this->names = $names;
    self::setData();
  }


  ### public


  /**
   * Приоритет от большего к меньшему:
   * GET-параметр -> env-параметр -> константа класса
   *
   * @return integer|null
   */
  public static function getShopID()
  {
    if (self::$shopID === null) {
      if (isset($_GET["shop-id"]) && $_GET["shop-id"]) self::$shopID = $_GET["shop-id"];
      else self::$shopID = env("SHOP_ID", self::SHOP_ID);
    }
    return self::$shopID;
  }


  /**
   * Приоритет от большего к меньшему:
   * GET-параметр -> env-параметр -> константа класса
   *
   * @return integer|null
   */
  public static function getSCID()
  {
    if (self::$scid === null) {
      if (isset($_GET["scid"]) && $_GET["scid"]) self::$scid = $_GET["scid"];
      else self::$scid = env("SCID", self::SCID);
    }
    return self::$scid;
  }


  /**
   * @return array
   */
  public function getData()
  {
    return $this->data;
  }


  ### private


  /**
   * @return void
   */
  private function setData()
  {
    $result = [
      "shopId" => Yandex::getShopID(),
      "scid" => Yandex::getSCID(),
      "sum" => $this->rawData["sum"],
      "orderDetails" => "Пожертвование",
      "customerNumber" => $this->rawData["customer-name"],
      "orderNumber" => ($this->rawData["customer-name"] . " --- " . time()),
      "shopSuccessURL" => self::getDefaultURL(),
      "shopFailURL" => self::getFailURL(),
      "shopDefaultUrl" => self::getDefaultURL(),
    ];

    if ($this->names) {
      $result["sum"] = $this->names->getTotal();
      $result["orderDetails"] = $this->names->getAsString();
      $result["orderNumber"] = $this->names->getFileName();
      $result["shopSuccessURL"] = self::getSuccessURL($this->names->getFileName());
    }

    $this->data = $result;
  }


  ### private


  /**
   * @param  string  $orderNumber
   *
   * @return string
   */
  private static function getSuccessURL($orderNumber)
  {
    return (env('HOST') . "/" . self::SUCCESS . "?orderNumber={$orderNumber}");
  }


  /**
   * @return string
   */
  private static function getFailURL()
  {
    return env('HOST');
  }


  /**
   * @return string
   */
  private static function getDefaultURL()
  {
    return env('HOST');
  }


}