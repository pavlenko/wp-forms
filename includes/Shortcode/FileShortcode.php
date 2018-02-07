<?php

namespace SunNYCT\WP\Forms\Shortcode;

use SunNYCT\WP\Forms\Model\FieldModel;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

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
            'max_size'    => '1M',
            'mime_types'  => '',
            'mime_error'  => '',
            'multiple'    => false,
        ], $params);

        $field_attr = [];

        $constraint = [];
        if (!empty($params['max_size'])) {
            $constraint['maxSize'] = $params['max_size'];
        }

        if (!empty($params['mime_types'])) {
            $constraint['mimeTypes'] = explode(',', $params['mime_types']);
        }

        if (!empty($params['mime_error'])) {
            $constraint['mimeTypesMessage'] = $params['mime_error'];
        }

        if (!empty($params['placeholder'])) {
            $field_attr['placeholder'] = $params['placeholder'];
        }

        $params['multiple'] = (bool) $params['multiple'];

        if ($form = Forms()->getFactory()->form) {
            $form->fields[$params['name']] = new FieldModel($params['name'], FileType::class, [
                'label'       => $params['label'],
                'attr'        => $field_attr,
                'multiple'    => $params['multiple'],
                'constraints' => count($constraint) ? [new File($constraint)] : []
            ]);
        }
    }
}