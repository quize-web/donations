<?php

namespace app\Services;

use XMLWriter;
use app\Core\XML;


class Names
{


  /* CONSTANTS */


  /**
   * @var string DEFAULT
   */
  const DEFAULT = "health";

  /**
   * @var array TYPES
   */
  const TYPES = [
    "health" => [
      "title" => "О здравии",
      "count" => 10,
      "cost" => 180,
      "foreach" => false
    ],
    "rest" => [
      "title" => "О Упокоении",
      "count" => 10,
      "cost" => 180,
      "foreach" => false
    ],
    "forty" => [
      "title" => "Сорокоуст",
      "count" => 10,
      "cost" => 180,
      "foreach" => true
    ],
    "donation" => [
      "title" => "Пожертвование",
      "count" => 0,
      "cost" => 0,
      "foreach" => false
    ]
  ];


  /**
   * @var array FOLDERS
   */
  const FOLDERS = [
    "root" => "names",
    "types" => [
      "payed" => "payed",
      "archive" => "archive",
      "new" => "new"
    ]
  ];


  /* PROPERTIES */


  /**
   * @var null|integer $timestamp
   */
  private $timestamp = null;

  /**
   * @var null|string $customerName
   */
  private $customerName = null;

  /**
   * @var null|string $fileName
   */
  private $fileName = null;

  /**
   * @var null|string $orderType
   */
  private $orderType = null;

  /**
   * @var array $names
   */
  private $names = [];

  /**
   * @var boolean $valid
   */
  private $valid = true;

  /**
   * @var integer $total
   */
  private $total = 0;

  /**
   * @var null|string $string
   */
  private $string = null;


  /* METHODS */


  /**
   * @param  string      $customerName
   * @param  null|array  $names
   */
  function __construct(string $customerName, ?array $names = null)
  {
    if ($names) { # создаем новую записку

      $this->timestamp = time();
      $this->customerName = $customerName;
      $this->orderType = key($names);
      $this->names = self::handleNames(current($names));

      $this->setValid();
      $this->setTotal();

    } else { # существущая записка

      $this->fileName = $customerName;

    }
  }


  ### public


  /**
   * @return void
   */
  public function createNew(): void
  {
    if ($this->valid) {
      $fileFullPath = $this->makeFileFullPath("new");
      $XML = new XML($fileFullPath, function (XMLWriter $writer) {

        $writer->writeElement("timestamp", $this->orderType);
        $writer->writeElement("timestamp", $this->timestamp);
        $writer->writeElement("payed", "false");
        $writer->writeElement("total", $this->getTotal());

        $writer->startElement("names");
        if ($this->names) {
          foreach ($this->names as $name) {
            $writer->writeElement("name", $name);
          }
        }
        $writer->endElement();

      });
    }
  }


  public function markAsPayed()
  {
    //
  }


  /**
   * @return integer
   */
  public function getTotal(): int
  {
    return $this->total;
  }


  /**
   * @return null|string
   */
  public function getFileName(): ?string
  {
    return $this->fileName;
  }


  /**
   * @return null|string
   */
  public function getAsString(): ?string
  {
    if ($this->string === null) {
      $this->string = (self::TYPES[$this->orderType]["title"] . ": " . implode(", ", $this->names));
    }
    return $this->string;
  }


  ### private


  /**
   * @return void
   */
  private function setValid(): void
  {
    if (empty($this->names) || (array_key_exists($this->orderType, self::TYPES) === false)) {
      # если пустые имена или такого типа записок нет - ошибка
      $this->valid = false;
    }
  }


  /**
   * @param  string  $type
   *
   * @return string
   */
  private function makeFileFullPath(string $type): string
  {
    if ($this->fileName === null) { # формируем хеш файла (при новой записке)
      $salt = ($this->customerName . $this->orderType . $this->timestamp);
      $this->fileName = md5($salt);
    } # при существующей - хеш уже известен и находится в $this->fileName (см. конструктор)

    return (self::makeFullPath($type) . $this->fileName . ".xml");
  }


  /**
   * @return void
   */
  private function setTotal(): void
  {
    $this->total = self::TYPES[$this->orderType]["cost"];
    if (self::TYPES[$this->orderType]["foreach"]) $this->total *= count($this->names);
  }


  /**
   * @param  string  $type
   *
   * @return string
   */
  private static function makeFullPath(string $type): string
  {
    $path = implode(DS, [self::FOLDERS["root"], self::FOLDERS["types"][$type]]);
    return (DOCROOT . DS . "storage" . DS . $path . DS);
  }


  /**
   * @param  array  $names
   *
   * @return array
   */
  private static function handleNames(array $names): array
  {
    return array_filter($names);
  }


}