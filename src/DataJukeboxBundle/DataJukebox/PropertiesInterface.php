<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\PropertiesInterface.php

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

/** Data properties interface
 *
 * <P>This interface wraps the methods required to retrieve data properties
 * useful for data processing (data-fill, persist, display, etc.).</P>
 *
 * @package    DataJukeboxBundle
 */
interface PropertiesInterface
{

  /*
   * METHODS
   ********************************************************************************/

  /*
   * GETTERS
   */

  /** Returns the properties UID
   *
   * <P>This method shall return an ASCII unique identifier for these properties.</P>
   *
   * @return string Properties UID
   */
  public function getUID();

  /** Returns the Doctrine ORM entity manager
   * @return \Doctrine\ORM\EntityManager Doctrine ORM entity manager
   */
  public function getEntityManager();

  /** Returns the Doctrine ORM entity name
   * @return string Doctrine ORM entity name
   */
  public function getEntityName();

  /** Returns the Doctrine ORM class metadata
   * @return \Doctrine\ORM\Mapping\ClassMetadata Doctrine ORM class metadata
   */
  public function getClassMetadata();

  /** Returns the list of routes for common views and operations
   *
   * <P>This method shall return, for each view/operation, an array where:<P>
   * <UL>
   * <LI><B>array[0]</B>: route name (as defined in routing configuration)</LI>
   * <LI><B>array[1]</B>: (optional) array associating the given route slugs and the current route/view slugs</LI>
   * </UL>
   *
   * @return array Array associating view/operation types and their corresponding route (name) and slugs mapping
   */
  public function getRoutes();

  /** Returns the selected data route
   *
   * <P>This method applies to actions: <SAMP>list</SAMP></P>
   *
   * @return array Selected data route (name) and slugs map
   * @see getRoutes()
   */
  public function getRouteSelect();

  /** Returns the label for each data field or resource
   *
   * <P>This method applies to actions: <SAMP>list</SAMP>, <SAMP>detail</SAMP>, <SAMP>insert</SAMP>, <SAMP>update</SAMP></P>
   *
   * @return array|string Array associating fields/reosurces (name) and their corresponding label
   */
  public function getLabels();

  /** Returns the tooltip for each data field or resource
   *
   * <P>This method applies to actions: <SAMP>list</SAMP>, <SAMP>detail</SAMP>, <SAMP>insert</SAMP>, <SAMP>update</SAMP></P>
   *
   * @return array|string Array associating fields/resources (name) and their corresponding tooltip
   */
  public function getTooltips();

  /** Returns the list of fields (name) that may appear
   *
   * <P>This method applies to actions: <SAMP>list</SAMP>, <SAMP>detail</SAMP>, <SAMP>insert</SAMP>, <SAMP>update</SAMP></P>
   *
   * <P>This method allows to override the fields defined by the Doctrine ORM and, in particular,
   * redefine the order in which they are presented to the application.</P>
   *
   * @return array|string Array of fields (name)
   */
  public function getFields();

  /** Returns the fields (name) that should appear by default
   *
   * <P>This method applies to actions: <SAMP>list</SAMP>, <SAMP>detail</SAMP></P>
   *
   * @return array|string Array of default fields (name)
   */
  public function getFieldsDefault();

  /** Returns the fields (name) that must be hidden
   *
   * <P>This method applies to actions: <SAMP>list</SAMP>, <SAMP>detail</SAMP>, <SAMP>insert</SAMP>, <SAMP>update</SAMP></P>
   *
   * @return array|string Array of hidden fields (name)
   */
  public function getFieldsHidden();

  /** Returns the fields (name) that must always appear
   *
   * <P>This method applies to actions: <SAMP>list</SAMP>, <SAMP>detail</SAMP>, <SAMP>insert</SAMP>, <SAMP>update</SAMP></P>
   *
   * @return array|string Array of required fields (name)
   */
  public function getFieldsRequired();

  /** Returns the fields (name) that may not be modified
   *
   * <P>This method applies to actions: <SAMP>insert</SAMP>, <SAMP>update</SAMP></P>
   *
   * @return array|string Array of read-only fields (name)
   */
  public function getFieldsReadonly();

  /** Returns the fields default value
   *
   * <P>This method applies to actions: <SAMP>insert</SAMP></P>
   *
   * <P>This method allows to specify a default value for any given field.</P>
   *
   * @return array|mixed Array associating fields (name) and their default value
   */
  public function getFieldsDefaultValue();

