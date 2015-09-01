<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\RepositoryInterface.php

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

/** Doctrine\ORM generic repository
 *
 * <P>This abstract class provides a skeleton Doctrine ORM entity repository
 * which provisions single or multiple primary key(s) handling for data
 * retrieval.</P>
 *
 * @package    DataJukeboxBundle
 */
interface RepositoryInterface
{

  /*
   * SETTERS
   */

  /** Sets the associated data properties (by reference)
   * @param \DataJukeboxBundle\DataJukebox\PropertiesInterface
   */
  public function setProperties(PropertiesInterface &$oProperties);

  /*
   * GETTERS
   */
  
  /** Returns the associated data properties
   * @return PropertiesInterface
   */
  public function getProperties();

  /** Returns the data list (queried from the database)
   * @param BrowserInterface $oBrowser Data browser
   * @return Result Data result (data list)
   */
  public function getDataList($oBrowser=null);

  /** Returns the data detail (queried from the database)
   * @param array $aPK_values Primary key(s)
   * @param BrowserInterface $oBrowser Data browser
   * @return Result Data result (data detail)
   */
  public function getDataDetail($aPK_values, $oBrowser=null);

  /** Returns the data object (queried from the database)
   * @param array $aPK_values Primary key(s)
   * @return Result Data result (data detail)
   */
  public function getDataObject($aPK_values);

}
