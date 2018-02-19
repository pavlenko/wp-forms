<?php

namespace PE\WP\Forms\Shortcode;

use PE\WP\Forms\Model\FormModel;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class FieldSetShortcode
{
    public function __construct()
    {
        add_shortcode('form-field-set', $this);
        add_shortcode('_form-field-set', $this);//WARNING! Need for nesting
        add_shortcode('__form-field-set', $this);//WARNING! Need for nesting
        add_shortcode('___form-field-set', $this);//WARNING! Need for nesting
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function __invoke($params, $content = null)
    {
        $params = shortcode_atts([
            'name'  => '',
            'label' => null,
        ], (array) $params);

        /**
         * @var string $name
         * @var string $label
         */
        extract($params, EXTR_OVERWRITE);

        unset($params['name'], $params['label']);

        $factory = Forms()->getFactory();

        // Save reference to parent field
        $parent = $factory->form;

        // Add field and set it as parent for allow add children
        $parent->children[$name] = $factory->form = new FormModel($name, FormType::class, [
            'label'        => in_array($label, ['false', '0'], false) ? false : $label,
            'inherit_data' => true,
        ]);

        // Execute inner shortcodes
        do_shortcode($content);

        // Return reference to parent
        $factory->form = $parent;
    }
}