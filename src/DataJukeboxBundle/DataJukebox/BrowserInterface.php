<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\BrowserInterface.php

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

/** Data browser interface
 *
 * <P>This interface wraps the methods required to abstract data browsing
 * (displayed fields, ordering, searching, filtering, etc.).</P>
 *
 * @package    DataJukeboxBundle
 */
interface BrowserInterface
{

  /*
   * METHODS
   ********************************************************************************/

  /** Returns the browser UID
   *
   * <P>This method shall return an ASCII unique identifier for this browser.</P>
   *
   * @return string Browser UID
   */
  public function getUID();

  /** Returns queried (and potentially displayed) columns
   *
   * <P>This method shall return the list of fields (names) to query/display.</P>
   *
   * @return array|string Array of fields (names) to query/display
   */
  public function getFields();

  /** Returns the fields ordering
   *
   * <P>This method shall return a numerically indexed (ordered) array, with each
   * value being an array with a field name ([0]) and its ordering direction ([1]={A,D}).</P>
   *
   * @return array|array|string Fields ordering array
   */
  public function getFieldsOrder();

  /** Returns the fields-specific filter criteria
   *
   * <P>This method shall return the filter expression to be used to perform fields-specific filtering.</P>
   *
   * @return array|string Array associating each field (name) and its corresponding filter criteria
   */
  public function getFieldsFilter();

  /** Returns the (global) fields searching criteria
   *
   * <P>This method shall return the filter expression to be used to perform a global search.</P>
   *
   * @return string Global search (filter) criteria
   */
  public function getSearch();

  /** Returns the data range
   *
   * <P>This method shall return an array associating:</P>
   * <UL>
   * <LI><SAMP>array['count']</SAMP>: actual data count
   * <LI><SAMP>array['from']</SAMP>: actual data first tuple index (starting from 0)
   * <LI><SAMP>array['to']</SAMP>: actual data last tuple index (starting from 0)
   * <LI><SAMP>array['limit']</SAMP>: user-requested data limit (quantity of rows)
   * </UL>
   *
   * @return array|integer Data range
   */
  public function getRange();

  /** Returns the data offset (rows paging)
   *
   * @return integer Data offset
   */
  public function getOffset();

  /** Returns the data limit (quantity of rows)
   *
   * @return integer Data limit
   */
  public function getLimit();

  /** Returns the ad-hoc template data
   * @return array Template data
   */
  public function getTemplateData();

}
