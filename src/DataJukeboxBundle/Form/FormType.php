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

use Symfony\Component\Form as SymfonyForm;
use Symfony\Component\Form\Extension\Core\Type as CoreType;
use Symfony\Component\OptionsResolver as SymfonyOptions;
use Symfony\Bridge\Doctrine\Form\Type as DoctrineType;

/** DataJukebox-specific form (type)
 *
 * <P>This class provides a form type implementation which integrate the
 * (fields) parameters defined by DataJukebox Properties and provisions
 * single or multiple primary key(s) handling for data insert, update or
 * delete operations.</P>
 * @see \DataJukeboxBundle\DataJukebox\PropertiesInterface
 *
 * @package    DataJukeboxBundle
 * @subpackage SymfonyExtension
 */
class FormType
  extends SymfonyForm\AbstractType
{

  /*
   * CONSTANTS
   ********************************************************************************/

    // FORM
    //   Symfony 3.x: Instead of referencing types by name, you must reference them
    //   by their fully-qualified class name (FQCN)
    //    <-> https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md#form
    //   Backward-compatibility: keep both options (until Symfony 2.x is EOL)
  const TYPE_FQCN_TO_STRING = array(
    CoreType\BirthdayType::class => 'birthday',
    CoreType\ButtonType::class => 'button',
    CoreType\CheckboxType::class => 'checkbox',
    CoreType\ChoiceType::class => 'choice',
    CoreType\CollectionType::class => 'collection',
    CoreType\CountryType::class => 'country',
    CoreType\CurrencyType::class => 'currency',
    CoreType\DateIntervalType::class => 'dateinterval',
    CoreType\DateTimeType::class => 'datetime',
    CoreType\DateType::class => 'date',
    CoreType\EmailType::class => 'email',
    CoreType\FileType::class => 'file',
    CoreType\HiddenType::class => 'hidden',
    CoreType\IntegerType::class => 'integer',
    CoreType\LanguageType::class => 'language',
    CoreType\LocaleType::class => 'locale',
    CoreType\MoneyType::class => 'money',
    CoreType\NumberType::class => 'number',
    CoreType\PasswordType::class => 'password',
    CoreType\PercentType::class => 'percent',
    CoreType\RadioType::class => 'radio',
    CoreType\RangeType::class => 'range',
    CoreType\RepeatedType::class => 'repeated',
    CoreType\ResetType::class => 'reset',
    CoreType\SearchType::class => 'search',
    CoreType\SubmitType::class => 'submit',
    CoreType\TextareaType::class => 'textarea',
    CoreType\TextType::class => 'text',
    CoreType\TimeType::class => 'time',
    CoreType\TimezoneType::class => 'timezone',
    CoreType\UrlType::class => 'url',
    DoctrineType\EntityType::class => 'entity',
  );

  const TYPE_STRING_TO_FQCN = array(
    'birthday' => CoreType\BirthdayType::class,
    'button' => CoreType\ButtonType::class,
    'checkbox' => CoreType\CheckboxType::class,
    'choice' => CoreType\ChoiceType::class,
    'collection' => CoreType\CollectionType::class,
    'country' => CoreType\CountryType::class,
    'currency' => CoreType\CurrencyType::class,
    'dateinterval' => CoreType\DateIntervalType::class,
    'datetime' => CoreType\DateTimeType::class,
    'date' => CoreType\DateType::class,
    'email' => CoreType\EmailType::class,
    'file' => CoreType\FileType::class,
    'hidden' => CoreType\HiddenType::class,
    'integer' => CoreType\IntegerType::class,
    'language' => CoreType\LanguageType::class,
    'locale' => CoreType\LocaleType::class,
    'money' => CoreType\MoneyType::class,
    'number' => CoreType\NumberType::class,
    'password' => CoreType\PasswordType::class,
    'percent' => CoreType\PercentType::class,
    'radio' => CoreType\RadioType::class,
    'range' => CoreType\RangeType::class,
    'repeated' => CoreType\RepeatedType::class,
    'reset' => CoreType\ResetType::class,
    'search' => CoreType\SearchType::class,
    'submit' => CoreType\SubmitType::class,
    'textarea' => CoreType\TextareaType::class,
    'text' => CoreType\TextType::class,
    'time' => CoreType\TimeType::class,
    'timezone' => CoreType\TimezoneType::class,
    'url' => CoreType\UrlType::class,
    'entity' => DoctrineType\EntityType::class,
  );


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
    $oClassMetadata = $this->oProperties->getClassMetadata();
    if (is_null($oClassMetadata)) {
      throw new \RuntimeException('Properties have no associated entity and class metadata');
    }

    return array(
      '_pk' => implode(
        ':',
        array_merge(
          array_fill_keys($oClassMetadata->getIdentifierFieldNames(), null),
          $oData->getPrimaryKey()
        )
      )
    );
  }


  /*
   * METHODS: (Symfony) FormType
   ********************************************************************************/

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    // FORM
    //   Symfony 3.x: The getBlockPrefix() method was added to the FormTypeInterface
    //   in replacement of the getName() method which has been removed.
    //    <-> https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md#form
    //   Backward-compatibility: keep the option (until Symfony 2.x is EOL)
    return $this->oProperties->getName();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(SymfonyForm\FormBuilderInterface $oFormBuilder, array $amOptions)
  {
    // Retrieve properties from options
    if(isset($amOptions['data_properties_object'])) $this->oProperties = $amOptions['data_properties_object'];

    // Make 'data_properties' available to the form view
    $oFormBuilder->setAttribute('data_properties', $amOptions['data_properties']);

    // Action/fields properties
    $oClassMetadata = $this->oProperties->getClassMetadata();
    $sAction = $this->oProperties->getAction();
    $asLabels = $this->oProperties->getLabels();
    $asTooltips = $this->oProperties->getTooltips();
    $asFields_all = $this->oProperties->getFields();
    $asFields_pk = !is_null($oClassMetadata) ? $oClassMetadata->getIdentifierFieldNames() : array();
    $asFields_hidden = $this->oProperties->getFieldsHidden();
    $asFields_required = array_merge($asFields_pk, array_diff($this->oProperties->getFieldsRequired(), $asFields_pk));
    $asFields_readonly = array_merge($asFields_pk, array_diff($this->oProperties->getFieldsReadonly(), $asFields_pk));
    $amFields_default = $this->oProperties->getFieldsDefaultValue();
    $aaFields_form = $this->oProperties->getFieldsForm();

    // Add fields and apply properties
    foreach($asFields_all as $sField) {

      // ... type and options
      if (array_key_exists($sField, $aaFields_form)) {
        $sField_type = array_key_exists(0, $aaFields_form[$sField]) ? $aaFields_form[$sField][0] : null;
        $aField_options = array_key_exists(1, $aaFields_form[$sField]) ? $aaFields_form[$sField][1] : array();
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
        $sField_type = CoreType\HiddenType::class;
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
      switch ((is_null($sField_type) and !is_null($oClassMetadata)) ? $oClassMetadata->getTypeOfField($sField) : $sField_type) {
      case 'datetime':
      case CoreType\DateTimeType::class:
        // We need a properly formatted 'single_text' widget for JQuery
        if (!array_key_exists('widget', $aField_options) or $aField_options['widget']=='single_text') {
          $aField_options = array_merge($aField_options, array('widget' => 'single_text', 'format' => 'yyyy-MM-dd HH:mm'));
        }
        break;
      case 'date':
      case CoreType\DateType::class:
        // We need 'single_text'-style widget along JQuery
        if (!array_key_exists('widget', $aField_options) or $aField_options['widget']=='single_text') {
          $aField_options = array_merge($aField_options, array('widget' => 'single_text', 'format' => 'yyyy-MM-dd'));
        }
        break;
      }

      // FORM
      //   Symfony 3.x:
      //   1. Instead of referencing types by name, you must reference them by their fully-qualified class name (FQCN)
      //   2. Choices must be given as HTML label=>value pairs
      //    <-> https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md#form
      //   Backward-compatibility: keep both options (until Symfony 2.x is EOL)
      if(\Symfony\Component\HttpKernel\Kernel::VERSION_ID<30000) {
        if(array_key_exists($sField_type, self::TYPE_FQCN_TO_STRING))
          $sField_type = self::TYPE_FQCN_TO_STRING[$sField_type];
      } else {
        if(array_key_exists($sField_type, self::TYPE_STRING_TO_FQCN))
          $sField_type = self::TYPE_STRING_TO_FQCN[$sField_type];
        // In DataJukebox, choices shall ALWAYS be given as HTML value=>label pairs (which makes much more sense!)
        if(isset($aField_options['choices']) and is_array($aField_options['choices']))
          $aField_options['choices'] = array_flip($aField_options['choices']);
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

  /**
   * {@inheritdoc}
   */
  public function buildView(SymfonyForm\FormView $oFormView, SymfonyForm\FormInterface $oForm, array $amOptions)
  {
    $oFormView->vars['data_properties'] = $oForm->getConfig()->getAttribute('data_properties');
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(SymfonyOptions\OptionsResolver $oOptionsResolver)
  {
    // FORM
    //   Symfony 3.x: Passing a form type instance to the FormFactory::create*() methods
    //   is not supported anymore. Pass the fully-qualified class name of the type instead.
    //    <-> https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md#form
    //   Backward-compatibility: keep both options (until Symfony 2.x is EOL)
    if(isset($this->oProperties)) {
      $oClassMetadata = $this->oProperties->getClassMetadata();
      $oOptionsResolver->setDefaults(
        array(
          'data_class' => !is_null($oClassMetadata) ? $oClassMetadata->name : null,
          'data_properties' => $this->oProperties->getTemplateData(),
        )
      );
      return;
    }

    // Lazy resolvers (closures)
    // ... 'data_class'
    $fnDataClass = function(SymfonyOptions\Options $amOptions) {
      if(!isset($amOptions['data_properties_object'])) return null;
      $oClassMetadata = $amOptions['data_properties_object']->getClassMetadata();
      return !is_null($oClassMetadata) ? $oClassMetadata->name : null;
    };
    // ... 'data_properties'
    $fnDataProperties = function(SymfonyOptions\Options $amOptions) {
      if(!isset($amOptions['data_properties_object'])) return null;
      return $amOptions['data_properties_object']->getTemplateData();
    };

    // Form defaults
    $oOptionsResolver->setDefined(
      array(
        'data_properties_object',
      )
    );
    $oOptionsResolver->setDefaults(
      array(
        'data_class' => $fnDataClass,
        'data_properties' => $fnDataProperties,
      )
    );
  }

}