  /** Returns the fields hyperlinks
   *
   * <P>This method applies to actions: <SAMP>list</SAMP>, <SAMP>detail</SAMP></P>
   *
   * <P>This method allows to specify hyperlink resources as an array associating (for any given field):</P>
   * <UL>
   * <LI><B>array[0]</B> hyperlink type: Twig <SAMP>path</SAMP>, <SAMP>path+query</SAMP>, <SAMP>url</SAMP>, <SAMP>url+query</SAMP> or HTML <SAMP>href</SAMP></LI>
   * <LI><B>array[1]</B> hyperlink definition: the corresponding route name</LI>
   * <LI><B>array[2]</B> the route slugs</LI>
   * </UL>
   *
   * @return array|array Array associating fields (name) and their link options
   */
  public function getFieldsLink();

  /** Returns the fields (name) that may be used for data ordering
   *
   * <P>This method applies to actions: <SAMP>list</SAMP></P>
   *
   * <P>This method allows to specify which fields may be used for data ordering. For large
   * dataset, one would typically want to use only <I>indexed</I> database columns.</P>
   *
   * @return array|string Array of order fields (name)
   */
  public function getFieldsOrder();

  /** Returns the fields (name) that may be used for data (field-specific) filtering
   *
   * <P>This method applies to actions: <SAMP>list</SAMP></P>
   *
   * <P>This method allows to specify which fields may be used for data filtering. For large
   * dataset, one would typically want to use only <I>indexed</I> database columns.</P>
   *
   * @return array|string Array of filterable fields (name)
   */
  public function getFieldsFilter();

  /** Returns the fields (name) that may be used for data searching
   *
   * <P>This method applies to actions: <SAMP>list</SAMP></P>
   *
   * <P>This method allows to specify which fields may be used for data searching. For large
   * dataset, one would typically want to use only <I>indexed</I> database columns.</P>
   *
   * @return array|string Array of searchable fields (name)
   */
  public function getFieldsSearch();

  /** Returns the list of (overridden) fields type and options
   *
   * <P>This method applies to actions: <SAMP>insert</SAMP>, <SAMP>update</SAMP></P>
   *
   * <P>This method allows to override the fields type automatically detected from the Doctrine ORM,
   * as weel as specify additional from options (using standard Symfony form stanzas):</P>
   * <UL>
   * <LI><B>array[0]</B> field type (<SAMP>null</SAMP> to preserve auto-detection)</LI>
   * <LI><B>array[1]</B> array of additional form options</LI>
   * </UL>
   *
   * @return array|array Array associating fields (name) and their corresponding type and options
   */
  public function getFieldsForm();

  /** Returns additionnal (footer) hyperlinks
   *
   * <P>This method applies to actions: <SAMP>list</SAMP>, <SAMP>detail</SAMP></P>
   *
   * <P>This method allows to specify additional (footer) hyperlink resources as an array associating (for any given link):</P>
   * <UL>
   * <LI><B>key</B> hyperlink ID: the name of the hyperlink (used for labels and tooltips resolution)</LI>
   * <LI><B>array[0]</B> hyperlink type: Twig <SAMP>path</SAMP>, <SAMP>path+query</SAMP>, <SAMP>url</SAMP>, <SAMP>url+query</SAMP> or HTML <SAMP>href</SAMP></LI>
   * <LI><B>array[1]</B> hyperlink definition: route name (for Twig hyperlinks) or URL (for HTML hyperlinks)</LI>
   * <LI><B>array[2]</B> the route slugs (for Twig hyperlinks) or the anchor target (for HTML hyperlinks)</LI>
   * <LI><B>array[3]</B> optional icon (character)</LI>
   * </UL>
   * <P>For <SAMP>href</SAMP>, the hyperlink definition may contain the following variables:</P>
   * <UL>
   * <LI><B>%{this}</B>: the field value</LI>
   * <LI><B>%{pk}</B>: the data primary key</LI>
   * </UL>
   *
   * @return array|array Array associating hyperlink (ID) and its options
   */
  public function getFooterLinks();

  /** Returns the template resource (name) to be used for view rendering
   * @return string Template resource (name)
   */
  public function getTemplate();

  /** Returns the ad-hoc template data
   * @return array Template data
   */
  public function getTemplateData();

  /** Returns the ad-hoc data browser
   * @param \Symfony\Component\HttpFoundation\Request $oRequest HTTP request (optional)
   * @param string $sUID Browser UID
   * @return BrowserInterface Data browser
   */
  public function getBrowser($oRequest=null, $sUID=null);

}
