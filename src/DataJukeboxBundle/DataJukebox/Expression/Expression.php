<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Expression\Expression.php

/** Data Jukebox Bundle
 *
 * <P><B>COPYRIGHT:</B></P>
 * <PRE>
 * Data Jukebox Bundle
 * Copyright (C) 2015 Idiap Research Institute <http://www.idiap.ch>
 * Author: Cedric Dufour <http://cedric.dufour.name>
 *
 * This file is part of the Data Jukebox Bundle.
 *
 * The Data Jukebox Bundle is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, Version 3.
 *
 * The Data Jukebox Bundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 * </PRE>
 *
 * @package    ExpressionLanguage
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle\DataJukebox\Expression;

/** Expression
 *
 * <P>This class provides the mean to parse expression string and return the corresponding expression
 * nodes. It works by first tokenizing the expression string and then parsing those tokens into expression
 * nodes.</P>
 * @see Node
 *
 * @package    ExpressionLanguage
 */
class Expression
{

  /*
   * CONSTANTS
   ********************************************************************************/

  /** Token: value
   * @var integer */
  const TOKEN_VALUE = 0;

  /** Token: parameter
   * @var integer */
  const TOKEN_PARAMETER = 1;

  /** Token: operator
   * @var integer */
  const TOKEN_OPERATOR = 2;


  /*
   * METHODS
   ********************************************************************************/

  /** Returns a token for the given value/type at the given position
   *
   * <P>This method returns a token as an array associating:</P>
   * <UL>
   * <LI><B>type</B>: the token type (<SAMP>TOKEN_VALUE</SAMP>, <SAMP>TOKEN_PARAMETER</SAMP>, <SAMP>TOKEN_OPERATOR</SAMP>)</LI>
   * <LI><B>value</B>: the token value (actual value, parameter name or operator symbol/keyword)</LI>
   * <LI><B>position</B>: the token position within the entire expression</LI>
   * </UL>
   *
   * @param integer $iType Token type
   * @param mixed $mValue Token value
   * @param integer $iPosition Token position
   * @return array Token array
   */
  public static function makeToken($iType, $mValue, $iPosition=0)
  {
    return array(
      'type' => $iType,
      'value' => $mValue,
      'position' => sprintf('c:%d', $iPosition+1),
    );
  }

  /** Returns a token for the given string fragment
   *
   * <P>This method returns the token corresponding to the given fragment, either:</P>
   * <UL>
   * <LI>the leyword-matching operator (<SAMP>TOKEN_OPERATOR</SAMP>)</LI>
   * <LI>or the actual (PHP typed) token value (<SAMP>TOKEN_VALUE</SAMP>)</LI>
   * </UL>
   *
   * @param array|array $aaSymbols Operator symbols dictionary
   * @param string $sFragment String fragment to tokenize
   * @param integer $iPosition String fragment/token position
   * @return array Token array
   */
  public static function tokenizeFragment(array $aaSymbols, $sFragment, $iPosition=0)
  {
    $sFragment = (string)$sFragment;
    if (in_array($sFragment, $aaSymbols['keywords'])) {
      return self::makeToken(self::TOKEN_OPERATOR, $sFragment, $iPosition);
    }
    if (is_numeric($sFragment)) {
      if (ctype_digit($sFragment)) return self::makeToken(self::TOKEN_VALUE, (integer)$sFragment, $iPosition);
      if (ctype_xdigit($sFragment)) return self::makeToken(self::TOKEN_VALUE, (integer)$sFragment, $iPosition);
      return self::makeToken(self::TOKEN_VALUE, (float)$sFragment, $iPosition);
    }
    return self::makeToken(self::TOKEN_VALUE, $sFragment, $iPosition);
  }

