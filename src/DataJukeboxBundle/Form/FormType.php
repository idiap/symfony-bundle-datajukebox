<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\Form\FormType.php

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
 * @subpackage SymfonyExtension
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle\Form;
use DataJukeboxBundle\DataJukebox as DataJukebox;

use Symfony\Component\Form as Form;
use Symfony\Component\OptionsResolver as OptionsResolver;
use Doctrine\ORM as ORM;

/** Symfony generic form (type)
 *
 * <P>This class provides a default form type implementation which provisions
 * single or multiple primary key(s) handling for data insert, update or
 * delete operations, along automatic retrieval of fields properties.</P>
 * @see \DataJukeboxBundle\DataJukebox\PropertiesInterface
 *
 * @package    DataJukeboxBundle
 * @subpackage SymfonyExtension
 */
class FormType
  extends Form\AbstractType
{

  /*
   * PROPERTIES
   ********************************************************************************/

  /** Data properties
   * @var PropertiesInterface
   */
  protected $oProperties;

  /** Debug flag
   * @var boolean
   */
  protected $bDebug = false;

 
  /*
   * METHODS
   ********************************************************************************/

  /*
   * SETTERS
   */

  /** Sets the associated data properties (by reference)
   * @param \DataJukeboxBundle\DataJukebox\PropertiesInterface
   */
  public function setProperties(DataJukebox\PropertiesInterface &$oProperties)
  {
    $this->oProperties =& $oProperties;
  }

  /** Sets the debug flag
   * @param boolean $bDebug Debug flag
   */
  public function setDebug($bDebug)
  {
    $this->bDebug = (boolean)$bDebug;
  }

  /*
   * GETTERS
   */

  /** Returns the associated data properties
   * @return \DataJukeboxBundle\DataJukebox\PropertiesInterface
   */
  public function getProperties()
  {
    return $this->oProperties;
  }

  /** Returns the internal primary key slug (_pk)
   * @return string Internal primary key slug (_pk)
   */
  public function getPrimaryKeySlug($oData) {
    if (!$oData instanceof DataJukebox\PrimaryKeyInterface) {
      throw new \RuntimeException('Data object must implement \DataJukeboxBundle\DataJukebox\PrimaryKeyInterface');
    }

    return array(
      '_pk' => implode(
        ',',
        array_merge(
          array_fill_keys($this->oProperties->getClassMetadata()->getIdentifierFieldNames(), null),
          $oData->getPrimaryKey()
        )
      )
    );
  }


  /*
   * METHODS: AbstractType
   ********************************************************************************/

  public function getName()
  {
    list($sNamespaceAlias, $sSimpleClassName) = explode(':', $this->oProperties->getEntityName());
    return $sSimpleClassName;
  }

  public function buildForm(Form\FormBuilderInterface $oFormBuilder, array $amOptions)
  {
    // Action/fields properties
    $oClassMetadata = $this->oProperties->getClassMetadata();
    $sAction = $this->oProperties->getAction();
    $asLabels = $this->oProperties->getLabels();
    $asTooltips = $this->oProperties->getTooltips();
    $asFields_all = $this->oProperties->getFields();
    $asFields_pk = $oClassMetadata->getIdentifierFieldNames();
    $asFields_hidden = $this->oProperties->getFieldsHidden();
    $asFields_required = array_merge($asFields_pk, array_diff($this->oProperties->getFieldsRequired(), $asFields_pk));
    $asFields_readonly = array_merge($asFields_pk, array_diff($this->oProperties->getFieldsReadonly(), $asFields_pk));
    $amFields_default = $this->oProperties->getFieldsDefaultValue();
    $aaFields_form = $this->oProperties->getFieldsForm();

    // Add fields and apply properties
    foreach($asFields_all as $sField) {

      // ... type and options
      if (array_key_exists($sField, $aaFields_form)) {
        $sField_type = $aaFields_form[$sField][0];
        $aField_options = $aaFields_form[$sField][1];
      } else {
        $sField_type = null;
        $aField_options = array();
      }

      // ... id
      $aField_options = array_merge($aField_options, array('label_attr' => array('ID' => $sField)));

      // ... label
      if (array_key_exists($sField, $asLabels)) {
        $aField_options = array_merge($aField_options, array('label' => $asLabels[$sField]));
      }
      // ... tooltip
      if (array_key_exists($sField, $asTooltips)) {
        $aField_options['label_attr'] = array_merge(
          $aField_options['label_attr'],
          array(
            'TITLE' => $asTooltips[$sField],
            'STYLE' => 'CURSOR:help;',
          )
        );
      }
      // ... hidden
      if (in_array($sField, $asFields_hidden)) {
        if (!in_array($sField, $asFields_required)) continue;
        $sField_type = 'hidden';
      }
      // ... required and read-only (disabled)
      $aField_options = array_merge(
        $aField_options,
        array(
          'required' => in_array($sField, $asFields_required),
          'disabled' => in_array($sField, $asFields_readonly),
        )
      );
      // ... default value
      if ($sAction=='insert' and array_key_exists($sField, $amFields_default)) {
        $aField_options = array_merge($aField_options, array('data' => $amFields_default[$sField]));
      }
      // ... (custom) widget
      switch (!is_null($sField_type) ? $sField_type : $oClassMetadata->getTypeOfField($sField)) {
      case 'datetime':
        // We need a properly formatted 'single_text' widget for JQuery
        if (!array_key_exists('widget', $aField_options) or $aField_options['widget']=='single_text') {
          $aField_options = array_merge($aField_options, array('widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm'));
        }
        break;
      case 'date':
        // We need 'single_text'-style widget along JQuery
        if (!array_key_exists('widget', $aField_options) or $aField_options['widget']=='single_text') {
          $aField_options = array_merge($aField_options, array('widget' => 'single_text', 'format' => 'yyyy-MM-dd'));
        }
        break;
      }

      // Add the field to the form
      if ($this->bDebug) { // DEBUG
        $aField_options_debug = $aField_options;
        array_walk( // avoid circular reference within Symfony resources
          $aField_options_debug,
          function(&$v,$k){
            if (is_object($v)) $v=sprintf('#OBJ{%s}#', get_class($v));
          }
        );
        var_dump(array(
          'field' => $sField,
          'type' => $sField_type,
          'options' => $aField_options_debug,
        ));
      }
      $oFormBuilder->add(
        $sField,
        $sField_type,
        $aField_options
      );

    }

  }

  public function setDefaultOptions(OptionsResolver\OptionsResolverInterface $oOptionsResolver)
  {
    // Form defaults
    $oOptionsResolver->setDefaults(
      array(
        'data_class' => $this->oProperties->getClassMetadata()->name,
        'data_properties' => $this->oProperties->getTemplateData(),
      )
    );
  }

}
