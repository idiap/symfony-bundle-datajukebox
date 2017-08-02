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

/** Twig extension
 * @package    DataJukeboxBundle
 * @subpackage SymfonyIntegration
 */
class TwigExtension
  extends \Twig_Extension
  implements \Twig_Extension_InitRuntimeInterface
{

  /*
   * PROPERTIES
   ********************************************************************************/

  /** Twig environment
   *  @var \Twig_Environment
   */
  private $oTwigEnvironment;

  /** Twig template (list view)
   *  @var \Twig_TemplateInterface
   */
  private $oTwigTemplate_list;

  /** Twig template (detail view)
   *  @var \Twig_TemplateInterface
   */
  private $oTwigTemplate_detail;

  /** Twig template (popup)
   *  @var \Twig_TemplateInterface
   */
  private $oTwigTemplate_popup;


  /*
   * METHODS: Twig_Extension_InitRuntimeInterface
   ********************************************************************************/

  public function initRuntime(\Twig_Environment $oTwigEnvironment)
  {
    $this->oTwigEnvironment = $oTwigEnvironment;
    $this->oTwigTemplate_list = $this->oTwigEnvironment->loadTemplate('@DataJukebox/DataJukebox/list.html.twig');
    $this->oTwigTemplate_detail = $this->oTwigEnvironment->loadTemplate('@DataJukebox/DataJukebox/detail.html.twig');
    $this->oTwigTemplate_popup = $this->oTwigEnvironment->loadTemplate('@DataJukebox/DataJukebox/popup.html.twig');
  }


  /*
   * METHODS: Twig_Extension
   ********************************************************************************/

  public function getName()
  {
    return 'DataJukebox';
  }

  public function getFunctions()
  {
    return array(

      // List view
      new \Twig_SimpleFunction('DataJukebox_list', array($this, 'renderList'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listData', array($this, 'renderListData'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listHeader', array($this, 'renderListHeader'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listTitle', array($this, 'renderListTitle'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listDisplay', array($this, 'renderListDisplay'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listSearch', array($this, 'renderListSearch'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listLabel', array($this, 'renderListLabel'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listFilter', array($this, 'renderListFilter'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listRows', array($this, 'renderListRows'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listRow', array($this, 'renderListRow'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listFooter', array($this, 'renderListFooter'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listBrowser', array($this, 'renderListBrowser'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listActions', array($this, 'renderListActions'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listLinks', array($this, 'renderListLinks'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listHelp', array($this, 'renderListHelp'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listJavascript', array($this, 'renderListJavascript'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_listTemplate', array($this, 'initListTemplate'), array('is_safe' => array('html'))),

      // Detail view
      new \Twig_SimpleFunction('DataJukebox_detail', array($this, 'renderDetail'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_detailData', array($this, 'renderDetailData'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_detailHeader', array($this, 'renderDetailHeader'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_detailTitle', array($this, 'renderDetailTitle'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_detailDisplay', array($this, 'renderDetailDisplay'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_detailRow', array($this, 'renderDetailRow'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_detailFooter', array($this, 'renderDetailFooter'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_detailActions', array($this, 'renderDetailActions'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_detailLinks', array($this, 'renderDetailLinks'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_detailJavascript', array($this, 'renderDetailJavascript'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_detailTemplate', array($this, 'initDetailTemplate'), array('is_safe' => array('html'))),

      // Popup
      new \Twig_SimpleFunction('DataJukebox_popupContainer', array($this, 'renderPopupContainer'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('DataJukebox_popupTemplate', array($this, 'initPopupTemplate'), array('is_safe' => array('html'))),

      // Export
      new \Twig_SimpleFunction('DataJukebox_csv', array($this, 'renderCSV'), array('is_safe' => array('txt'))),
      new \Twig_SimpleFunction('DataJukebox_xml', array($this, 'renderXML'), array('is_safe' => array('txt'))),
      new \Twig_SimpleFunction('DataJukebox_json', array($this, 'renderJSON'), array('is_safe' => array('txt'))),

    );
  }

  public function getFilters()
  {
    return array(

      // Format
      new \Twig_SimpleFilter('DataJukebox_format', array($this, 'formatHTML'), array('is_safe' => array('html'))),
      new \Twig_SimpleFilter('DataJukebox_formatDateTime', array($this, 'formatDateTime'), array('is_safe' => array('html'))),
      new \Twig_SimpleFilter('DataJukebox_formatCSV', array($this, 'formatCSV'), array('is_safe' => array('txt'))),
      new \Twig_SimpleFilter('DataJukebox_formatXML', array($this, 'formatXML'), array('is_safe' => array('txt'))),
      new \Twig_SimpleFilter('DataJukebox_formatJSON', array($this, 'formatJSON'), array('is_safe' => array('txt'))),

      // Helpers
      new \Twig_SimpleFilter('DataJukebox_map', array($this, 'arrayMap')),

    );
  }


  /*
   * METHODS
   ********************************************************************************/

  /*
   * List view
   */

  public function renderList($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_list', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListData($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listData', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListHeader($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listHeader', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListTitle($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listTitle', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListDisplay($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listDisplay', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListSearch($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listSearch', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListLabel($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listLabel', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListFilter($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listFilter', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListRows($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listRows', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListRow($data, $row, $loop)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listRow', $this->oTwigEnvironment->mergeGlobals(array('data' => $data, 'row' => $row, 'loop' => $loop)));
  }

  public function renderListFooter($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listFooter', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListBrowser($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listBrowser', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListActions($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listActions', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListLinks($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listLinks', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListHelp($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listHelp', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderListJavascript($data)
  {
    return $this->oTwigTemplate_list->renderBlock('DataJukebox_listJavascript', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function initListTemplate($template)
  {
    $this->oTwigTemplate_list = $this->oTwigEnvironment->loadTemplate($template);
  }

  /*
   * Detail view
   */

  public function renderDetail($data)
  {
    return $this->oTwigTemplate_detail->renderBlock('DataJukebox_detail', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailData($data)
  {
    return $this->oTwigTemplate_detail->renderBlock('DataJukebox_detailData', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailHeader($data)
  {
    return $this->oTwigTemplate_detail->renderBlock('DataJukebox_detailHeader', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailTitle($data)
  {
    return $this->oTwigTemplate_detail->renderBlock('DataJukebox_detailTitle', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailDisplay($data)
  {
    return $this->oTwigTemplate_detail->renderBlock('DataJukebox_detailDisplay', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailRow($data)
  {
    return $this->oTwigTemplate_detail->renderBlock('DataJukebox_detailRow', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailFooter($data)
  {
    return $this->oTwigTemplate_detail->renderBlock('DataJukebox_detailFooter', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailActions($data)
  {
    return $this->oTwigTemplate_detail->renderBlock('DataJukebox_detailActions', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailLinks($data)
  {
    return $this->oTwigTemplate_detail->renderBlock('DataJukebox_detailLinks', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetailJavascript($data)
  {
    return $this->oTwigTemplate_detail->renderBlock('DataJukebox_detailJavascript', $this->oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function initDetailTemplate($template)
  {
    $this->oTwigTemplate_detail = $this->oTwigEnvironment->loadTemplate($template);
  }

  /*
   * Popup
   */

  public function renderPopupContainer()
  {
    return $this->oTwigTemplate_popup->renderBlock('DataJukebox_popupContainer', array());
  }

  public function initPopupTemplate($template)
  {
    $this->oTwigTemplate_popup = $this->oTwigEnvironment->loadTemplate($template);
  }

  /*
   * Export
   */

  public function renderCSV($data)
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

  public function renderXML($data)
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

  public function renderJSON($data)
  {
    // XML output
    // ... header
    $sOutput .= "{\r\n";
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

}
