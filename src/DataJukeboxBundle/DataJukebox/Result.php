<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Result.php

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

/** Data result implementation
 *
 * @package    DataJukeboxBundle
 */
class Result
  implements ResultInterface
{

  /*
   * PROPERTIES
   ********************************************************************************/

  /** Data properties
   * @var PropertiesInterface
   */
  private $oProperties;

  /** Data browser
   * @var BrowserInterface
   */
  private $oBrowser;

  /** Data (actual)
   * @var array
   */
  private $aData;


  /*
   * CONSTRUCTORS
   ********************************************************************************/

  public function __construct(
    PropertiesInterface $oProperties,
    BrowserInterface $oBrowser,
    array $aData
  )
  {
    $this->oProperties = $oProperties;
    $this->oBrowser = $oBrowser;
    $this->aData = $aData;
  }


  /*
   * METHODS: ResultInterface
   ********************************************************************************/

  public function getProperties()
  {
    return $this->oProperties;
  }

  public function getBrowser()
  {
    return $this->oBrowser;
  }

  public function getData()
  {
    return $this->aData;
  }

  /** Returns the ad-hoc template data
   *
   * <P>The following template data are returned:</P>
   * <LI>
   * <UL><B>array['properties']</B>: corresponding to <SAMP>$this->getProperties()->getTemplateData()</SAMP></UL>
   * <UL><B>array['browser']</B>: corresponding to <SAMP>$this->getBrowser()->getTemplateData()</SAMP></UL>
   * <UL><B>array['rows']</B>: corresponding to actual data</UL>
   * </LI>
   *
   * @return array Template data
   */
  public function getTemplateData()
  {
    return array(
      'properties' => $this->oProperties->getTemplateData(),
      'browser' => $this->oBrowser->getTemplateData(),
      'rows' => $this->aData,
    );
  }

  public function getPrimaryKeySlugs()
  {
    $aaPrimaryKeySlugs = array();
    foreach ($this->aData as $aRow) $aaPrimaryKeySlugs[] = array('_pk' => $aRow['_PK']);
    return $aaPrimaryKeySlugs;
  }


  /*
   * METHODS
   ********************************************************************************/

  public function &useProperties()
  {
    return $this->oProperties;
  }

  public function &useBrowser()
  {
    return $this->oBrowser;
  }

  public function &useData()
  {
    return $this->aData;
  }

}
