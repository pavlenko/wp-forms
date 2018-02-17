<?php
/**
 * SunNY Creative Technologies
 *
 *   #####                                ##     ##    ##      ##
 * ##     ##                              ###    ##    ##      ##
 * ##                                     ####   ##     ##    ##
 * ##           ##     ##    ## #####     ## ##  ##      ##  ##
 *   #####      ##     ##    ###    ##    ##  ## ##       ####
 *        ##    ##     ##    ##     ##    ##   ####        ##
 *        ##    ##     ##    ##     ##    ##    ###        ##
 * ##     ##    ##     ##    ##     ##    ##     ##        ##
 *   #####        #######    ##     ##    ##     ##        ##
 *
 * C  R  E  A  T  I  V  E     T  E  C  H  N  O  L  O  G  I  E  S
 */

namespace PE\WP\Forms\Model;

use PE\WP\Forms\Action\Action;
use Symfony\Component\Form\FormInterface;

class FormModel
{
    /**
     * @var array
     */
    public $options = [];

    /**
     * @var Action[]
     */
    public $actions = [];

    /**
     * @var FieldModel[]
     */
    public $fields = [];

    /**
     * @var string
     */
    public $theme;

    /**
     * @var FormInterface
     */
    public $instance;
}