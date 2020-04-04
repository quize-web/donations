<?php

namespace app\Core;
use XMLWriter;


class XML
{


  /**
   * @var null|XMLWriter $writer
   */
  private $writer = null;


  /**
   * @param  string    $fileFullPath
   * @param  callable  $callback
   *
   * @return void
   */
//  function write(string $fileFullPath, callable $callback): void
  function __construct(string $fileFullPath, callable $callback)
  {
    $this->writer = new XMLWriter();
    $this->writer ->openURI("file://" . $fileFullPath);
    $this->writer ->startDocument('1.0', 'UTF-8');
    $this->writer ->setIndent(4);

    $this->writer->startElement('data');
    if (is_callable($callback)) $callback($this->writer);
    $this->writer->endElement();

    $this->writer ->endDocument();
    $this->writer ->flush();
  }


}