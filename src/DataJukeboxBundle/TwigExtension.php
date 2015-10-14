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
{

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
      new \Twig_SimpleFunction(
        'DataJukebox_list',
        array($this, 'renderList'),
        array('needs_environment' => true, 'is_safe' => array('html'))
      ),
      new \Twig_SimpleFunction(
        'DataJukebox_detail',
        array($this, 'renderDetail'),
        array('needs_environment' => true, 'is_safe' => array('html'))
      ),
      new \Twig_SimpleFunction(
        'DataJukebox_csv',
        array($this, 'renderCSV'),
        array('is_safe' => array('txt'))
      ),
      new \Twig_SimpleFunction(
        'DataJukebox_xml',
        array($this, 'renderXML'),
        array('is_safe' => array('txt'))
      ),
      new \Twig_SimpleFunction(
        'DataJukebox_json',
        array($this, 'renderJSON'),
        array('is_safe' => array('txt'))
      ),
      new \Twig_SimpleFunction(
        'DataJukebox_popupContainer',
        array($this, 'renderPopupContainer'),
        array('needs_environment' => true, 'is_safe' => array('html'))
      ),
    );
  }

  public function getFilters()
  {
    return array(
      new \Twig_SimpleFilter(
        'DataJukebox_format',
        array($this, 'formatHTML'),
        array('is_safe' => array('html'))
      ),
      new \Twig_SimpleFilter(
        'DataJukebox_formatCSV',
        array($this, 'formatCSV'),
        array('is_safe' => array('txt'))
      ),
      new \Twig_SimpleFilter(
        'DataJukebox_formatXML',
        array($this, 'formatXML'),
        array('is_safe' => array('txt'))
      ),
      new \Twig_SimpleFilter(
        'DataJukebox_formatJSON',
        array($this, 'formatJSON'),
        array('is_safe' => array('txt'))
      ),
      new \Twig_SimpleFilter(
        'DataJukebox_map',
        array($this, 'arrayMap')
      ),
    );
  }


  /*
   * METHODS
   ********************************************************************************/

  public function renderList(\Twig_Environment $oTwigEnvironment, $data)
  {
    $oTemplate = $oTwigEnvironment->loadTemplate('DataJukeboxBundle:DataJukebox:list.html.twig');
    return $oTemplate->renderBlock('DataJukebox_list', $oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderDetail(\Twig_Environment $oTwigEnvironment, $data)
  {
    $oTemplate = $oTwigEnvironment->loadTemplate('DataJukeboxBundle:DataJukebox:detail.html.twig');
    return $oTemplate->renderBlock('DataJukebox_detail', $oTwigEnvironment->mergeGlobals(array('data' => $data)));
  }

  public function renderPopupContainer(\Twig_Environment $oTwigEnvironment)
  {
    $oTemplate = $oTwigEnvironment->loadTemplate('DataJukeboxBundle:DataJukebox:popup.html.twig');
    return $oTemplate->renderBlock('DataJukebox_popupContainer', array());
  }

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

  public function formatDateTime(\DateTime $oDateTime)
  {
    $sOutput = $oDateTime->format('Y-m-d H:i:s');
    $sOutput = preg_replace('/^(0000-00-00|1970-01-01) /', '', $sOutput);
    $sOutput = preg_replace('/( 00:00:00|:00)$/', '', $sOutput);
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
    if (is_bool($mValue)) return $mValue ? 'true' : 'false';
    if (is_object($mValue)) {
      if ($mValue instanceof \DateTime) return sprintf('"%s"', $this->formatDateTime($mValue));
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
    if (is_bool($mValue)) return $mValue ? 'true' : 'false';
    if (is_object($mValue)) {
      if ($mValue instanceof \DateTime) return sprintf('"%s"', $this->formatDateTime($mValue));
      return sprintf('"#OBJ{%s}#"', get_class($mValue));
    }
    if (is_resource($mValue)) return '"#RES#"';
    return sprintf('"%s"', addcslashes($mValue, "\\\"\f\n\r\t"));
  }

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
