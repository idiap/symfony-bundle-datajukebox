<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Service.php

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

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

/** Data jukebox service
 *
 * @package    DataJukeboxBundle
 * @subpackage SymfonyIntegration
 */
class Service {

  /*
   * PROPERTIES
   ********************************************************************************/

  /** Manager registry (e.g. Doctrine)
   * @var ManagerRegistry
   */
  private $oManagerRegistry;

  /** Service configuration
   * @var array
   */
  private $aConfiguration;


  /*
   * CONSTRUCTORS
   ********************************************************************************/

  public function __construct(ManagerRegistry $oManagerRegistry, $sConfigurationPath=null)
  {
    $this->oManagerRegistry = $oManagerRegistry;
    $aConfiguration = isset($sConfigurationPath) ? Yaml::parse(file_get_contents($sConfigurationPath)) : null;
    $aConfiguration = isset($aConfiguration['DataJukebox']) ? array($aConfiguration['DataJukebox']) : array();
    $this->aConfiguration = (new Processor())->processConfiguration(new ServiceConfiguration(), $aConfiguration);
  }


  /*
   * METHODS
   ********************************************************************************/

  /** Get the service configuration
   * @return array
   */
  public function getConfiguration()
  {
    return $this->aConfiguration;
  }

  /** Get the entity manager for the given entity
   * @param string $sEntityName Entity name (e.g. "FooBundle:BarEntity")
   * @return EntityManager
   */
  public function getEntityManager($sEntityName)
  {
    return $this->oManagerRegistry->getManagerForClass($sEntityName);
  }

  /** Get the fully qualified entity class name for the given entity
   * @param string $sEntityName Entity name (e.g. "FooBundle:BarEntity")
   * @return string
   */
  public function getEntityFqcn($sEntityName)
  {
    static $aEntityFqcn_cache = array();
    if (!in_array($sEntityName, $aEntityFqcn_cache)) {
      list($sNamespaceAlias, $sSimpleClassName) = explode(':', $sEntityName);
      $aEntityFqcn_cache[$sEntityName] = sprintf(
        '%s\\%s',
        $this->getEntityManager($sEntityName)->getConfiguration()->getEntityNamespace($sNamespaceAlias),
        $sSimpleClassName
      );
    }
    return $aEntityFqcn_cache[$sEntityName];
  }

  /** Get the properties annotation object for the given entity
   * @param string $sEntityName Entity name (e.g. "FooBundle:BarEntity")
   * @return DataJukeboxBundle\Annotations\Properties
   */
  public function getAnnotation($sEntityName)
  {
    static $aAnnotationFqcn_cache = array();
    if (!in_array($sEntityName, $aAnnotationFqcn_cache)) {
      $oAnnotationReader = new \Doctrine\Common\Annotations\AnnotationReader();
      $oAnnotation = $oAnnotationReader->getClassAnnotation(
        new \ReflectionClass($this->getEntityFqcn($sEntityName)),
        'DataJukeboxBundle\\Annotations\\Properties'
      );
      if (!$oAnnotation) {
        throw new \Exception(sprintf('No DataJukebox annotation in entity \'%s\'', $sEntityName));
      }
      $aAnnotationFqcn_cache[$sEntityName] = $oAnnotation;
    }
    return $aAnnotationFqcn_cache[$sEntityName];
  }

  /** Get the fully qualified properties class name for the given entity
   * @param string $sEntityName Entity name (e.g. "FooBundle:BarEntity")
   * @return string
   */
  public function getPropertiesFqcn($sEntityName)
  {
    return $this->getAnnotation($sEntityName)->getPropertiesClass();
  }

  /** Get the properties object for the given entity
   * @param string $sEntityName Entity name (e.g. "FooBundle:BarEntity")
   * @return DataJukeboxBundle\DataJukebox\Properties
   */
  public function getProperties($sEntityName)
  {
    static $aProperties_cache = array();
    if (!in_array($sEntityName, $aProperties_cache)) {
      $sPropertiesFqcn = $this->getPropertiesFqcn($sEntityName);
      $aProperties_cache[$sEntityName] = new $sPropertiesFqcn(
        $this->getEntityManager($sEntityName),
        $sEntityName
      );
    }
    return $aProperties_cache[$sEntityName];
  }

  /** Get the repository object for the given entity (properties)
   * @param DataJukeboxBundle\DataJukebox\Properties $oProperties Entity properties object
   * @return DataJukeboxBundle\Repository\Repository
   * @see getProperties()
   */
  public function getRepository(DataJukebox\PropertiesInterface &$oProperties)
  {
    $oEntityManager = $oProperties->getEntityManager();
    $oClassMetadata = $oProperties->getClassMetadata();
    $sRepositoryClassName = $oClassMetadata->customRepositoryClassName;
    if (is_null($sRepositoryClassName)) $sRepositoryClassName = 'DataJukeboxBundle\\Repository\\Repository';
    $oRepository = new $sRepositoryClassName($oEntityManager, $oClassMetadata);
    if (!$oRepository instanceof DataJukebox\RepositoryInterface) {
      throw new \Exception(sprintf('Entity repository \'%s\' must implement \DataJukeboxBundle\DataJukebox\RepositoryInterface', $sRepositoryClassName));
    }
    $oRepository->setProperties($oProperties);
    return $oRepository;
  }

  /** Get the form type object for the given entity (properties)
   * @param DataJukeboxBundle\DataJukebox\Properties $oProperties Entity properties object
   * @return DataJukeboxBundle\Form\FormType
   * @see getProperties()
   */
  public function getFormType(DataJukebox\PropertiesInterface &$oProperties)
  {
    $oFormType = new \DataJukeboxBundle\Form\FormType();
    $oFormType->setProperties($oProperties);
    return $oFormType;
  }

}
