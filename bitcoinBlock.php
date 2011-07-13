<?php
/*
 * Bitcoin Blockhashdecoder - based on stuff I've found on forum.bitcoin.org
 *
 * redhatzero
 *
 */

namespace x8s\BtcBundle\Library;

class bitcoinBlock {

  protected $raw;

  protected $nonce, $bits, $version, $timestamp, $prevBlock, $merkleRoot;

  public function __construct($block)
  {
    $this->raw = substr($block,0,160);

    $this->nonce = hexdec(substr($block, 152,8));
    $this->bits  = hexdec(substr($block, 144,8));
    $this->version  = hexdec(substr($block,   0,8));
    $this->timestamp  = hexdec(substr($block, 136,8));

    $this->prevBlock  = $this->switch_endianess(substr($block, 8,64));
    $this->merkleRoot  = $this->switch_endianess(substr($block, 72,64));
  }

  public function getNonce()  { return $this->nonce; }
  public function getBits()   { return $this->bits; }


  public function getHash()
  {
    $input = $this->hexToStr($this->getBlockHeader());

    $sha = hash_init('sha256');
    hash_update($sha,$input);
    $first = hash_final($sha);

    $sha = hash_init('sha256');
    hash_update($sha,$this->hexToStr($first));
    $second = hash_final($sha);

    return $this->byteSwap($second);
  }
  protected function switch_endianess($input)
  {
    $parts = str_split($input,8);
    return implode('',array_reverse($parts));
  }

  protected function byteSwap($input)
  {
    return implode('',array_reverse(str_split($input,2)));
  }

  protected function hexToStr($hex)
  {
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2)
    {
      $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
  }

  public function getBlockHeader()
  {
    $version = sprintf('%08x',$this->version);
    $bits = sprintf('%08x',$this->bits);
    $nonce = sprintf('%08x',$this->nonce);
    $timestamp = sprintf('%08x',$this->timestamp);

    $version   = $this->byteSwap($version);
    $prevBlock = $this->byteSwap($this->prevBlock);
    $merkleRoot = $this->byteSwap($this->merkleRoot);

    $bits   = $this->byteSwap($bits);
    $nonce   = $this->byteSwap($nonce);
    $timestamp   = $this->byteSwap($timestamp);

    return implode('', array(
    $version,
    $prevBlock,
    $merkleRoot,
    $timestamp,
    $bits,
    $nonce,
    ));
  }

  public function debug()
  {

    echo $this->prevBlock."\n";
    echo $this->merkleRoot."\n";
    echo $this->timestamp."\n";
    echo $this->bits."\n";
    echo $this->nonce."\n";

  }





}

