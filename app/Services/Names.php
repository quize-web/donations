<?php

namespace app\Services;

use XMLWriter;
use app\Core\XML;
use XMLReader;
use DOMDocument;
use DOMNodeList;


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
      "title" => "О Здравии",
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


  /**
   * @var string AUTH_HASH
   */
  const AUTH_HASH = "password";


  /**
   * 40 дней
   *
   * @var integer FORTY
   */
  const FORTY = (60 * 60 * 24 * 41);


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

      $this->setNames(current($names));
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
      $XML = new XML($fileFullPath);
      $XML->write(function (XMLWriter $writer) {

        $writer->writeElement("orderType", $this->orderType);
        $writer->writeElement("timestamp", $this->timestamp);
        $writer->writeElement("payed", "false");
        $writer->writeElement("total", $this->getTotal());

        self::writeNamesToXML($writer, $this->names);

      });
    }
  }


  /**
   * @return void
   */
  public function markAsPayed(): void
  {
    $orderFileFullPath = $this->makeFileFullPath("new");
    if (file_exists($orderFileFullPath)) { # записка существует в папке с неоплаченными записками

      # считываем с него данные в свойства класса
      $this->retrieveFromXML($orderFileFullPath);

      # модифицируем существующи файл
      $mainFileFullPath = self::getMainFileFullPath($this->orderType);
      $this->appendNamesToFile($mainFileFullPath);

      # переносим файл записки в архив
      self::moveToArchive($orderFileFullPath, $this->orderType, $this->fileName);
//      exit(); # отладка

    }
  }


  /**
   * @param  string  $type
   */
  public static function moveMainFileToArchive(string $type): void
  {
    $mainFileFullPath = self::getMainFileFullPath($type);
    self::moveToArchive($mainFileFullPath, $type, time());
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


  /**
   * @return array
   */
  public static function getNames(): array
  {
    $result = [];
    $mainFiles = self::getMainFiles();
    if ($mainFiles) {
      foreach ($mainFiles as $fileFullPath) { # обходим каждый файл из папки
        $type = basename($fileFullPath, ".xml");

        $XML = new XML($fileFullPath); # открываем XML-файл
        $XML->modify(function (DOMDocument $dom) use ($type, &$result) {
          $namesNode = $dom->getElementsByTagName("name");

          # запись[ и фильтрация] имен в массив
          if ($type === "forty") { # Сорокоуст (с фильтрацией уже неактуальных)
            $result[$type] = self::filterNamesXML($namesNode, $type);
          } else { # Обычные записки
            foreach ($namesNode as $nameNode) $result[$type][] = $nameNode->textContent;
          }

        });

      }
    }
    return $result;
  }


  /**
   * @param  integer  $createdAt
   * @param  boolean  $format
   *
   * @return integer|string
   */
  public static function getEndDate(int $createdAt, bool $format = false)
  {
    $endData = ($createdAt + self::FORTY);
    return ($format ? date('d.m.Y', $endData) : $endData);
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
   * @param  null|integer  $total
   *
   * @return void
   */
  private function setTotal(?int $total = null): void
  {
    if ($total) $this->total = $total;
    else {

      $this->total = self::TYPES[$this->orderType]["cost"];
      if (self::TYPES[$this->orderType]["foreach"]) $this->total *= count($this->names);

    }
  }


  /**
   * @param  array  $names
   *
   * @return void
   */
  private function setNames(array $names): void
  {
    $this->names = self::handleNames($names);
  }


  /**
   * @param  XMLWriter      $writer
   * @param  array|boolean  $withDate
   * @param  string[]       $names
   *
   * @return void
   */
  private static function writeNamesToXML(XMLWriter $writer, array $names, $withDate = false): void
  {
    $writer->startElement("names");
    if ($names) {
      foreach ($names as $name) {

        if ($withDate) {
          $writer->startElement("name");
          $writer->writeElement("value", $name);
          if (is_array($withDate)) $writer->writeElement(key($withDate), current($withDate));
          else $writer->writeElement("created_at", time());
          $writer->endElement();
        } else $writer->writeElement("name", $name);

      }
    }
    $writer->endElement();
  }


  /**
   * @param  string         $fileFullPath
   * @param  array|boolean  $withDate
   * @param  array|null     $names
   *
   * @return void
   */
  private static function createNamesXML(string $fileFullPath, $withDate = false, ?array $names = null): void
  {
    $XML = new XML($fileFullPath);
    $XML->write(function (XMLWriter $writer) use ($withDate, $names) {
      self::writeNamesToXML($writer, $names, $withDate);
    });
  }


  /**
   * @param  DOMDocument    $dom
   * @param  array|boolean  $withDate
   * @param  null|string[]  $names
   *
   * @return void
   */
  private function appendNamesToXML(DOMDocument $dom, $withDate = false, ?array $names = null): void
  {
    $namesNode = $dom->documentElement->getElementsByTagName("names")->item(0);
    $names = ($names ?? $this->names);
    if ($names) {
      foreach ($names as $name) {

        if ($withDate) {
          $valueNode = $dom->createElement("value", $name);
          if (is_array($withDate)) $createdAtNode = $dom->createElement(key($withDate), current($withDate));
          else $createdAtNode = $dom->createElement("created_at", time());
          $nameNode = $dom->createElement("name");
          $nameNode->appendChild($valueNode);
          $nameNode->appendChild($createdAtNode);
        } else $nameNode = $dom->createElement("name", $name);

        $namesNode->appendChild($nameNode);
      }
    }
  }


  /**
   * @param  string  $sourceFileFullPath
   * @param  string  $orderType
   * @param  string  $archiveFileName
   *
   * @return void
   */
  private static function moveToArchive(string $sourceFileFullPath, string $orderType, string $archiveFileName): void
  {
    # формируем полный путь до файла и выполняем перенос
    $archiveFileFullPath = self::makeArchiveFileFullPath($orderType, $archiveFileName);
    rename($sourceFileFullPath, $archiveFileFullPath);
  }


  /**
   * Вставляем список имен в общий файл
   *
   * @param  string         $fileFullPath
   * @param  null|string[]  $names
   *
   * @return void
   */
  private function appendNamesToFile(string $fileFullPath, ?array $names = null): void
  {
    $names = ($names ?? $this->names);
    $withDate = ($this->orderType === 'forty'); # пишем дополнительно дату добавления каждого имени (для Сорокоуста)
    if (file_exists($fileFullPath)) {

      # главный файл с записками существует - модифицируем его
      $XML = new XML($fileFullPath);
      $XML->modify(function (DOMDocument $dom) use ($withDate, $names) {
        $this->appendNamesToXML($dom, $withDate, $names);
      });

    } else {

      # главный файл с записками НЕ существует - создаем его
      self::createNamesXML($fileFullPath, $withDate, $names);

    }
  }


  /**
   * Извлекаем данные о записке в свойства класса
   *
   * @param  string  $orderFileFullPath
   *
   * @return void
   */
  private function retrieveFromXML(string $orderFileFullPath): void
  {
    $XML = simplexml_load_file($orderFileFullPath);

    $this->orderType = (string)$XML->orderType;
    $this->timestamp = (string)$XML->timestamp;

    $this->setNames((array)current($XML->names));
    $this->setTotal((int)$XML->total);

    unset($XML);
  }


  /**
   * @param  string  $orderType
   *
   * @return string
   */
  private static function getMainFileFullPath(string $orderType): string
  {
    $fullPath = self::makeFullPath("payed"); # директория файла
    return ($fullPath . $orderType . ".xml"); # полный путь до файла
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
   * @param  string  $orderType
   *
   * @return string
   */
  private static function makeArchiveFullPath(string $orderType): string
  {
    $archivePath = (date('Y-m-d') . DS . $orderType);
    $archiveFullPath = (self::makeFullPath("archive") . $archivePath . DS);

    if (file_exists($archiveFullPath) === false) mkdir($archiveFullPath, 0777, true);
    return $archiveFullPath;
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


  /**
   * @return array
   */
  private static function getMainFiles(): array
  {
    $fullPath = self::makeFullPath("payed");
    return array_filter(glob($fullPath . "*.xml"));
  }


  /**
   * @param  DOMNodeList  $namesNode
   * @param  string       $type
   *
   * @return array
   */
  private static function filterNamesXML(DOMNodeList $namesNode, string $type): array
  {
    $result = [];
    $archiveNodes = [];

    # получение актуальных имен, фильтрация в массив не актуальных

    foreach ($namesNode as $nameNode) {

      $createdAt = $nameNode->getElementsByTagName('created_at')->item(0)->textContent;
      if (self::isExpired($createdAt)) { # 40 дней прошло
        $archiveNodes[] = $nameNode;
        continue;
      }

      $value = $nameNode->getElementsByTagName('value')->item(0)->textContent;
      $result[] = ["value" => $value, "created_at" => $createdAt];

    }

    # удаление не актуальных имен, перенос их в архив

    $archiveNames = [];
    if ($archiveNodes) { # нашли имена, 40 дней у которых прошли

      foreach ($archiveNodes as $nameNode) {
        $archiveNames[] = $nameNode->getElementsByTagName('value')->item(0)->textContent;
        $nameNode->parentNode->removeChild($nameNode); ### удаляем имена из XML-файла
      }

      # создаем архивный файл с удаленными именами
      $archiveFileFullPath = self::makeArchiveFileFullPath($type, time());
      self::createNamesXML($archiveFileFullPath, true, $archiveNames);

    }

    #

    return $result;
  }


  /**
   * @param  integer  $createdAt
   *
   * @return boolean
   */
  private static function isExpired(int $createdAt): bool
  {
    return (time() > self::getEndDate($createdAt));
  }


  /**
   * @param  string  $type
   * @param  string  $name
   *
   * @return string
   */
  private static function makeArchiveFileFullPath(string $type, string $name)
  {
    $archiveFolderFullPath = self::makeArchiveFullPath($type); # формируем путь до директории
    return ($archiveFolderFullPath . $name . ".xml"); # формируем полный путь до файла
  }


}