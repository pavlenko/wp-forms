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

namespace PE\WP\Forms\Shortcode;

use PE\WP\Forms\Model\FormModel;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChoiceShortcode extends BaseShortcode
{
    /**
     * @var array
     */
    private $choices = [];

    /**
     * TextareaShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode('form-choice-block', function(array $params, $content){
            $this->block($params, $content);
        });

        add_shortcode('form-choice-option', function(array $params){
            $this->option($params);
        });
    }

    /**
     * @param array  $params
     * @param string $content
     */
    public function block($params, $content = null)
    {
        $params = (array) $params;

        /**
         * @var string $name
         * @var string $type
         * @var bool   $multiple
         * @var string $label
         */
        extract(
            shortcode_atts([
                'name'        => '',
                'type'        => 'select',
                'multiple'    => false,
                'label'       => null,
                'placeholder' => null,
            ], $params),
            EXTR_OVERWRITE
        );

        unset($params['name'], $params['type'], $params['multiple'], $params['label']);

        switch ($type) {
            case 'checkboxes':
                $options = ['expanded' => true, 'multiple' => true];
                break;
            case 'radios':
                $options = ['expanded' => true, 'multiple' => false];
                break;
            default:
                $options = ['expanded' => false, 'multiple' => $this->parseBoolean($multiple)];
        }

        $options['label'] = in_array($label, ['false', '0'], false) ? false : $label;

        $this->choices = [];

        do_shortcode($content);

        foreach ($this->choices as $choice) {
            if (!empty($choice['group'])) {
                $options['choices'][$choice['group']][$choice['label']] = $choice['name'];
                $options['choice_attr'][$choice['group']][$choice['label']] = $choice['attr'];
            } else {
                $options['choices'][$choice['label']] = $choice['name'];
                $options['choice_attr'][$choice['label']] = $choice['attr'];
            }
        }

        if ($form = Forms()->getFactory()->form) {
            $form->children[$name] = new FormModel($name, ChoiceType::class, $options);
        }
    }

    /**
     * Add choices to field
     *
     * @param array $params
     */
    public function option($params)
    {
        $params = (array) $params;

        /**
         * @var string $name
         * @var string $label
         * @var string $group
         */
        extract(
            shortcode_atts([
                'name'  => '',
                'label' => '',
                'group' => '',
            ], $params),
            EXTR_OVERWRITE
        );

        unset($params['name'], $params['label'], $params['group']);

        if (!$name && !$label) {
            return;
        }

        if (!$name && $label) {
            $name = $label;
        }

        if (!$label && $name) {
            $label = $name;
        }

        $this->choices[] = [
            'name'  => $name,
            'label' => $label,
            'group' => $group,
            'attr'  => $params,
        ];
    }
}