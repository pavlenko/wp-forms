<?php

namespace SunNYCT\WP\Forms\Shortcode;

use SunNYCT\WP\Forms\Model\FieldModel;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class FileShortcode
{
    /**
     * TextareaShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('file', $this);
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function __invoke($params, $content)
    {
        $params = (array) $params;

        $params = shortcode_atts([
            'name'        => '',
            'label'       => '',
            'placeholder' => '',
        ], $params);

        $field_attr = [];

        if (!empty($params['placeholder'])) {
            $field_attr['placeholder'] = $params['placeholder'];
        }

        if ($form = Forms()->getFactory()->form) {
            $form->fields[$params['name']] = new FieldModel($params['name'], FileType::class, [
                'label' => $params['label'],
                'attr'  => $field_attr,
            ]);
        }
    }
}