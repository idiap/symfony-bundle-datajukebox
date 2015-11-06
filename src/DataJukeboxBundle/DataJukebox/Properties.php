<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Properties.php

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

use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/** Generic data properties implementation
 *
 * <P>This abstract class provisions the requirements to implement the data
 * properties interface along Doctrine ORM class metadata and (optional) Symfony
 * translator service.</P>
 *
 * @package    DataJukeboxBundle
 */
abstract class Properties
  implements PropertiesInterface
{

  /*
   * CONSTANTS
   ********************************************************************************/

  /** Public (default) authorization level
   * @var integer
   */
  const AUTH_PUBLIC = 0;

  /** Authenticated user authorization level
   * @var integer
   */
  const AUTH_AUTHENTICATED = 1;

  /** System (internal operations) authorization level
   * @var integer
   */
  const AUTH_SYSTEM = PHP_INT_MAX;


  /*
   * PROPERTIES
   ********************************************************************************/

  /** Properties name
   * @var string
   */
  private $sName;

  /** Doctrine ORM entity manager
   * NOTE: this is required for generating SQL that matches the actual RDMS used
   * @var \Doctrine\ORM\EntityManager
   */
  private $oEntityManager;

  /** Entity name
   * @var string
   */
  private $sEntityName;

  /** Doctrine ORM class metadata
   * @var \Doctrine\ORM\Mapping\ClassMetadata
   */
  private $oClassMetadata;

  /** Authorization level
   * @var integer
   */
  private $iAuthorization;

  /** Action (list, detail, insert, update, ...)
   * @var string
   */
  private $sAction;

  /** Output format (html, xml, json, csv, ...)
   * @var string
   */
  private $sFormat;

  /** Symfony translator
   * @var \Symfony\Component\Translation\TranslatorInterface
   */
  private $oTranslator;

  /** Selected data route and slugs map
   * @var array
   */
  private $aRouteSelect;

  /** Fields hyperlinks
   * @var array
   */
  private $aFieldsLink;

  /** Footer hyperlinks
   * @var array
   */
  private $aFooterLinks;


  /*
   * CONSTRUCTORS
   ********************************************************************************/

  /** Constructor
   * @param EntityManager $oEntityManager Doctrine ORM entity manager
   * @param string $sEntityName Doctrine ORM entity name ('<Bundle>:<Entity>' notation)
   */
  public function __construct($oEntityManager=null, $sEntityName=null)
  {
    if (!is_null($oEntityManager)) {
      if (!$oEntityManager instanceof EntityManager )
        throw new \RuntimeException('Entity manager object must inherit from \Doctrine\ORM\EntityManager');
      if (empty($sEntityName))
        throw new \RuntimeException('Entity name may not be empty');
    }
    $this->sName = preg_replace('/(^.*\\\\|Properties$)/i', '', get_class($this));
    $this->oEntityManager = $oEntityManager;
    $this->sEntityName = !is_null($this->oEntityManager) ? (string)$sEntityName : null;
    $this->oClassMetadata = !is_null($this->oEntityManager) ? $this->oEntityManager->getClassMetadata($sEntityName) : null;
    $this->iAuthorization = self::AUTH_PUBLIC;
    $this->sAction = 'default';
    $this->sFormat = 'html';
    $this->oTranslator = null;
    $this->aRouteSelect = null;
    $this->aFieldsLink = array();
    $this->aFooterLinks = array();
  }


  /*
   * METHODS
   ********************************************************************************/

  /*
   * SETTERS
   */

  /** Sets the data name
   * @param string $sName Data name
   */
  public function setName($sName)
  {
    $this->sName = (string)$sName;
    return $this;
  }

  /** Sets the authorization
   * @param integer $iAuthorization Authorization level
   * @see getAuthorization()
   */
  public function setAuthorization($iAuthorization)
  {
    $this->iAuthorization = (integer)$iAuthorization;
    return $this;
  }

  /** Sets the action
   * @param string $sAction Action (list, detail, insert, update, ...)
   */
  public function setAction($sAction)
  {
    $this->sAction = (string)$sAction;
    return $this;
  }

  /** Sets the output format
   * @param string $sFormat Output format (html, xml, json, csv, ...)
   */
  public function setFormat($sFormat)
  {
    $this->sFormat = (string)$sFormat;
    return $this;
  }

  /** Sets the translator
   * @param TranslatorInterface $oTranslator Symfony translator
   */
  public function setTranslator(TranslatorInterface $oTranslator)
  {
    $this->oTranslator = $oTranslator;
    return $this;
  }

  /** Set the selected data route
   * @param array $aRouteSelect Selected data route (name) and slugs map
   * @return this
   */
  public function setRouteSelect(array $aRouteSelect)
  {
    $this->aRouteSelect = $aRouteSelect;
    return $this;
  }

  /** Set the fields hyperlinks
   * @param array $aFieldsLink Array associating fields (name) and their link options
   * @return this
   */
  public function setFieldsLink(array $aFieldsLink)
  {
    $this->aFieldsLink = $aFieldsLink;
    return $this;
  }

  /** Set the footer hyperlinks
   * @param array $aFooterLinks Array associating footer (name) and their link options
   * @return this
   */
  public function setFooterLinks(array $aFooterLinks)
  {
    $this->aFooterLinks = $aFooterLinks;
    return $this;
  }

  /*
   * GETTERS
   */

  /** Returns the data name
   * @return string Data name
   */
  public function getName()
  {
    return $this->sName;
  }

  /** Returns whether the current action is authorized (at the current authorization level)
   *
   * <P>This method MUST be overridden since by default, it returns <SAMP>false</SAMP> for
   * any authentication level lower than <SAMP>AUTH_SYSTEM</SAMP>.</P>
   *
   * @return boolean Authorization status
   */
  public function isAuthorized()
  {
    return $this->iAuthorization >= self::AUTH_SYSTEM;
  }

  /** Returns the authorization level
   *
   * <P>This method returns the authorization level (<SAMP>AUTH_PUBLIC</SAMP> by default).
   * The authorization level numeric (integer) value MUST increase as a more privileges
   * are granted (allowing for easy/fast numeric comparison of authorization levels).</P>
   *
   * @return integer Authorization level
   */
  public function getAuthorization()
  {
    return $this->iAuthorization;
  }

  /** Returns the action
   * @return string Action (list, detail, insert, update, ...)
   */
  public function getAction()
  {
    return $this->sAction;
  }

  /** Returns the output format
   * @return string Output format (html, xml, json, csv, ...)
   */
  public function getFormat()
  {
    return $this->sFormat;
  }

  /** Returns meta-data (label, tooltip, ...) for fields and resources
   * @param string $sMetaType Meta data type (label, tooltip, ...)
   * @param string $sTranslatorDomain Translation domain
   * @param boolean $bDefaultToName Meta-data defaults to field name (useful for labels)
   * @return array|array Array associating fields/resources (name) and their corresponding meta-data
   */
  protected function getMeta($sMetaType, $sTranslatorDomain=null, $bDefaultToName=false)
  {
    // Initialize meta-data for all possible fields
    $asFields = array_merge(
      !is_null($this->oClassMetadata) ? $this->oClassMetadata->getFieldNames() : $this->getFields(),
      array_keys($this->getFooterLinks()),
      array_keys($this->getFieldsForm())
    );
    $asFields = array_combine(
      $asFields,
      $bDefaultToName ? $asFields : array_fill(0, count($asFields), null)
    );
    // ... add internal resources
    $asFields_internal = array(
      '_title_list' => 'List',
      '_title_detail' => 'Detail',
      '_title_form' => 'Form',
      '_view_list' => 'List',
      '_view_detail' => 'Detail',
      '_action_insert' => 'Add',
      '_action_update' => 'Edit',
      '_action_delete' => 'Delete',
      '_action_delete_confirm' => 'Delete ?',
      '_action_select' => 'Select',
      '_action_select_confirm' => 'Select ?',
      '_action_export' => 'Export',
      '_action_form_submit' => 'Submit',
      '_action_form_reset' => 'Reset',
      '_action_form_cancel' => 'Cancel',
      '_display' => 'Fields',
      '_display_submit' => 'Submit',
      '_display_reset' => 'Reset',
      '_display_order' => 'Sort',
      '_search' => 'Search',
      '_search_submit' => 'Submit',
      '_search_reset' => 'Reset',
      '_range_begin' => 'First',
      '_range_previous' => 'Previous',
      '_range_next' => 'Next',
      '_range_end' => 'Last',
      '_range_limit' => 'Rows',
      '_data_empty' => 'No Data',
      '_data_required' => 'Required',
    );
    if ($sTranslatorDomain!='_actions' or $sMetaType!='label') {
      array_walk(
        $asFields_internal,
        function(&$v, $k) { $v=null; }
      );
    }
    $asFields = array_merge($asFields_internal, $asFields);

    // Translation
    if (!is_null($this->oTranslator)) {
      array_walk(
        $asFields,
        function(&$v, $k, $t) {
          $s = $t[0]->trans(sprintf('%s%s', $t[1], $k), array(), $t[2]);
          if (substr($s, 0, strlen($t[1])) != $t[1]) $v=$s;
        },
        array($this->oTranslator, sprintf('%s.', $sMetaType), $sTranslatorDomain)
      );
    }

    // Strip off undefined meta-data
    return array_filter(
      $asFields,
      function($v){ return !empty($v); }
    );
  }


  /*
   * METHODS: PropertiesInterface
   ********************************************************************************/

  public function getUID()
  {
    return sprintf('%s_%s', $this->sName, $this->sAction);
  }

  public function getEntityManager()
  {
    return $this->oEntityManager;
  }

  public function getEntityName()
  {
    return $this->sEntityName;
  }

  public function getClassMetadata()
  {
    return $this->oClassMetadata;
  }

  /** Returns the list of routes for common CRUD operations
   *
   * <P>The implementation of this method may provide the routes for the following views/actions:</P>
   * <LI>
   * <UL><B>list</B>: data list view</UL>
   * <UL><B>detail</B>: data detail view</UL>
   * <UL><B>insert</B>: data insert form</UL>
   * <UL><B>update</B>: data update form</UL>
   * <UL><B>delete</B>: data delete handler</UL>
   * <UL><B>select_delete</B>: selected data delete handler</UL>
   * </LI>
   *
   * @return array|string Array associating views/actions and their corresponding route (name)
   */
  public function getRoutes() {
    return array();
  }

  public function getRouteSelect()
  {
    return $this->aRouteSelect;
  }

  public function getLabels()
  {
    return $this->getMeta('label', '_actions', true);
  }

  public function getTooltips()
  {
    return $this->getMeta('tooltip', '_actions');
  }

  public function getFields()
  {
    return !is_null($this->oClassMetadata) ? $this->oClassMetadata->getFieldNames() : array();
  }

  public function getFieldsDefault()
  {
    return $this->getFields();
  }

  public function getFieldsHidden()
  {
    switch ($this->sAction) {
    case 'insert':
    case 'update':
      return array_diff(
        $this->getFieldsReadOnly(),
        $this->getFieldsRequired()
      );
    }
    return array();
  }

  public function getFieldsRequired()
  {
    return array();
  }

  public function getFieldsReadonly()
  {
    return !is_null($this->oClassMetadata) ? $this->oClassMetadata->getIdentifierFieldNames() : array();
  }

  public function getFieldsDefaultValue()
  {
    return array();
  }

  public function getFieldsLink()
  {
    return $this->aFieldsLink;
  }

  public function getFieldsOrder()
  {
    return array();
  }

  public function getFieldsFilter()
  {
    return array();
  }

  public function getFieldsSearch()
  {
    return array();
  }

  public function getFieldsForm()
  {
    return array();
  }

  /** Returns the template resource (name) to be used for view rendering
   *
   * <P>This default implementation supports the following views/actions (and
   * corresponding templates):</P>
   * <LI>
   * <UL><B>list</B>: corresponding to <SAMP>DataJukeboxBundle:Default:list.html.twig</SAMP></UL>
   * <UL><B>select</B>: corresponding to <SAMP>DataJukeboxBundle:Default:list.html.twig</SAMP></UL>
   * <UL><B>detail</B>: corresponding to <SAMP>DataJukeboxBundle:Default:detail.html.twig</SAMP></UL>
   * <UL><B>insert</B>: corresponding to <SAMP>DataJukeboxBundle:Default:form.html.twig</SAMP></UL>
   * <UL><B>update</B>: corresponding to <SAMP>DataJukeboxBundle:Default:form.html.twig</SAMP></UL>
   * </LI>
   *
   * @return string Template resource (name)
   * @throws \Exception
   */
  public function getTemplate()
  {
    switch ($this->sFormat) {
    case 'html':
      switch ($this->sAction) {
      case 'list':
      case 'select':
        return 'DataJukeboxBundle:Default:list.html.twig';
      case 'detail':
        return 'DataJukeboxBundle:Default:detail.html.twig';
      case 'insert':
      case 'update':
        return 'DataJukeboxBundle:Default:form.html.twig';
      }

    case 'xml':
      switch ($this->sAction) {
      case 'list':
      case 'detail':
      case 'export':
        return 'DataJukeboxBundle:Default:xml.txt.twig';
      }

    case 'json':
      switch ($this->sAction) {
      case 'list':
      case 'detail':
      case 'export':
        return 'DataJukeboxBundle:Default:json.txt.twig';
      }

    case 'csv':
      switch ($this->sAction) {
      case 'list':
      case 'detail':
      case 'export':
        return 'DataJukeboxBundle:Default:csv.txt.twig';
      }
    }
    throw new \Exception(sprintf('Invalid/unsupported action/format (%s/%s)', $this->sAction, $this->sFormat));
  }

  public function getFooterLinks()
  {
    return $this->aFooterLinks;
  }

  /** Returns the ad-hoc template data
   *
   * <P>The following template data are returned:</P>
   * <LI>
   * <UL><B>array['uid']</B>: corresponding to <SAMP>$this->getUID()</SAMP></UL>
   * <UL><B>array['name']</B>: corresponding to <SAMP>$this->getName()</SAMP></UL>
   * <UL><B>array['action']</B>: corresponding to <SAMP>$this->getAction()</SAMP></UL>
   * <UL><B>array['routes']</B>: corresponding to <SAMP>$this->getRoutes()</SAMP></UL>
   * <UL><B>array['labels']</B>: corresponding to <SAMP>$this->getLabels()</SAMP></UL>
   * <UL><B>array['tooltips']</B>: corresponding to <SAMP>$this->getTooltips()</SAMP></UL>
   * <UL><B>array['fields']</B>: corresponding to <SAMP>$this->getFields()</SAMP></UL>
   * <UL><B>array['fields_default']</B>: corresponding to <SAMP>$this->getFieldsDefault()</SAMP></UL>
   * <UL><B>array['fields_hidden']</B>: corresponding to <SAMP>$this->getFieldsHidden()</SAMP></UL>
   * <UL><B>array['fields_required']</B>: corresponding to <SAMP>$this->getFieldsRequired()</SAMP></UL>
   * <UL><B>array['fields_readonly']</B>: corresponding to <SAMP>$this->getFieldsReadonly()</SAMP></UL>
   * <UL><B>array['fields_link']</B>: corresponding to <SAMP>$this->getFieldsLink()</SAMP></UL>
   * <UL><B>array['fields_order']</B>: corresponding to <SAMP>$this->getFieldsOrder()</SAMP></UL>
   * <UL><B>array['fields_search']</B>: corresponding to <SAMP>$this->getFieldsSearch()</SAMP></UL>
   * <UL><B>array['fields_filter']</B>: corresponding to <SAMP>$this->getFieldsFilter()</SAMP></UL>
   * <UL><B>array['footer_links']</B>: corresponding to <SAMP>$this->getFooterLinks()</SAMP></UL>
   * </LI>
   *
   * @return array Template data
   */
  public function getTemplateData()
  {
    // Populate default routes
    $aaRoutes = $this->getRoutes();
    if ($aRouteSelect = $this->getRouteSelect()) $aaRoutes = array_merge($aaRoutes, array('select_route' => $aRouteSelect ));

    // Valid fields
    if (!is_null($this->oClassMetadata)) {
      $aFields_valid = $this->oClassMetadata->getFieldNames();
      $aFields = array_intersect($this->getFields(), $aFields_valid);
    } else {
      $aFields = $this->getFields();
    }

    // Hide special fields
    $aFields_hidden = array_merge(array('_PK'), $this->getFieldsHidden());
    if ($this instanceof FormatInterface) {
      $aFields_hidden = array_merge(
        $aFields_hidden,
        array_map(
          function($v){ return sprintf('%s_formatted',$v); },
          $aFields
        )
      );
    }

    // Return data
    return array(
      'uid' => $this->getUID(),
      'name' => $this->getName(),
      'action' => $this->getAction(),
      'routes' => $aaRoutes,
      'labels' => $this->getLabels(),
      'tooltips' => $this->getTooltips(),
      'fields' => $aFields,
      'fields_default' => $this->getFieldsDefault(),
      'fields_hidden' => $aFields_hidden,
      'fields_required' => $this->getFieldsRequired(),
      'fields_readonly' => $this->getFieldsReadonly(),
      'fields_link' => $this->getFieldsLink(),
      'fields_order' => $this->getFieldsOrder(),
      'fields_search' => $this->getFieldsSearch(),
      'fields_filter' => $this->getFieldsFilter(),
      'footer_links' => $this->getFooterLinks(),
    );
  }

  public function getBrowser($oRequest=null, $sUID=null)
  {
    return new Browser($this, $oRequest, $sUID);
  }

}
