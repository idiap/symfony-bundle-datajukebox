<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Expression\ParserInterface.php

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


/** Parser interface
 *
 * @package    ExpressionLanguage
 */
interface ParserInterface
{

  /*
   * METHODS
   ********************************************************************************/

  /** Returns the operator symbols dictionary
   *
   * <P>This method shall return the operator symbols dictionary, as an array associating:</P>
   * <UL>
   * <LI><B>keywords</B>: array of alphanumeric keywords (ex: <SAMP>and</SAMP>, <SAMP>or</SAMP>, etc.)</LI>
   * <LI><B>singletons</B>: array of single-character symbols (ex: <SAMP>+</SAMP>, <SAMP>-</SAMP>, etc.)</LI>
   * <LI><B>doublets</B>: array of two-character symbols (ex: <SAMP>==</SAMP>, <SAMP>!=</SAMP>, etc.)</LI>
   * <LI><B>triplets</B>: array of three-character symbols (ex: <SAMP>===</SAMP>, <SAMP>!==</SAMP>, etc.)</LI>
   * </UL>
   *
   * @return array|array Operator symbols dictionary
   */
  public function symbols();

  /** Parses the given expression tokens and returns the corresponding expression nodes
   *
   * @param array|array $aaTokens Tokens array
   * @return Node Expression (top-level) node
   */
  public function parse(array $aaTokens);

}
