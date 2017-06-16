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

namespace SunNYCT\WP\Forms;

use SunNYCT\WP\Forms\Admin\Admin;
use SunNYCT\WP\Forms\Shortcode\ActionShortcode;
use SunNYCT\WP\Forms\Shortcode\ButtonShortcode;
use SunNYCT\WP\Forms\Shortcode\ChoiceShortcode;
use SunNYCT\WP\Forms\Shortcode\FormAttributesShortcode;
use SunNYCT\WP\Forms\Shortcode\FormShortcode;
use SunNYCT\WP\Forms\Shortcode\TextareaShortcode;
use SunNYCT\WP\Forms\Shortcode\TextShortcode;
use SunNYCT\WP\Forms\Shortcode\ValidateShortcode;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

class Forms
{
    /**
     * @var static
     */
    private static $instance;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @return static
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        $locale = substr(get_locale(), 0, 2);

        $formFactoryBuilder = \Symfony\Component\Form\Forms::createFormFactoryBuilder();

        $translator = new Translator($locale);
        $translator->setFallbackLocales(['en']);
        $translator->addLoader('xlf', new XliffFileLoader());

        $formVendorDIR       = dirname((new \ReflectionClass(\Symfony\Component\Form\Forms::class))->getFileName());
        $validatorVendorDIR  = dirname((new \ReflectionClass(Validation::class))->getFileName());
        $twigBridgeVendorDIR = dirname((new \ReflectionClass(AppVariable::class))->getFileName());

        // Load fallback locale messages
        $translator->addResource('xlf', $formVendorDIR . '/Resources/translations/validators.en.xlf', 'en', 'validators');
        $translator->addResource('xlf', $validatorVendorDIR . '/Resources/translations/validators.en.xlf', 'en', 'validators');

        // Load current locale messages
        if (file_exists($file = $formVendorDIR . '/Resources/translations/validators.' . $locale . '.xlf')) {
            $translator->addResource('xlf', $file, $locale, 'validators');
        }

        if (file_exists($file = $validatorVendorDIR . '/Resources/translations/validators.' . $locale . '.xlf')) {
            $translator->addResource('xlf', $file, $locale, 'validators');
        }

        $validatorBuilder = Validation::createValidatorBuilder();
        $validatorBuilder->setTranslationDomain('validators');
        $validatorBuilder->setTranslator($translator);

        $formFactoryBuilder->addExtension(new ValidatorExtension($validatorBuilder->getValidator()));

        $this->formFactory = $formFactoryBuilder->getFormFactory();

        $viewDIRs = [];

        if (
            get_template_directory() !== get_stylesheet_directory()
            && is_dir(get_stylesheet_directory() . '/' . SUNNYCT_WP_FORMS_PLUGIN_NAME)
        ) {
            $viewDIRs[] = get_stylesheet_directory() . '/' . SUNNYCT_WP_FORMS_PLUGIN_NAME;
        }

        if (is_dir(get_template_directory() . '/' . SUNNYCT_WP_FORMS_PLUGIN_NAME)) {
            $viewDIRs[] = get_template_directory() . '/' . SUNNYCT_WP_FORMS_PLUGIN_NAME;
        }

        $viewDIRs[] = __DIR__ . '/../templates';
        $viewDIRs[] = $twigBridgeVendorDIR . '/Resources/views/Form';

        $this->twig = new Environment(new FilesystemLoader($viewDIRs));

        // Add FormRenderer factory runtime loader
        $this->twig->addRuntimeLoader(new FactoryRuntimeLoader([
            TwigRenderer::class => function() {
                return new FormRenderer(new TwigRendererEngine(array('sunnyct-wp-forms-theme.twig'), $this->twig));
            },
        ]));

        // Add the FormExtension to Twig
        $this->twig->addExtension(new FormExtension());
        $this->twig->addExtension(new TranslationExtension($translator));

        $this->twig->addGlobal('WP_SITEURL', WP_SITEURL);

        $this->factory = new Factory($this->formFactory);

        new Admin();//TODO create custom admin, maybe

        add_action('init', function(){ $this->onInit(); });
    }

    private function onInit()
    {
        register_post_type(SUNNYCT_WP_FORMS_PLUGIN_NAME, array(
            'label'               => __('Forms', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'singular_label'      => __('Form', SUNNYCT_WP_FORMS_PLUGIN_NAME),
            'public'              => true,
            'show_ui'             => true,
            'show_in_nav_menus'   => false,
            'capability_type'     => 'page',
            'rewrite'             => false,
            'supports'            => array(
                'title',
                'editor',
            ),
        ));

        //TODO create custom admin if possible
        /*add_action('add_meta_boxes', function(){
            // Remove the Yoast SEO metabox
            remove_meta_box('wpseo_meta', SUNNYCT_WP_FORMS_PLUGIN_NAME, 'normal');
        }, 100);

        add_filter('manage_edit-' . SUNNYCT_WP_FORMS_PLUGIN_NAME . '_columns', function($columns){
            // Remove the Yoast SEO columns
            unset(
                $columns['wpseo-score'],
                $columns['wpseo-title'],
                $columns['wpseo-metadesc'],
                $columns['wpseo-focuskw'],
                $columns['wpseo-score-readability']
            );
            return $columns;
        });*/

        new FormShortcode();
        new FormAttributesShortcode();
        new TextareaShortcode();
        new ButtonShortcode();
        new TextShortcode();
        new ChoiceShortcode();

        new ValidateShortcode();

        new ActionShortcode();

        new AJAXHandler();

        wp_register_script(
            SUNNYCT_WP_FORMS_PLUGIN_NAME,
            plugin_dir_url(__DIR__) . '/js/sunnyct-wp-forms.js',
            ['jquery'],
            SUNNYCT_WP_FORMS_PLUGIN_VERSION,
            true
        );
    }

    /**
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * Render form to HTML
     *
     * @param FormInterface $form
     *
     * @return string
     */
    public function render(FormInterface $form)
    {
        return $this->twig->render('sunnyct-wp-forms.twig', ['form' => $form->createView()]);
    }
}