  /** Returns the tokens corresponding to the given (parameterized) string
   *
   * <P>This method returns the tokens corresponding to the given string, potientially
   * containing <SAMP>${...}</SAMP> parameters.</P>
   *
   * @param string $sString String to tokenize
   * @param integer $iPosition String/token position
   * @return array|array Tokens array
   */
  public static function tokenizeStringParameters($sString, $iPosition=0)
  {
    $aaTokens = array();

    $s = $sString;
    $o = $iPosition;
    $j = 0;
    while(mb_strlen($s)) {
      $u = preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY); // used for faster mb_char extraction
      $l = count($u);
      $f = ''; // fragment

      $e = false;
      $i; for($i=0; $i<$l; $i++) {
        // character
        $c = $u[$i];

        // escape
        if( $c == '\\' ) {
          if ($e) {
            $f .= $c;
            $e = false;
          } else {
            $e = true;
          }
          continue;
        }

        // parameter: ${...}
        if ($c == '$') {
          if (preg_match('/^\${(\w+)}(.*)$/u', mb_substr($s, $i), $a)) {
            if ($i) {
              $aaTokens[] = self::makeToken(self::TOKEN_VALUE, $f, $o);
              $f = '';
            }
            $aaTokens[] = self::makeToken(self::TOKEN_PARAMETER, $a[1], $o+$i);
            $j = mb_strlen($a[0]) - mb_strlen($a[2]) + $i;
            break;
          }
        }

        // default
        $f .= $c;

      } // for

      if (mb_strlen($f)) {
        $aaTokens[] = self::makeToken(self::TOKEN_VALUE, $f, $o);
      }

      if ($i>=$l) break;

      if ($j) {
        $s = mb_substr($s, $j);
        $o += $j;
        $j = 0;
      }

    } // while

