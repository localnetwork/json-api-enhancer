<?php
namespace Drupal\json_api_enhancers\EventSubscriber;


use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JSONAPIRequestEventsSubscriber implements EventSubscriberInterface {

  /**
   * @var \Drupal\Core\Config\Config
   */
  protected Config $jsonAPIConfig;

  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->jsonAPIConfig = $configFactory->get('jsonapi_extras.settings');
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => 'modifyRequestParameters'
    ];
  }

  public function modifyRequestParameters(RequestEvent $event) {

    $request = $event->getRequest();
    $path = $request->getPathInfo();
    $jsonAPIPathPrefix = "/" .$this->jsonAPIConfig->get('path_prefix');

    if ($this->checkIfJsonAPIRequest($path, $jsonAPIPathPrefix)) {
      $apiPath = str_replace($jsonAPIPathPrefix, '', $path);
      $explodePath = explode('/', $apiPath);
      $entity = isset($explodePath[1]) ? $explodePath[1] : '';
      $bundle = isset($explodePath[2]) ? $explodePath[2] : '';
      $include = $this->getJsonAPIIncludes($entity, $bundle);
      if (!empty($include)) {
        $request->query->set('jsonapi_include', 1);
        $request->query->set('include', $include);
      }
    }
  }

  /**
   * Determines if the current JSON API request
   *
   * @param string $path
   *   Current Request Path Info
   * @param string $jsonAPIPathPrefix
   *   JSON API path prefix
   *
   * @return bool
   */
  private function checkIfJsonAPIRequest(string $path, string $jsonAPIPathPrefix): bool {
    if (!empty($jsonAPIPathPrefix)) {
      $length = strlen($jsonAPIPathPrefix);
      if (substr($path, 0, $length ) === $jsonAPIPathPrefix) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * @param string $entity
   *  Entity Type ID
   * @param string $bundle
   *  Bundle Machine name
   *
   * @return string
   */
  private function getJsonAPIIncludes(string $entity, string $bundle): string {
    $includes = '';
    if (!empty($entity) && !empty($bundle)) {
      switch ($entity) {
        case 'node':
          if ($bundle == 'landing_page') {
            $includes = 'field_section_component,
              field_section_component.field_product_selection,
              field_section_component.field_cta,
              field_section_component.field_featured_section,
              field_section_component.field_display_cards,
              field_section_component.field_toaster,
              field_section_component.field_tabs,
              field_section_component.field_tabs.field_tab_contents,
              field_section_component.field_tabs.field_tab_contents.field_product_selection,
              field_section_component.field_tabs.field_tab_contents.field_featured_section,
              field_section_component.field_tabs.field_tab_contents.field_display_cards,
              field_section_component.field_tabs.field_tab_contents.field_toaster,
              field_section_component.field_tabs.field_tab_contents.field_cta,
              field_section_component.field_tabs.field_tab_contents.field_display_cards.field_cta,
              field_section_component.field_tabs.field_tab_contents.field_toaster,
              field_section_component.field_cta.field_analytics.field_event_category,
              field_section_component.field_cta.field_analytics.field_event_action,
              field_section_component.field_cta.field_analytics.field_ui_element,
              field_section_component.field_cta.field_analytics.field_ui_section,
              field_section_component.field_desktop_image,
              field_section_component.field_mobile_image,
              field_section_component.field_analytics.field_event_category,
              field_section_component.field_analytics.field_event_action,
              field_section_component.field_analytics.field_ui_element,
              field_section_component.field_analytics.field_ui_section,
              field_section_component.field_tabs.field_tab_contents.field_featured_section.field_media_icon,
              field_analytics.field_event_category,
              field_analytics.field_event_action,
              field_analytics.field_ui_element,
              field_analytics.field_ui_section,
              field_section_component.field_display_cards.field_supporting_icon,
              field_section_component.field_tabs.field_tab_contents.field_display_cards.field_supporting_icon,
              field_associated_product.field_user_manual,
              field_associated_product.field_whats_in_box_product_image,
              field_messages,
              field_section_component.field_tabs.field_tab_contents.field_display_cards.field_cta.field_analytics.field_event_category,
              field_section_component.field_tabs.field_tab_contents.field_display_cards.field_cta.field_analytics.field_event_action,
              field_section_component.field_tabs.field_tab_contents.field_display_cards.field_cta.field_analytics.field_ui_element,
              field_section_component.field_tabs.field_tab_contents.field_display_cards.field_cta.field_analytics.field_ui_section,
              field_flow,
              field_section_component.field_tabs.field_tab_contents.field_desktop_banner_image,
              field_section_component.field_tabs.field_tab_contents.field_mobile_banner_image,
              field_section_component.field_tabs.field_tab_contents.field_gradient,
              field_google_analytics_events.field_event_category,
              field_google_analytics_events.field_event_action,
              field_page_header_style';
          }
          elseif ($bundle == 'quiz_result') {
            $includes = 'field_quiz,field_flow,field_messages,
            field_analytics.field_event_category,
            field_analytics.field_event_action,
            field_analytics.field_ui_element,
            field_analytics.field_ui_section,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action,
            field_page_header_style';
          }
          elseif ($bundle == 'quiz') {
            $includes = 'field_flow,field_messages,
            field_analytics.field_event_category,
            field_analytics.field_event_action,
            field_analytics.field_ui_element,
            field_analytics.field_ui_section,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action,
            field_page_header_style';
          }
          elseif ($bundle == 'article') {
            $includes = 'field_flow,field_tags,
              field_google_analytics_events.field_event_category,
              field_google_analytics_events.field_event_action,
              field_page_header_style';
          }
          elseif ($bundle == 'activation_page') {
            $includes = 'field_section_component,
            field_media_image,
            field_section_component.field_step,
            field_section_component.field_tabs,
            field_section_component.field_tabs.field_tab_contents,
            field_section_component.field_tab_contents,
            field_section_component.field_step.field_media_icon,
            field_section_component.field_step.field_mobile_icon,
            field_messages,
            field_flow,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action,
            field_page_header_style';
          }
          elseif ($bundle == 'activation_landing_page') {
            $includes = 'field_section_component,
            field_section_component.field_container,
            field_section_component.field_container.field_media_icon,
            field_messages,
            field_flow,field_analytics.field_event_category,
            field_analytics.field_event_action,
            field_analytics.field_ui_element,
            field_analytics.field_ui_section,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action,
            field_page_header_style';
          }
          elseif ($bundle == 'page') {
            $includes = 'field_section_component,
            field_analytics.field_event_category,
            field_analytics.field_event_action,
            field_analytics.field_ui_element,
            field_analytics.field_ui_section,
            field_section_component.field_page_section,
            field_section_component.field_page_section.field_cta,
            field_messages,
            field_section_component.field_display_rules,
            field_section_component.field_display_rules.field_plan_type,
            field_section_component.field_page_section.field_display_rules,
            field_section_component.field_page_section.field_tabs,
            field_section_component.field_page_section.field_tabs.field_tab_contents,
            field_section_component.field_tabs,
            field_section_component.field_tabs.field_page_section,
            field_section_component.field_tabs.field_page_section.field_cta,
            field_section_component.field_tabs.field_media_icon,
            field_flow,
            field_section_component.field_page_section.field_cta,
            field_section_component.field_page_section.field_desktop_banner_image,
            field_section_component.field_page_section.field_mobile_banner_image,
            field_section_component.field_page_section.field_cta.field_analytics.field_event_category,
            field_section_component.field_page_section.field_cta.field_analytics.field_event_action,
            field_section_component.field_page_section.field_cta.field_analytics.field_ui_element,
            field_section_component.field_page_section.field_cta.field_analytics.field_ui_section,
            field_section_component.field_page_section.field_cta.field_frontend_display,
            field_section_component.field_tabs.field_page_section.field_cta,
            field_section_component.field_tabs.field_page_section.field_cta.field_analytics.field_event_category,
            field_section_component.field_tabs.field_page_section.field_cta.field_analytics.field_event_action,
            field_section_component.field_tabs.field_page_section.field_cta.field_analytics.field_ui_element,
            field_section_component.field_tabs.field_page_section.field_cta.field_analytics.field_ui_section,
            field_section_component.field_tabs.field_page_section.field_cta.field_frontend_display,
            field_section_component.field_desktop_banner_image,
            field_section_component.field_mobile_banner_image,
            field_section_component.field_region,
            field_section_component.field_region.field_region_section,
            field_section_component.field_region.field_region_section.field_background,
            field_section_component.field_region.field_region_section.field_background.field_desktop_banner_image,
            field_section_component.field_region.field_region_section.field_background.field_mobile_banner_image,
            field_section_component.field_region.field_region_section.field_desktop_device_image,
            field_section_component.field_region.field_region_section.field_mobile_device_image,
            field_section_component.field_region.field_region_section.field_cta,
            field_section_component.field_region.field_region_section.field_cta.field_analytics.field_event_category,
            field_section_component.field_region.field_region_section.field_cta.field_analytics.field_event_action,
            field_section_component.field_region.field_region_section.field_cta.field_analytics.field_ui_element,
            field_section_component.field_region.field_region_section.field_cta.field_analytics.field_ui_section,
            field_section_component.field_region.field_region_section.field_cta.field_frontend_display,
            field_section_component.field_region.field_region_section.field_display_rules,
            field_section_component.field_background,
            field_section_component.field_background.field_desktop_banner_image,
            field_section_component.field_background.field_mobile_banner_image,
            field_section_component.field_cta,
            field_section_component.field_icon,
            field_section_component.field_supporting_icon,
            field_section_component.field_cta.field_analytics.field_event_category,
            field_section_component.field_cta.field_analytics.field_event_action,
            field_section_component.field_cta.field_analytics.field_ui_element,
            field_section_component.field_cta.field_analytics.field_ui_section,
            field_section_component.field_cta.field_frontend_display,
            field_section_component.field_page_section.field_animation_items,
            field_section_component.field_page_section.field_animation_items.field_desktop_banner_image,
            field_section_component.field_page_section.field_animation_items.field_mobile_banner_image,
            field_section_component.field_page_section.field_animation_items.field_icon,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action,
            field_section_component.field_column,
            field_section_component.field_page_sections,
            field_section_component.field_page_sections.field_desktop_banner_image,
            field_section_component.field_page_sections.field_mobile_banner_image,
            field_section_component.field_page_sections.field_display_rules,
            field_section_component.field_page_sections.field_page_section,
            field_section_component.field_column.field_column_section,
            field_section_component.field_column.field_column_section.field_desktop_banner_image,
            field_section_component.field_column.field_column_section.field_mobile_banner_image,
            field_section_component.field_column.field_column_section.field_display_rules,
            field_section_component.field_column.field_column_section.field_page_section,
            field_section_component.field_column.field_column_section.field_page_section.field_desktop_banner_image,
            field_section_component.field_column.field_column_section.field_page_section.field_mobile_banner_image,
            field_page_header_style';
          }
          elseif ($bundle == 'popup') {
            $includes = 'field_analytics.field_event_category,
            field_analytics.field_event_action,
            field_analytics.field_ui_element,
            field_analytics.field_ui_section,
            field_messages,
            field_section_component,
            field_section_component.field_page_section,
            field_section_component.field_background,
            field_section_component.field_cta,
            field_section_component.field_background.field_desktop_banner_image,
            field_section_component.field_background.field_mobile_banner_image,
            field_section_component.field_cta.field_analytics,
            field_section_component.field_cta.field_analytics.field_event_category,
            field_section_component.field_cta.field_analytics.field_event_action,
            field_section_component.field_cta.field_analytics.field_ui_element,
            field_section_component.field_cta.field_analytics.field_ui_section,
            field_section_component.field_cta.field_frontend_display,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action';
          }
          elseif ($bundle == 'renewal_landing_pages') {
            $includes = 'field_section_component,field_flow,
            field_section_component.field_desktop_banner_image,
            field_section_component.field_mobile_banner_image,
            field_section_component.field_plan_style_item,
            field_section_component.field_display_cards.field_desktop_banner_image,
            field_section_component.field_display_cards.field_mobile_banner_image,
            field_section_component.field_page_section,
            field_section_component.field_gradient,
            field_section_component.field_anchor_tabs_v2,
            field_section_component.field_data_banners,
            field_section_component.field_banner_items.field_desktop_banner_image,
            field_section_component.field_banner_items.field_mobile_banner_image,
            field_section_component.field_banner_items.field_cta,
            field_section_component.field_banner_items.field_cta.field_analytics.field_event_category,
            field_section_component.field_banner_items.field_cta.field_analytics.field_event_action,
            field_section_component.field_banner_items.field_cta.field_analytics.field_ui_element,
            field_section_component.field_banner_items.field_cta.field_analytics.field_ui_section,
            field_section_component.field_banner_items.field_cta.field_frontend_display,
            field_section_component.field_banner_items.field_dynamic_form_field,
            field_section_component.field_plan_style_item.field_image,
            field_messages,
            field_section_component.field_display_rules,
            field_section_component.field_cta,
            field_section_component.field_cta.field_analytics.field_event_category,
            field_section_component.field_cta.field_analytics.field_event_action,
            field_section_component.field_cta.field_analytics.field_ui_element,
            field_section_component.field_cta.field_analytics.field_ui_section,
            field_section_component.field_cta.field_frontend_display,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action,
            field_section_component.field_steps,
            field_section_component.field_steps.field_media_icon,
            field_section_component.field_background,
            field_section_component.field_background.field_desktop_banner_image,
            field_section_component.field_background.field_mobile_banner_image,
            field_section_component.field_installation_steps,
            field_section_component.field_installation_steps.field_gpon_installation_card,
            field_section_component.field_installation_steps.field_gpon_installation_card.field_background,
            field_section_component.field_installation_steps.field_gpon_installation_card.field_background.field_desktop_banner_image,
            field_section_component.field_installation_steps.field_gpon_installation_card.field_background.field_mobile_banner_image,
            field_section_component.field_installation_steps.field_background,
            field_section_component.field_installation_steps.field_background.field_desktop_banner_image,
            field_section_component.field_installation_steps.field_background.field_mobile_banner_image,
            field_page_header_style';
          }
          elseif ($bundle == 'device_pdp') {
            $includes = 'field_analytics.field_event_category,
            field_analytics.field_event_action,
            field_analytics.field_ui_element,
            field_analytics.field_ui_section,
            field_messages,
            field_section_component,
            field_section_component.field_desktop_banner_image,
            field_section_component.field_mobile_banner_image,
            field_section_component.field_display_rules,
            field_section_component.field_page_section,
            field_section_component.field_content_tab,
            field_flow,
            field_page_header_style';
          }
          elseif ($bundle == 'terms_and_conditions') {
            $includes = 'field_analytics.field_event_category,
            field_analytics.field_event_action,
            field_analytics.field_ui_element,
            field_analytics.field_ui_section,
            field_section_component,
            field_section_component.field_page_section,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action';
          }
          elseif ($bundle == 'mobile_app_pages') {
            $includes = 'field_maintenance_page,
            field_maintenance_page.field_cta,
            field_maintenance_page.field_image,
            field_maintenance_page.field_display_rules,
            field_maintenance_page.field_display_rules.field_version_ios,
            field_maintenance_page.field_display_rules.field_version_android,
            field_mobile_section_component,
            field_mobile_section_component.field_mobile_app_brand,
            field_mobile_section_component.field_display_rules,
            field_mobile_section_component.field_display_rules.field_version_ios,
            field_mobile_section_component.field_display_rules.field_version_android,
            field_mobile_section_component.field_soft_banner_image,
            field_mobile_section_component.field_soft_banner_image.field_cta,
            field_mobile_section_component.field_soft_banner_image.field_image,
            field_mobile_section_component.field_buy_load_brand,
            field_mobile_section_component.field_buy_load_brand_category,
            field_mobile_section_component.field_cta,
            field_mobile_section_component.field_banner_identifier_taxonomy,
            field_mobile_section_component.field_category,

            field_mobile_section_component.field_general_app_banner,
            field_mobile_section_component.field_general_app_banner.field_image,
            field_mobile_section_component.field_general_app_banner.field_banner_identifier_taxonomy,
            field_mobile_section_component.field_general_app_banner.field_mobile_app_brand,
            field_mobile_section_component.field_general_app_banner.field_mobile_app_segment,
            field_mobile_section_component.field_general_app_banner.field_general_cta,
            field_mobile_section_component.field_general_app_banner.field_general_cta.field_general_cta_identifier,
            field_mobile_section_component.field_general_app_banner.field_general_cta.field_general_cta_type,
            field_mobile_section_component.field_general_app_banner.field_display_rules,
            field_mobile_section_component.field_mobile_app_segment,
            field_mobile_section_component.field_banner_carousel_identifier,

            field_mobile_section_component.field_rewards_catalog_category,
            field_mobile_section_component.field_rewards_catalog_category.field_category,
            field_mobile_section_component.field_rewards_catalog_category.field_image,
            field_mobile_section_component.field_rewards_catalog_category.field_display_rules,

            field_mobile_section_component.field_general_cta,
            field_mobile_section_component.field_general_cta.field_general_cta_identifier,
            field_mobile_section_component.field_general_cta.field_general_cta_type,
            field_mobile_section_component.field_discovery_static_image,
            field_mobile_section_component.field_discovery_static_image.field_cta,
            field_mobile_section_component.field_discovery_static_image.field_image,
            field_mobile_section_component.field_discovery_banner_image,
            field_mobile_section_component.field_discovery_banner_image.field_icon_cta,
            field_mobile_section_component.field_discovery_banner_image.field_icon_cta.field_image,
            field_mobile_section_component.field_discovery_banner_image.field_image,
            field_mobile_section_component.field_icon_cta,
            field_mobile_section_component.field_icon_cta.field_image,
            field_mobile_section_component.field_icon_cta.field_icon_sort_value,
            field_mobile_section_component.field_quicklink_cta.field_image,
            field_mobile_section_component.field_quicklink_cta.field_brand,
            field_mobile_section_component.field_quicklink_cta.field_quicklink_sort_value,
            field_mobile_section_component.field_quicklink_cta.field_general_cta,
            field_mobile_section_component.field_quicklink_cta.field_general_cta.field_general_cta_type,
            field_mobile_section_component.field_quicklink_cta.field_general_cta.field_general_cta_identifier,
            field_mobile_section_component.field_maintenance_page,
            field_mobile_section_component.field_maintenance_page.field_cta,
            field_mobile_section_component.field_maintenance_page.field_display_rules,
            field_mobile_section_component.field_maintenance_page.field_display_rules.field_version_ios,
            field_mobile_section_component.field_maintenance_page.field_display_rules.field_version_android,
            field_mobile_section_component.field_maintenance_page.field_image,
            field_mobile_section_component.field_rewards_banner_image.field_icon_cta.field_image,
            field_mobile_section_component.field_rewards_banner_image.field_image,
            field_mobile_section_component.field_rewards_banner_image.field_faq_brand,
            field_mobile_section_component.field_account_banner_image,
            field_mobile_section_component.field_account_banner_image.field_faq_brand,
            field_mobile_section_component.field_account_banner_image.field_mobile_app_applied_brand,
            field_mobile_section_component.field_account_banner_image.field_mobile_app_brand,
            field_mobile_section_component.field_account_banner_image.field_campaign_flow,
            field_mobile_section_component.field_account_banner_image.field_cta,
            field_mobile_section_component.field_account_banner_image.field_image,
            field_mobile_section_component.field_article_mobile_section,
            field_mobile_section_component.field_article_mobile_section.field_media_image,
            field_mobile_section_component.field_article_mobile_section.field_mobile_app_article_type,
            field_mobile_section_component.field_article_mobile_section.field_thumbnail_image,
            field_mobile_section_component.field_image,
            field_mobile_section_component.field_soft_banner_image.field_mobile_app_brand';
          }
          elseif ($bundle == 'article_mobile_pages'){
            $includes = 'field_media_image,
                         field_thumbnail_image,
                         field_mobile_app_article_type';
          }
          elseif ($bundle == 'gallery') {
            $includes = 'field_section_component,
            field_section_component.field_region,
            field_section_component.field_region.field_region_section,
            field_section_component.field_region.field_region_section.field_background,
            field_section_component.field_region.field_region_section.field_background.field_desktop_banner_image,
            field_section_component.field_region.field_region_section.field_background.field_mobile_banner_image,
            field_section_component.field_region.field_region_section.field_desktop_device_image,
            field_section_component.field_region.field_region_section.field_mobile_device_image,
            field_section_component.field_region.field_region_section.field_cta,
            field_section_component.field_region.field_region_section.field_cta.field_analytics.field_event_category,
            field_section_component.field_region.field_region_section.field_cta.field_analytics.field_event_action,
            field_section_component.field_region.field_region_section.field_cta.field_analytics.field_ui_element,
            field_section_component.field_region.field_region_section.field_cta.field_analytics.field_ui_section,
            field_section_component.field_region.field_region_section.field_cta.field_frontend_display,
            field_section_component.field_region.field_region_section.field_display_rules,
            field_flow,
            field_messages,
            field_analytics.field_event_category,
            field_analytics.field_event_action,
            field_analytics.field_ui_element,
            field_analytics.field_ui_section,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action,
            field_filterby_values,
            field_filterby_values.device_brand,
            field_filterby_values.featured_products,
            field_filterby_values.plan_value,
            field_filterby_values.productType,
            field_page_header_style';
          }
          elseif ($bundle == 'maintenance') {
            $includes = 'field_section_component,
            field_section_component.field_desktop_banner_image,
            field_section_component.field_gradient,
            field_section_component.field_mobile_banner_image,
            field_messages,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action,
            field_page_header_style';
          }
          elseif ($bundle == 'track_order') {
            $includes = 'field_section_component,
            field_analytics.field_event_category,
            field_analytics.field_event_action,
            field_analytics.field_ui_element,
            field_analytics.field_ui_section,
            field_messages,
            field_flow,
            field_google_analytics_events.field_event_category,
            field_google_analytics_events.field_event_action,
            field_section_component.field_column,
            field_section_component.field_column.field_column_section,
            field_section_component.field_column.field_column_section.field_desktop_banner_image,
            field_section_component.field_column.field_column_section.field_mobile_banner_image,
            field_section_component.field_column.field_column_section.field_display_rules,
            field_section_component.field_column.field_column_section.field_page_section,
            field_section_component.field_page_section,
            field_page_header_style';
          }
          break;
        case 'product':
          if ($bundle == 'promo') {
            $includes = 'field_social_media,field_boosters,field_brands,field_messages';
          }
          elseif ($bundle == 'hpw') {
            $includes = 'field_brand,field_user_manual,field_whats_in_box_product_image';
          }
          break;
        case 'component':
          if ($bundle == 'faq') {
            $includes = 'field_brand';
          }
          break;
        case 'app_component':
          if ($bundle == 'soft_banner_carousel') {
            $includes = 'field_soft_banner_image.field_image,field_soft_banner_image.field_cta,
            field_soft_banner_image.field_mobile_app_brand';
          }
          elseif ($bundle == 'maintenance') {
            $includes = 'field_cta,field_image,field_display_rules';
          }
          if ($bundle == 'buy_load_banner') {
            $includes = 'field_image_file,field_buy_load_brand_category';
          }
          if ($bundle == 'faq') {
            $includes = 'field_faq_brand';
          }
          if ($bundle == 'discovery_banner_image') {
            $includes = 'field_image,field_icon_cta.field_image';
          }
          if ($bundle == 'discovery_static_banner') {
            $includes = 'field_discovery_static_image.field_cta,field_discovery_static_image.field_image,field_display_rules';
          }
          if ($bundle == 'account_banner_carousel') {
          $includes = 'field_account_banner_image.field_image,field_account_banner_image.field_cta,field_account_banner_image.field_faq_brand,field_display_rules';
          }
          if ($bundle == 'discover_main_banner_carousel') {
            $includes = 'field_discovery_banner_image.field_icon_cta.field_image,
                         field_discovery_banner_image.field_image,
                         field_display_rules';
          }
          if ($bundle == 'social_media_list') {
            $includes = 'field_icon_cta.field_image,field_display_rules';
          }
          if ($bundle == 'rewards_banner_carousel') {
            $includes = 'field_rewards_banner_image.field_icon_cta.field_image,
                         field_rewards_banner_image.field_image,
                         field_rewards_banner_image.field_faq_brand,
                         field_display_rules.field_version';
          }
          if ($bundle == 'page_tab_field') {
            $includes = 'field_maintenance_page,field_maintenance_page.field_cta,
            field_maintenance_page.field_image,
            field_maintenance_page.field_display_rules,
            field_maintenance_page.field_display_rules.field_version_ios,
            field_maintenance_page.field_display_rules.field_version_android';
          }
          if ($bundle == 'floating_icon') {
            $includes = 'field_image,field_display_rules,
            field_display_rules.field_version_ios,
            field_display_rules.field_version_android';
          }
          break;
      }
    }
    return $includes;
  }
}
