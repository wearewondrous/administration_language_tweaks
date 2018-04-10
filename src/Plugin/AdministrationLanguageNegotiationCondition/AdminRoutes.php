<?php

namespace Drupal\administration_language_tweaks\Plugin\AdministrationLanguageNegotiationCondition;

use Drupal\administration_language_negotiation\AdministrationLanguageNegotiationConditionBase;
use Drupal\administration_language_negotiation\AdministrationLanguageNegotiationConditionInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\language\ConfigurableLanguageManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Routing\Router;

/**
 * Class for the Blacklisted paths condition plugin.
 *
 * @AdministrationLanguageNegotiationCondition(
 *   id = "admin_routes",
 *   weight = -50,
 *   name = @Translation("Admin Routes"),
 *   description = @Translation("Returns particular language on admin routes.")
 * )
 */
class AdminRoutes extends AdministrationLanguageNegotiationConditionBase implements
  AdministrationLanguageNegotiationConditionInterface {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The configurable language manager.
   *
   * @var \Drupal\language\ConfigurableLanguageManager
   */
  protected $languageManager;

  /**
   * The router manager.
   *
   * @var \Drupal\Core\Routing\Router
   */
  protected $router;

  /**
   * Constructs a RequestPath condition plugin.
   *
   * @param \Drupal\Core\Path\AliasManagerInterface $alias_manager
   *   An alias manager to find the alias for the current system path.
   * @param \Drupal\Core\Path\PathMatcherInterface $path_matcher
   *   The path matcher service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   The current path.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
    RequestStack $request_stack,
    ConfigFactory $config_factory,
    array $configuration,
    $plugin_id,
    array $plugin_definition,
    ConfigurableLanguageManager $language_manager,
    Router $router
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->requestStack = $request_stack;
    $this->configFactory = $config_factory;
    $this->languageManager = $language_manager;
    $this->router = $router;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('request_stack'),
      $container->get('config.factory'),
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('language_manager'),
      $container->get('router.no_access_checks')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    $active = $this->configuration[$this->getPluginId()];

    return ($active && $this->isAdminRoute()) ? $this->block() : $this->pass();
  }

  /**
   * Checks if the current request is admin route.
   *
   * @return bool
   *   TRUE if the current request is admin route.
   */
  public function isAdminRoute() {
    $request = $this->requestStack->getCurrentRequest();
    $router = \Drupal::service('router.no_access_checks');

    if (($match = $router->matchRequest($request)) && isset($match['_route_object'])) {
      return (bool) $match['_route_object']->getOption('_admin_route');
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form[$this->getPluginId()] = [
      '#title' => $this->t('Enable'),
      '#type' => 'checkbox',
      '#default_value' => $this->configuration[$this->getPluginId()],
      '#description' => $this->t(
        'Detects if the current path is admin route.'
      ),
    ];

    return $form;
  }
}
