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

namespace SunNYCT\WP\Forms\Model;

use SunNYCT\WP\Forms\Action\Action;
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
     * @var FormInterface
     */
    public $instance;
}