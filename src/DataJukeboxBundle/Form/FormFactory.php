<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\Form\FormFactory.php

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

use Symfony\Component\Form as SymfonyForm;

/** DataJukebox-specific form factory
 *
 * <P>This class overrides the default form factory such as to use the
 * (variable) DataJukebox Properties name rather than the DataJukebox
 * (fixed) form (type) name when building the form.</P>
 * @see \DataJukeboxBundle\DataJukebox\PropertiesInterface
 *
 * @package    DataJukeboxBundle
 * @subpackage SymfonyExtension
 */
class FormFactory extends SymfonyForm\FormFactory
{
    /*
    * METHODS: (Symfony) FormFactory
    ********************************************************************************/

    /**
     * {@inheritdoc}
     */
    function createNamedBuilder($sName, $sType='Symfony\Component\Form\Extension\Core\Type\FormType', $mData=null, array $amOptions=array())
    {
        if(isset($amOptions['data_properties_object'])) $sName = $amOptions['data_properties_object']->getName();
        return parent::createNamedBuilder($sName, $sType, $mData, $amOptions);
    }
}

