<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\FormatInterface.php

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
 * @package    DataJukeboxBundle
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle\DataJukebox;

/** Data format interface
 *
 * <P>This interface wraps the methods required to format data in any way
 * the user deems fit.</P>
 *
 * @package    DataJukeboxBundle
 */
interface FormatInterface
{

  /*
   * METHODS
   ********************************************************************************/

  /** Format data
   *
   * <P>This method shall be fed to PHP <SAMP>array_walk</SAMP> and add to the given
   * data row - associating fields name and their value - additional '<fieldname>|format'
   * items - associating the formatted value - for each field that needs custom formatting.<P>
   *
   * <P><B>WARNING: proper escaping MUST be performed by this function (formatted value shall
   * be displayed as raw data - {{ ...|raw }} - in generated output).</B><P>
   *
   * @param array Data row (fields array)
   * @param integer Data row index
   */
  public static function formatFields(array &$aRow, $iIndex);

}
