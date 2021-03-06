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

namespace PE\WP\Forms;

use PE\WP\Forms\Admin;
use PE\WP\Forms\Model\FormModel;
use PE\WP\Forms\Shortcode\ActionShortcode;
use PE\WP\Forms\Shortcode\ButtonShortcode;
use PE\WP\Forms\Shortcode\CheckboxShortcode;
use PE\WP\Forms\Shortcode\ChoiceShortcode;
use PE\WP\Forms\Shortcode\FieldSetShortcode;
use PE\WP\Forms\Shortcode\FileShortcode;
use PE\WP\Forms\Shortcode\FormShortcode;
use PE\WP\Forms\Shortcode\ReCaptchaShortcode;
use PE\WP\Forms\Shortcode\TextareaShortcode;
use PE\WP\Forms\Shortcode\TextShortcode;
use PE\WP\Forms\Shortcode\ValidateShortcode;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
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
            FormRenderer::class => function() {
                return new FormRenderer(new TwigRendererEngine(array('sunnyct-wp-forms-theme.twig'), $this->twig));
            },
        ]));

        // Add the FormExtension to Twig
        $this->twig->addExtension(new FormExtension());
        $this->twig->addExtension(new TranslationExtension($translator));

        $this->twig->addGlobal('WP_SITEURL', WP_SITEURL);

        $this->factory = new Factory($this->formFactory);

        new Admin\Admin();//TODO create custom admin, maybe
        new Admin\Settings();

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

        do_action('sunnyct-wp-forms-register', $this->factory);

        add_shortcode('sunnyct-wp-forms', function ($params) {
            $params = shortcode_atts(['id' => null], (array) $params);

            if (empty($params['id'])) {
                return false;
            }

            if ($formModel = Forms()->getFactory()->create($params['id'])) {
                return Forms()->render($formModel);
            };

            return false;
        });

        new FormShortcode();
        new TextareaShortcode();
        new ButtonShortcode();
        new TextShortcode();
        new ChoiceShortcode();
        new CheckboxShortcode();
        new ReCaptchaShortcode();
        new FileShortcode();
        new FieldSetShortcode();

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

        wp_register_script(
            SUNNYCT_WP_FORMS_PLUGIN_NAME . '-recaptcha',
            'https://www.google.com/recaptcha/api.js?onload=sunnyct_wp_form_recaptcha_update&render=explicit&hl=' . get_locale(),
            [SUNNYCT_WP_FORMS_PLUGIN_NAME],
            '2.0',
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
     * @param FormModel $formModel
     *
     * @return string
     */
    public function render(FormModel $formModel)
    {
        $view = $formModel->instance->createView();

        if ($formModel->theme) {
            /* @var $renderer FormRenderer */
            $renderer = $this->twig->getRuntime(TwigRenderer::class);
            $renderer->setTheme($view, $formModel->theme);
        }

        try {
            return $this->twig->render('sunnyct-wp-forms.twig', ['form' => $view]);
        } catch (\Twig_Error $exception) {
            return WP_DEBUG ? $exception->getMessage() : '';
        }
    }
}