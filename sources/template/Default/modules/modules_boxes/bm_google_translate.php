<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT

 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class bm_google_translate {
    public string $code;
    public string $group;
    public $title;
    public $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;
    public $pages;

    public function __construct() {

      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_boxes_google_translate_title');
      $this->description = CLICSHOPPING::getDef('module_boxes_google_translate_description');

      if (\defined('MODULE_BOXES_GOOGLE_TRANSLATE_STATUS')) {
        $this->sort_order = MODULE_BOXES_GOOGLE_TRANSLATE_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_GOOGLE_TRANSLATE_STATUS == 'True');
        $this->pages = MODULE_BOXES_GOOGLE_TRANSLATE_DISPLAY_PAGES;
        $this->group = ((MODULE_BOXES_GOOGLE_TRANSLATE_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');

      $footer_tag = '<script defer>';
      $footer_tag .= 'function googleTranslateElementInit() {';
      $footer_tag .= 'new google.translate.TranslateElement({pageLanguage: \'en\'}, \'google_translate_element\');';
      $footer_tag .= '}';
      $footer_tag .= '</script>' . "\n";
      $footer_tag .= '<script defer src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>';

      $CLICSHOPPING_Template->addBlock($footer_tag, 'footer_scripts');

      $data = '<!-- header_google_translate  start -->' . "\n";

      ob_start();
      require($CLICSHOPPING_Template->getTemplateModules('/modules_boxes/content/google_translate'));
      $data .= ob_get_clean();
      $data .='<!-- bm_google_translate end -->' . "\n";

      $CLICSHOPPING_Template->addBlock($data, $this->group);
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return \defined('MODULE_BOXES_GOOGLE_TRANSLATE_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BOXES_GOOGLE_TRANSLATE_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please choose where the boxe must be displayed',
          'configuration_key' => 'MODULE_BOXES_GOOGLE_TRANSLATE_CONTENT_PLACEMENT',
          'configuration_value' => 'Right Column',
          'configuration_description' => 'Choose where the boxe must be displayed',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Left Column\', \'Right Column\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the banner group for the image',
          'configuration_key' => 'MODULE_BOXES_GOOGLE_TRANSLATE_BANNER_GROUP',
          'configuration_value' => SITE_THEMA.'_boxe_google_translate',
          'configuration_description' => 'Indicate the banner group<br /><br /><strong>Note :</strong><br /><i>The group must be created or selected whtn you create a banner in Marketing / banner</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BOXES_GOOGLE_TRANSLATE_SORT_ORDER',
          'configuration_value' => '500',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate where boxing should be displayed',
          'configuration_key' => 'MODULE_BOXES_GOOGLE_TRANSLATE_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Sélectionnez les pages où la boxe doit être présente',
          'configuration_group_id' => '6',
          'sort_order' => '7',
          'set_function' => 'clic_cfg_set_select_pages_list',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return ['MODULE_BOXES_GOOGLE_TRANSLATE_STATUS',
            'MODULE_BOXES_GOOGLE_TRANSLATE_CONTENT_PLACEMENT',
            'MODULE_BOXES_GOOGLE_TRANSLATE_BANNER_GROUP',
            'MODULE_BOXES_GOOGLE_TRANSLATE_SORT_ORDER',
            'MODULE_BOXES_GOOGLE_TRANSLATE_DISPLAY_PAGES'
           ];
    }
  } 