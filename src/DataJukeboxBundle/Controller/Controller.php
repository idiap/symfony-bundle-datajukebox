<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\Controller\Controller.php

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

namespace DataJukeboxBundle\Controller;
use DataJukeboxBundle\Form\FormType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;

abstract class Controller
  extends SymfonyController
{

  /*
   * METHODS: (Symfony) Controller
   ********************************************************************************/

  /**
   * {@inheritdoc}
   */
  function createForm($oFormType, $mData=null, array $amOptions=array())
  {
    if(!$oFormType instanceof FormType) {
      throw new \RuntimeException('Form type must be a \DataJukeboxBundle\Form\FormType object');
    }

    // FORM
    //   Symfony 3.x: Passing a form type instance to the FormFactory::create*() methods
    //   is not supported anymore. Pass the fully-qualified class name of the type instead.
    //    <-> https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md#form
    //   Backward-compatibility: keep both options (until Symfony 2.x is EOL)
    if(\Symfony\Component\HttpKernel\Kernel::VERSION_ID<30000) {
      return parent::createForm($oFormType, $mData, $amOptions);
    }

    $amOptions['data_properties_object'] = $oFormType->getProperties();
    return $this->container->get('DataJukebox.form.factory')->create(FormType::class, $mData, $amOptions);
  }

  /**
   * {@inheritdoc}
   */
  function createFormBuilder($mData=null, array $amOptions=array())
  {
    // FORM
    //   Symfony 3.x: Passing a form type instance to the FormFactory::create*() methods
    //   is not supported anymore. Pass the fully-qualified class name of the type instead.
    //    <-> https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md#form
    //   Backward-compatibility: keep both options (until Symfony 2.x is EOL)
    if(\Symfony\Component\HttpKernel\Kernel::VERSION_ID<30000) {
      return parent::createFormBuilder($mData, $amOptions);
    }

    return $this->container->get('DataJukebox.form.factory')->createBuilder(FormType::class, $mData, $amOptions);
  }

}
