<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\TwigExtension.php

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
 * @subpackage SymfonyIntegration
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle;

use Twig\Extension\AbstractExtension;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Twig\Template;

/** Twig extension
 * @package    DataJukeboxBundle
 * @subpackage SymfonyIntegration
 */
class TwigExtension extends AbstractExtension
{
  private $environment;
  /** Twig template (list view)
   *  @var TwigTemplate
   */
  private $oTwigTemplate_list;

  /** Twig template (detail view)
   *  @var TwigTemplate
   */
  private $oTwigTemplate_detail;

  /** Twig template (popup)
   *  @var TwigTemplate
   */
  private $oTwigTemplate_popup;

  public function __construct(Environment $environment)
  {
    $this->environment = $environment;
  }

  public function getName()
  {
    return 'DataJukebox';
  }

  public function getFunctions()
  {
    return array(

      // List view
      new TwigFunction('DataJukebox_list', array($this, 'renderList'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listData', array($this, 'renderListData'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listHeader', array($this, 'renderListHeader'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listTitle', array($this, 'renderListTitle'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listDisplay', array($this, 'renderListDisplay'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listSearch', array($this, 'renderListSearch'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listLabel', array($this, 'renderListLabel'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listFilter', array($this, 'renderListFilter'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listRows', array($this, 'renderListRows'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listRow', array($this, 'renderListRow'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listFooter', array($this, 'renderListFooter'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listBrowser', array($this, 'renderListBrowser'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listActions', array($this, 'renderListActions'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listLinks', array($this, 'renderListLinks'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listHelp', array($this, 'renderListHelp'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listJavascript', array($this, 'renderListJavascript'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_listTemplate', array($this, 'initListTemplate'), array('is_safe' => array('html'))),

      // Detail view
      new TwigFunction('DataJukebox_detail', array($this, 'renderDetail'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_detailData', array($this, 'renderDetailData'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_detailHeader', array($this, 'renderDetailHeader'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_detailTitle', array($this, 'renderDetailTitle'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_detailDisplay', array($this, 'renderDetailDisplay'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_detailSearch', array($this, 'renderDetailSearch'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_detailRow', array($this, 'renderDetailRow'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_detailFooter', array($this, 'renderDetailFooter'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_detailActions', array($this, 'renderDetailActions'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_detailLinks', array($this, 'renderDetailLinks'), array('is_safe' => array('html'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_detailJavascript', array($this, 'renderDetailJavascript'), array('is_safe' => array('html'), 'needs_environment' => true)),
      // new TwigFunction('DataJukebox_detailTemplate', array($this, 'initDetailTemplate'), array('is_safe' => array('html'), 'needs_environment' => true)),

      // Popup
      new TwigFunction('DataJukebox_popupContainer', array($this, 'renderPopupContainer'), array('is_safe' => array('html'), 'needs_environment' => true)),
      // new TwigFunction('DataJukebox_popupTemplate', array($this, 'initPopupTemplate'), array('is_safe' => array('html'), 'needs_environment' => true)),

      // Export
      new TwigFunction('DataJukebox_csv', array($this, 'renderCSV'), array('is_safe' => array('txt'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_xml', array($this, 'renderXML'), array('is_safe' => array('txt'), 'needs_environment' => true)),
      new TwigFunction('DataJukebox_json', array($this, 'renderJSON'), array('is_safe' => array('txt'), 'needs_environment' => true)),

    );
  }

  public function getFilters()
  {
    return array(

      // Format
      new TwigFilter('DataJukebox_format', array($this, 'formatHTML'), array('is_safe' => array('html'))),
      new TwigFilter('DataJukebox_formatDateTime', array($this, 'formatDateTime'), array('is_safe' => array('html'))),
      new TwigFilter('DataJukebox_formatCSV', array($this, 'formatCSV'), array('is_safe' => array('txt'))),
      new TwigFilter('DataJukebox_formatXML', array($this, 'formatXML'), array('is_safe' => array('txt'))),
      new TwigFilter('DataJukebox_formatJSON', array($this, 'formatJSON'), array('is_safe' => array('txt'))),

      // Helpers
      new TwigFilter('DataJukebox_map', array($this, 'arrayMap')),

    );
  }


  /*
   * METHODS
   ********************************************************************************/

  /*
   * List view
   */

  public function renderList(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_list', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListData(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listData', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListHeader(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listHeader', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListTitle(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listTitle', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListDisplay(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listDisplay', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListSearch(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listSearch', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListLabel(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listLabel', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListFilter(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listFilter', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListRows(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listRows', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListRow(Environment $environment, $data, $row, $loop)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listRow', $environment->mergeGlobals(array('data' => $data, 'row' => $row, 'loop' => $loop)));
  }

  public function renderListFooter(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listFooter', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListBrowser(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listBrowser', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListActions(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listActions', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListLinks(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listLinks', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListHelp(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listHelp', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderListJavascript(Environment $environment, $data)
  {
    return $this->loadListTemplate($environment)->renderBlock('DataJukebox_listJavascript', $environment->mergeGlobals(array('data' => $data)));
  }

  public function initListTemplate($template)
  {
    $this->oTwigTemplate_list = $this->environment->load($template);
    // $this->oTwigTemplate_list = $this->loadListTemplate($environment);
  }

  /*
   * Detail view
   */

  public function renderDetail(Environment $environment, $data, $listRoute = null)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detail', $environment->mergeGlobals(array('data' => $data, 'listRoute' => $listRoute)));
  }

  public function renderDetailData(Environment $environment, $data, $listRoute = null)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detailData', $environment->mergeGlobals(array('data' => $data, 'listRoute' => $listRoute)));
  }

  public function renderDetailHeader(Environment $environment, $data, $listRoute = null)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detailHeader', $environment->mergeGlobals(array('data' => $data, 'listRoute' => $listRoute)));
  }

  public function renderDetailTitle(Environment $environment, $data)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detailTitle', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailDisplay(Environment $environment, $data)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detailDisplay', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailSearch(Environment $environment, $data, $listRoute = null)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detailSearch', $environment->mergeGlobals(array('data' => $data, 'listRoute' => $listRoute)));
  }

  public function renderDetailRow(Environment $environment, $data)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detailRow', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailFooter(Environment $environment, $data)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detailFooter', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailActions(Environment $environment, $data)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detailActions', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailLinks(Environment $environment, $data)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detailLinks', $environment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailJavascript(Environment $environment, $data)
  {
    return $this->loadDetailTemplate($environment)->renderBlock('DataJukebox_detailJavascript', $environment->mergeGlobals(array('data' => $data)));
  }

  // public function initDetailTemplate($template)
  // {
  //   $this->loadDetailTemplate($environment) = $environment->load($template);
  // }

  /*
   * Popup
   */

  public function renderPopupContainer(Environment $environment)
  {
    return $this->loadPopupTemplate($environment)->renderBlock('DataJukebox_popupContainer', array());
  }

  // public function initPopupTemplate(Environment $environment, $template)
  // {
  //   $this->oTwigTemplate_popup = $environment->load($template);
  // }

  /*
   * Export
   */

  public function renderCSV(Environment $environment, $data)
  {
    // CSV output (as per RFC4180)
    $sOutput = '';
    // ... header
    $f=0; foreach ($data['browser']['fields'] as $sField) {
      if (in_array($sField, $data['properties']['fields_hidden'])) continue;
      if ($f++) $sOutput .= ',';
      $sOutput .= $this->formatCSV($sField);
    }
    // ... data
    $l=1; foreach ($data['rows'] as $aRow) {
      if ($l++) $sOutput .= "\r\n";
      $f=0; foreach ($data['browser']['fields'] as $sField) {
        if (in_array($sField, $data['properties']['fields_hidden'])) continue;
        $mValue = $aRow[$sField];
        $sField_formatted = sprintf('%s_formatted', $sField);
        if (array_key_exists($sField_formatted, $aRow)) $mValue = $aRow[$sField_formatted];
        if ($f++) $sOutput .= ',';
        if (is_null($mValue)) continue;
        $sOutput .= $this->formatCSV($mValue);
      }
    }

    // Done
    return $sOutput;
  }

  public function renderXML(Environment $environment, $data)
  {
    // XML output
    $sOutput = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    // ... header
    $sOutput .= sprintf("<%s>\r\n", $data['properties']['name']);
    // ... data
    foreach ($data['rows'] as $aRow) {
      $sOutput .= "  <Item>\r\n";
      $f=0; foreach ($data['browser']['fields'] as $sField) {
        if (in_array($sField, $data['properties']['fields_hidden'])) continue;
        $mValue = $aRow[$sField];
        $sField_formatted = sprintf('%s_formatted', $sField);
        if (array_key_exists($sField_formatted, $aRow)) $mValue = $aRow[$sField_formatted];
        if (is_null($mValue)) continue;
        $sOutput .= sprintf("    <%s>%s</%s>\r\n", $sField, $this->formatXML($mValue), $sField);
      }
      $sOutput .= "  </Item>\r\n";
    }
    // ... footer
    $sOutput .= sprintf("</%s>\r\n", $data['properties']['name']);

    // Done
    return $sOutput;
  }

  public function renderJSON(Environment $environment, $data)
  {
    // XML output
    // ... header
    $sOutput = "{\r\n";
    // ... data
    $sOutput .= sprintf("  \"%s\": [\r\n", $data['properties']['name']);
    $sOutput .= "    {\r\n";
    $l=0; foreach ($data['rows'] as $aRow) {
      if ($l++) $sOutput .= "    },\r\n    {\r\n";
      $f=0; foreach ($data['browser']['fields'] as $sField) {
        if (in_array($sField, $data['properties']['fields_hidden'])) continue;
        $mValue = $aRow[$sField];
        $sField_formatted = sprintf('%s_formatted', $sField);
        if (array_key_exists($sField_formatted, $aRow)) $mValue = $aRow[$sField_formatted];
        if (is_null($mValue)) continue;
        if ($f++) $sOutput .= ",\r\n";
        $sOutput .= sprintf('      "%s": %s', $sField, $this->formatJSON($mValue));
      }
      $sOutput .= "\r\n";
    }
    $sOutput .= "    }\r\n";
    $sOutput .= "  ]\r\n";
    // ... footer
    $sOutput .= "}\r\n";

    // Done
    return $sOutput;
  }

  /*
   * Format
   */

  public function formatDateTime(\DateTime $oDateTime, $sFormat=null)
  {
    if (is_null($sFormat)) {
      $sOutput = $oDateTime->format('Y-m-d H:i:s');
      $sOutput = preg_replace('/^(0000-00-00|1970-01-01) /', '', $sOutput);
      $sOutput = preg_replace('/( 00:00:00|:00)$/', '', $sOutput);
    } else {
      $sOutput = $oDateTime->format($sFormat);
    }
    return $sOutput;
  }

  public function formatHTML($mValue)
  {
    if (is_bool($mValue)) return $mValue ? '&#x2611;' : '&#x2610;';
    if (is_object($mValue)) {
      if ($mValue instanceof \DateTime) return $this->formatDateTime($mValue);
      return sprintf('#OBJ{%s}#', get_class($mValue));
    }
    if (is_resource($mValue)) return '#RES#';
    return nl2br(htmlspecialchars($mValue));
  }

  public function formatCSV($mValue)
  {
    if (is_integer($mValue)) return sprintf('%d', $mValue);
    if (is_float($mValue)) return sprintf('%f', $mValue);
    if (is_bool($mValue)) return $mValue ? 'true' : 'false';
    if (is_object($mValue)) {
      if ($mValue instanceof \DateTime) return $this->formatDateTime($mValue);
      return sprintf('#OBJ{%s}#', get_class($mValue));
    }
    if (is_resource($mValue)) return '#RES#';
    return sprintf('"%s"', str_replace('"', '""', $mValue));
  }

  public function formatXML($mValue)
  {
    if (is_bool($mValue)) return $mValue ? 'true' : 'false';
    if (is_object($mValue)) {
      if ($mValue instanceof \DateTime) return $this->formatDateTime($mValue);
      return sprintf('#OBJ{%s}#', get_class($mValue));
    }
    if (is_resource($mValue)) return '#RES#';
    return htmlspecialchars($mValue, ENT_XML1, 'UTF-8');
  }

  public function formatJSON($mValue)
  {
    if (is_integer($mValue)) return sprintf('%d', $mValue);
    if (is_float($mValue)) return sprintf('%f', $mValue);
    if (is_bool($mValue)) return $mValue ? 'true' : 'false';
    if (is_object($mValue)) {
      if ($mValue instanceof \DateTime) return sprintf('"%s"', $this->formatDateTime($mValue));
      return sprintf('"#OBJ{%s}#"', get_class($mValue));
    }
    if (is_resource($mValue)) return '"#RES#"';
    return sprintf('"%s"', addcslashes($mValue, "\\\"\f\n\r\t"));
  }

  /*
   * Helpers
   */

  public function arrayMap($aMap,$aValues)
  {
    $aMap_out = array();
    foreach($aMap as $k => $m) {
      if (array_key_exists($m,$aValues)) {
        if (is_null($aValues[$m])) continue;
        $aMap_out[$k] = $aValues[$m];
      } else {
        $aMap_out[$k] = $m;
      }
    }
    return $aMap_out;
  }

  private function loadListTemplate(Environment $environment) {
    return $environment->load('@DataJukebox/DataJukebox/list.html.twig');
  }

  private function loadDetailTemplate(Environment $environment) {
    return $environment->load('@DataJukebox/DataJukebox/detail.html.twig');
  }

  private function loadPopupTemplate(Environment $environment) {
    return $environment->load('@DataJukebox/DataJukebox/popup.html.twig');
  }
}
