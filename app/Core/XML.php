<?php

namespace app\Core;

use XMLWriter;
use DOMDocument;


class XML
{


  /**
   * @var null|XMLWriter $writer
   */
  private $writer = null;

  /**
   * @var null|DOMDocument $writer
   */
  private $dom = null;

  /**
   * @var null|string $fileFullPath
   */
  private $fileFullPath = null;


  function __construct(string $fileFullPath)
  {
    $this->fileFullPath = $fileFullPath;
  }


  /**
   * @param  callable  $callback
   *
   * @return void
   */
  function write(callable $callback): void
  {
    $this->writer = new XMLWriter();
    $this->writer->openURI("file://" . $this->fileFullPath);
    $this->writer->startDocument('1.0', 'UTF-8');
    $this->writer->setIndent(4);

    $this->writer->startElement('data');
    if (is_callable($callback)) $callback($this->writer);
    $this->writer->endElement();

    $this->writer->endDocument();
    $this->writer->flush();
  }


  function read()
  {
    //
  }


  function modify(callable $callback)
  {
    $this->dom = new DOMDocument();
    $this->dom->load($this->fileFullPath);

    if (is_callable($callback)) $callback($this->dom);

    $this->dom->save($this->fileFullPath);
    unset($this->dom);
  }


}