<?php

function add_admin_menu_separator(int $atPosition)
{
  global $menu;

  $seperator = ['', 'read', '', '', 'wp-menu-separator'];

  // check if a entry already exists at this position
  $menuIdxUsed = isset($menu[$atPosition]);

  if (!$menuIdxUsed) {
    $menu[$atPosition] = $seperator;
    return;
  }

  // as the menu position is already used, save the menu entry
  $previousMenuEntry = $menu[$atPosition];

  // now calculate the offset for splitting the menu
  $offset = 0;
  for ($i = 0; $i < $atPosition; $i++) {
    if (isset($menu[$i])) {
      $offset++;
    }
  }

  $beforeSeperator = array_slice($menu,0, $offset, true);
  $behindSeperator = array_slice($menu, $offset, null, true);
  
  $menu = $beforeSeperator + $behindSeperator;
  $menu[$atPosition] = $seperator;
  $menu[$atPosition+1] = $previousMenuEntry;
  
}

function refresh_permalinks() {
  flush_rewrite_rules();
}

function unregister_taxonomies() {
  unregister_taxonomy('semester');
  unregister_taxonomy('special-program');
}

function ggl_post_types_load_textdomain() {
  load_plugin_textdomain('ggl-post-types', false, dirname( plugin_basename(__FILE__) ) .'/languages');
}

function reorder_menu() {
  remove_menu_page( 'edit.php' ); // Posts
  remove_menu_page( 'edit-comments.php' ); // Comments 
  global $menu;

  $menu[5] = $menu[6];
  $menu[6] = $menu[7];
  $menu[7] = $menu[11];
  unset($menu[11]);

  add_admin_menu_separator(10);
}

function generate_language_mapping(): array {
	$output = array();
	foreach (GGL_LANGUAGES as $languageKey => $language) {
		//global $output;
		$translatedLanguage = __($language, 'ggl-post-types');
		$output[$languageKey] = $translatedLanguage;
	}
	return $output;
}

function generate_country_mapping(): array {
  $output = array();
  foreach (GGL_COUNTRIES as $code => $name) {
    $translatedName = __($name, 'ggl-post-types');
    $output[$code] = $translatedName;
  }
  return $output;
}