<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Browser.php

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

use Symfony\Component\HttpFoundation\Request;

/** Data browser implementation
 *
 * @package    DataJukeboxBundle
 */
class Browser
  implements BrowserInterface
{

  /*
   * PROPERTIES
   ********************************************************************************/

  /** Browser UID
   * @var string
   */
  private $sUID;

  /** Associated data properties
   * @var PropertiesInterface
   */
  private $oProperties;

  /** Displayed fields (names)
   * @var array|string
   */
  private $asFields;

  /** Order fields/direction
   * @var array|array|string
   */
  private $aasFieldsOrder;

  /** Filter criteria
   * @var array|string
   */
  private $asFieldsFilter;

  /** Search criteria
   * @var string
   */
  private $sSearch;

  /** Data range
   * @var array|integer
   */
  private $aiRange;


  /*
   * CONSTRUCTORS
   ********************************************************************************/

  /** Constructor
   * @param PropertiesInterface $oProperties Associated data properties
   * @param Request $oRequest HTTP request object
   * @param string $sUID Browser UID
   * @throws \Exception
   */
  public function __construct(
    PropertiesInterface &$oProperties,
    $oRequest=null,
    $sUID=null
  )
  {
    if (!is_null($oRequest) and !$oRequest instanceof Request) {
      throw new \Exception('Request object must be a \Symfony\Component\HttpFoundation\Request');
    }
    $this->sUID = $sUID;
    $this->oProperties = &$oProperties;

    // Parse request
    $this->setRangeActual(0, 0, 0, 25);
    if ($oRequest) {
      $this->setFields($oRequest->query->get(sprintf('%s_fd', $this->sUID)));
      $this->setFieldsOrder($oRequest->query->get(sprintf('%s_or', $this->sUID)), true);
      $this->setFieldsFilter($oRequest->query->all(), true);
      $this->setSearch($oRequest->query->get(sprintf('%s_sh', $this->sUID)));
      $this->setOffset($oRequest->query->get(sprintf('%s_of', $this->sUID)));
      $this->setLimit($oRequest->query->get(sprintf('%s_lt', $this->sUID)));
    } else {
      $this->asFields = array();
      $this->aasFieldsOrder = array();
      $this->asFieldsFilter = array();
      $this->sSearch = null;
    }
  }


  /*
   * METHODS
   ********************************************************************************/

  /** Sets the query/display fields
   * @param mixed $asFields Array (or comma-separated) fields (name) to query/display
   * @return this
   */
  public function setFields($asFields)
  {
    $this->asFields = array();
    if (!is_null($asFields)) {
      if (!is_array($asFields)) $asFields = explode(',', $asFields);
      $this->asFields = $asFields;
    }
    return $this;
  }

  /** Sets the fields ordering
   * @param mixed $asFieldsOrder Array (or comma-separated) fields (name), A(sc)/D(esc) suffixed
   * @param boolean $bParseValidate Parse and validate filter criteria
   * @return this
   */
  public function setFieldsOrder($asFieldsOrder, $bParseValidate=false)
  {
    $this->aasFieldsOrder = array();
    if (!is_null($asFieldsOrder)) {
      if (!is_array($asFieldsOrder)) $asFieldsOrder = explode(',', $asFieldsOrder);
      if ($bParseValidate) {
        $asFieldsOrder = array_filter(
          $asFieldsOrder,
          function($k) { return strlen($k) and $k[0]!='*'; }, // prevent "SYSTEM" criteria injection
          ARRAY_FILTER_USE_KEY
        );
      }
      foreach ($asFieldsOrder as $sFieldVsDirection) {
        if (preg_match('/^(\*?\w{1,})_(A|D)$/', $sFieldVsDirection, $asMatches)) {
          $sField = $asMatches[1];
          $this->aasFieldsOrder[] = array($sField, $asMatches[2]);
        }
      }
    }
    return $this;
  }

  /** Sets the fields-specific filter criteria (expression)
   * @param array $asFieldsFilter Array associating each field (name) and its corresponding filter criteria (expression)
   * @param boolean $bParseValidate Parse and validate filter criteria
   * @return this
   */
  public function setFieldsFilter(array $asFieldsFilter, $bParseValidate=false)
  {
    if ($bParseValidate) {
      $asFilter = array();
      foreach ($asFieldsFilter as $sField => $sFilter) {
        if (!is_scalar($sFilter)) continue;
        $sFilter = trim($sFilter);
        if (!strlen($sFilter)) continue;
        if (is_null($this->sUID)) {
          $asFilter[$sField] = $sFilter;
        } else {
          $l = strlen($this->sUID);
          if (substr($sField, 0, $l) == $this->sUID) {
            $asFilter[substr($sField,$l)] = $sFilter;
          }
        }
      }
      $asFilter = array_filter(
        $asFilter,
        function($k) { return strlen($k) and $k[0]!='*'; }, // prevent "SYSTEM" criteria injection
        ARRAY_FILTER_USE_KEY
      );
      $this->asFieldsFilter = $asFilter;
    } else {
      $this->asFieldsFilter = $asFieldsFilter;
    }
    return $this;
  }

  /** Sets the (global) fields searching criteria (expression)
   * @param string $sSearch Search criteria (expression)
   * @return this
   */
  public function setSearch($sSearch)
  {
    $this->sSearch = $sSearch;
    return $this;
  }

  /** Sets the data offset (rows paging)
   * @param integer $iOffset Data offset (rows paging)
   * @return this
   */
  public function setOffset($iOffset)
  {
    if (!is_null($iOffset)) $this->aiRange['from'] = (integer)$iOffset;
    return $this;
  }

  /** Sets the data limit (quantity of rows)
   * @param integer $iLimit Data limit (quantity of rows)
   * @return this
   */
  public function setLimit($iLimit)
  {
    if (!is_null($iLimit)) $this->aiRange['limit'] = (integer)$iLimit;
    return $this;
  }

  /** Sets the actual data count/range/limit
   * @param integer $iCount Actual quantity of data (rows)
   * @param integer $iFrom Actual index of the first queried/displayed data (row), starting from zero
   * @param integer $iTo Actual index of the last queried/displayed data (row), starting from zero
   * @param integer $iLimit Data limit (quantity of rows), ignored if <SAMP>null</SAMP>
   * @return this
   */
  public function setRangeActual($iCount, $iFrom, $iTo, $iLimit=null)
  {
    $this->aiRange = array(
      'count' => (integer)$iCount,
      'from' => (integer)$iFrom,
      'to' => (integer)$iTo,
      'limit' => !is_null($iLimit) ? (integer)$iLimit : $this->aiRange['limit'],
    );
    return $this;
  }

  /** Returns the POST-ed primary keys (_pk[])
   * @return array|mixed Array of POST-ed primary keys
   */
  public static function getPrimaryKeys(Request $oRequest) {
    $asPK = $oRequest->request->get('_pk', array());
    if (!is_array($asPK)) $asPK = array($asPK);
    return $asPK;
  }


  /*
   * METHODS: BrowserInterface
   ********************************************************************************/

  public function getUID()
  {
    return $this->sUID;
  }

  public function getFields()
  {
    $asFields = $this->asFields;
    if (!count($asFields)) $asFields = $this->oProperties->getFieldsDefault();
    $asFields = array_intersect(
      $this->oProperties->getFields(),
      array_diff(
        $this->oProperties->getClassMetadata()->getFieldNames(),
        $this->oProperties->getFieldsHidden()
      ),
      array_merge(
        $asFields,
        $this->oProperties->getFieldsRequired()
      )
    );
    return $asFields;
  }

  public function getFieldsOrder()
  {
    return $this->aasFieldsOrder;
  }

  public function getFieldsFilter()
  {
    return $this->asFieldsFilter;
  }

  public function getSearch()
  {
    return $this->sSearch;
  }

  public function getRange()
  {
    return $this->aiRange;
  }

  public function getOffset()
  {
    return $this->aiRange['from'];
  }

  public function getLimit()
  {
    return $this->aiRange['limit'];
  }

  /** Returns the ad-hoc template data
   *
   * <P>The following template data are returned:</P>
   * <LI>
   * <UL><B>array['uid']</B>: corresponding to <SAMP>$this->getUID()</SAMP></UL>
   * <UL><B>array['fields']</B>: corresponding to <SAMP>$this->getFields()</SAMP></UL>
   * <UL><B>array['fields_order']</B>: corresponding to <SAMP>$this->getFieldsOrder()</SAMP></UL>
   * <UL><B>array['fields_filter']</B>: corresponding to <SAMP>$this->getFieldsFilter()</SAMP></UL>
   * <UL><B>array['search']</B>: corresponding to <SAMP>$this->getSearch()</SAMP></UL>
   * <UL><B>array['range']</B>: corresponding to <SAMP>$this->getRange()</SAMP></UL>
   * </LI>
   *
   * @return array Template data
   */
  public function getTemplateData()
  {
    return array(
      'uid' => $this->getUID(),
      'fields' => $this->getFields(),
      'fields_order' => $this->getFieldsOrder(),
      'fields_filter' => $this->getFieldsFilter(),
      'search' => $this->getSearch(),
      'range' => $this->getRange(),
    );
  }

}
