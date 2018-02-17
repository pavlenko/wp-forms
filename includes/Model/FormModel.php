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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class FormModel
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var Action[]
     */
    public $actions = [];

    /**
     * @var FormModel[]
     */
    public $children = [];

    /**
     * @var string
     */
    public $theme;

    /**
     * @var FormInterface
     */
    public $instance;

    /**
     * @param string $name
     * @param string $type
     * @param array  $options
     */
    public function __construct($name = '', $type = '', array $options = [])
    {
        $this->name    = $name;
        $this->type    = $type;
        $this->options = $options;
    }

    /**
     * Build form hierarchy
     *
     * @param FormBuilderInterface $builder
     */
    public function build(FormBuilderInterface $builder)
    {
        $child = $builder->create($this->name, $this->type, $this->options);

        foreach ($this->children as $model) {
            $model->build($child);
        }

        $builder->add($child);
    }
}