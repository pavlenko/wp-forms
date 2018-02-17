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

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @deprecated
 */
class FieldModel
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
     * @var FieldModel[]
     */
    private $children = [];

    /**
     * FieldModel constructor.
     *
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
     * Add child
     *
     * @param string|FieldModel $name
     * @param string            $type
     * @param array              $options
     */
    public function add($name = '', $type = '', array $options = [])
    {
        if ($name instanceof self) {
            $child = $name;
        } else {
            $child = new self($name, $type, $options);
        }

        $this->children[$child->name] = $child;
    }

    /**
     * Has child
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->children);
    }

    /**
     * Get child
     *
     * @param string $name
     *
     * @return FieldModel|null
     */
    public function get($name)
    {
        return array_key_exists($name, $this->children) ? $this->children[$name] : null;
    }

    /**
     * Remove child
     *
     * @param string $name
     */
    public function remove($name)
    {
        unset($this->children[$name]);
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