<?php

namespace PE\WP\Forms\Shortcode;

use PE\WP\Forms\Model\FormModel;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class FileShortcode
{
    /**
     * TextareaShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('form-file', $this);
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function __invoke($params, $content)
    {
        $params = (array) $params;

        /**
         * @var string $name
         * @var string $label
         * @var string $max_size
         * @var string $mime_types
         * @var string $mime_error
         * @var bool   $multiple
         */
        extract(
            shortcode_atts([
                'name'        => '',
                'label'       => null,
                'max_size'    => '',
                'mime_types'  => '',
                'mime_error'  => '',
                'multiple'    => false,
            ], $params),
            EXTR_OVERWRITE
        );

        unset(
            $params['name'],
            $params['label'],
            $params['max_size'],
            $params['mime_types'],
            $params['mime_error'],
            $params['multiple']
        );

        $constraint = [];
        if ($max_size) {
            $constraint['maxSize'] = $max_size;
        }

        if ($mime_types) {
            $constraint['mimeTypes'] = explode(',', $mime_types);
        }

        if ($mime_error) {
            $constraint['mimeTypesMessage'] = $mime_error;
        }

        if ($form = Forms()->getFactory()->form) {
            $form->children[$name] = new FormModel($name, FileType::class, [
                'label'       => in_array($label, ['false', '0'], false) ? false : $label,
                'attr'        => $params,
                'multiple'    => $multiple,
                'constraints' => count($constraint)
                    ? [$multiple ? new All([new File($constraint)]): new File($constraint)]
                    : []
            ]);
        }
    }
}