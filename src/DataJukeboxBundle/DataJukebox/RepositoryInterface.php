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
   * @param PropertiesInterface
   */
  public function setProperties(PropertiesInterface &$oProperties);

  /*
   * GETTERS
   */
  
  /** Returns the associated data properties
   * @return PropertiesInterface
   */
  public function getProperties();

  /** Returns the data count (queried from the database)
   * @param BrowserInterface $oBrowser Data browser
   * @return integer Data result
   */
  public function getDataCount($oBrowser=null);

  /** Returns the data result (queried from the database)
   *
   * <P>Returns the result matching the (optional) primary key and browser.
   * If the primary key is supplied, it will ignore any browser-supplied
   * range/limit/search/filter criteria and fetch the (single) corresponding
   * item. Otherwise, all items (or browser-specified items) shall be fetched.</P>
   *
   * @param array $aPK_values Primary key(s)
   * @param BrowserInterface $oBrowser Data browser
   * @return Result Data result
   */
  public function getDataResult($aPK_values=null, $oBrowser=null);

  /** Returns the data entity (queried from the database)
   * @param array $aPK_values Primary key(s)
   * @return Entity Data entity
   */
  public function getDataEntity($aPK_values);

}