    return $aaTokens;
  }

  /** Returns the tokens corresponding to the given expression
   *
   * <P>This method returns the tokens corresponding to the given expression (string). Operators
   * shall be identified based to the given symbols dictionary, as an array associating:</P>
   * <UL>
   * <LI><B>keywords</B>: array of alphanumeric keywords (ex: <SAMP>and</SAMP>, <SAMP>or</SAMP>, etc.)</LI>
   * <LI><B>singletons</B>: array of single-character symbols (ex: <SAMP>+</SAMP>, <SAMP>-</SAMP>, etc.)</LI>
   * <LI><B>doublets</B>: array of two-character symbols (ex: <SAMP>==</SAMP>, <SAMP>!=</SAMP>, etc.)</LI>
   * <LI><B>triplets</B>: array of three-character symbols (ex: <SAMP>===</SAMP>, <SAMP>!==</SAMP>, etc.)</LI>
   * </UL>
   *
   * @param array|array $aaSymbols Operator symbols dictionary
   * @param string $sExpression Expression (string) to tokenize
   * @return array|array Tokens array
   */
  public static function tokenize($aaSymbols, $sExpression)
  {
    $aaTokens = array();

    $s = (string)$sExpression;
    $o = 0;
    $j = 0;
    while(mb_strlen($s)) {
      $u = preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY); // used for faster mb_char extraction
      $l = count($u);
      $f = ''; // fragment

      $e = false;
      $i; for($i=0; $i<$l; $i++) {
        // character
        $c = mb_strtolower($u[$i]); // singleton

        // blank
        if (trim($c) == '') {
          $j = $i+1;
          break;
        }

        // escape
        if ($c == '\\' and in_array($c, $aaSymbols['singletons'])) {
          if ($e) {
            $f .= $c;
            $e = false;
          } else {
            $e = true;
          }
          continue;
        }

        // parameter: ${...}
        if ($c == '$' and in_array($c, $aaSymbols['singletons'])) {
          if (preg_match('/^\${(\w+)}(.*)$/u', mb_substr($s, $i), $a)) {
            if ($i) {
              $aaTokens[] = self::tokenizeFragment($aaSymbols, $f, $o);
              $f = '';
            }
            $aaTokens[] = self::makeToken(self::TOKEN_PARAMETER, $a[1], $o+$i);
            $j = mb_strlen($a[0]) - mb_strlen($a[2]);
            break;
          } else {
            $f .= $c;
            continue;
          }
        }

        // single-quote: '...'
        if ($c == '\'' and in_array($c, $aaSymbols['singletons'])) {
          if (preg_match('/^\'([^\']*(\'\'[^\']*)*[^\']*)\'([^\'].*)*$/u', mb_substr($s, $i), $a)) {
            if ($i) {
              $aaTokens[] = self::tokenizeFragment($aaSymbols, $f, $o);
              $f = '';
            }
            $aaTokens[] = self::makeToken(self::TOKEN_OPERATOR, '\'', $o+$i);
            $aaTokens[] = self::makeToken(self::TOKEN_VALUE, $a[1], $o+$i+1);
            $aaTokens[] = self::makeToken(self::TOKEN_OPERATOR, '\'', $o+$i+1+mb_strlen($a[1]));
            $j = $i+2+mb_strlen($a[1]);
            break;
          } else {
            $f .= $c;
            continue;
          }
        }

        // double-quote: "..."
        if ($c == '"' and in_array($c, $aaSymbols['singletons'])) {
          if (preg_match('/^"([^"]*(""[^"]*)*[^"]*)"([^"].*)*$/u', mb_substr($s, $i), $a)) {
            if ($i) {
              $aaTokens[] = self::tokenizeFragment($aaSymbols, $f, $o);
              $f = '';
            }
            $aaTokens[] = self::makeToken(self::TOKEN_OPERATOR, '"', $o+$i);
            $aaTokens[] = self::makeToken(self::TOKEN_VALUE, $a[1], $o+$i+1);
            $aaTokens[] = self::makeToken(self::TOKEN_OPERATOR, '"', $o+$i+1+mb_strlen($a[1]));
            $j = $i+2+mb_strlen($a[1]);
            break;
          } else {
            $f .= $c;
            continue;
          }
        }

        // others
        $cc = $i+1<$l ? $c.mb_strtolower($u[$i+1]) : null; // doublet
        $ccc = $i+2<$l ? $cc.mb_strtolower($u[$i+2]) : null; // triplet
        // ... triplets
        if (in_array($ccc, $aaSymbols['triplets'])) {
          if ($i) {
            $aaTokens[] = self::tokenizeFragment($aaSymbols, $f, $o);
            $f = '';
          }
          $aaTokens[] = self::makeToken(self::TOKEN_OPERATOR, $ccc, $o+$i);
          $j = $i+3;
          break;
        }
        // ... doublets
        if (in_array($cc, $aaSymbols['doublets'])) {
          if ($i) {
            $aaTokens[] = self::tokenizeFragment($aaSymbols, $f, $o);
            $f = '';
          }
          $aaTokens[] = self::makeToken(self::TOKEN_OPERATOR, $cc, $o+$i);
          $j = $i+2;
          break;
        }
        // ... singletons
        if (in_array($c, $aaSymbols['singletons'])) {
          if ($i) {
            $aaTokens[] = self::tokenizeFragment($aaSymbols, $f, $o);
            $f = '';
          }
          $aaTokens[] = self::makeToken(self::TOKEN_OPERATOR, $c, $o+$i);
          $j = $i+1;
          break;
        }

        // default
        $f .= $c;

      } // for

      if (mb_strlen($f)) {
        $aaTokens[] = self::tokenizeFragment($aaSymbols, $f, $o);
      }

      if ($i>=$l) break;

      if ($j) {
        $s = mb_substr($s, $j);
        $o += $j;
        $j = 0;
      }

    } // while

    return $aaTokens;
  }


  /** Parses the given expression (tokens) and returns the corresponding expression nodes
   *
   * @param mixed $mExpression Expression string or tokens
   * @param ParserInterface $oParser (Expression) tokens parser
   * @return Node Expression (top-level) node
   */
  public static function parse($mExpression, ParserInterface $oParser)
  {
    if (!is_array($mExpression)) $mExpression = self::tokenize($oParser->symbols(), $mExpression);
    return $oParser->parse($mExpression);
  }

}
