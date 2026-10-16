<?php
/*
 * Copyright (c) 2026. Unikino GEGENLICHT. Licensed under the EUPL v1.2 or later
 */

namespace CinemaPress\post_types;

use CinemaPress\interfaces\Post_Type;
use function CinemaPress\cp_county_mapping;

class Screening_Location implements Post_Type
{

    const string SLUG = 'screening_location';

    /**
     * @inheritDoc
     */
    public static function register_data(): array
    {
        return array(
            'name' => __('Screening Location', 'cinema-press'),
            'labels' => array(
                'name' => __('Screening Locations', 'cinema-press'),
                'singular_name' => __('Screening Location', 'cinema-press'),
                'add_new' => __('Add Screening Location', 'cinema-press'),
                'add_new_item' => __('Add Screening Location', 'cinema-press'),
                'edit_item' => __('Edit Screening Location', 'cinema-press'),
                'new_item' => __('New Screening Location', 'cinema-press'),
                'view_item' => __('View Screening Location', 'cinema-press'),
                'view_items' => __('View Screening Locations', 'cinema-press'),
                'search_items' => __('Search Screening Locations', 'cinema-press'),
                'not_found' => __('No Screening Locations found', 'cinema-press'),
                'not_found_in_trash' => __('No Screening Locations found in Trash', 'cinema-press'),
                'all_items' => __('Screening Locations', 'cinema-press'),
                'archives' => __('Locations', 'cinema-press'),
                'attributes' => __('Location Attributes', 'cinema-press'),
                'insert_into_item' => __('Insert into screening location', 'cinema-press'),
                'uploaded_to_this_item' => __('Uploaded to this location', 'cinema-press'),
                'featured_image' => __('Featured Image', 'cinema-press'),
                'set_featured_image' => __('Set featured image', 'cinema-press'),
                'remove_featured_image' => __('Remove featured image', 'cinema-press'),
                'use_featured_image' => __('Use as featured image', 'cinema-press'),
                'filter_items_list' => __('Filter Screening Locations list', 'cinema-press'),
                'items_list_navigation' => __('Screening Locations list navigation', 'cinema-press'),
                'items_list' => __('Screening Locations list', 'cinema-press'),
                'item_published' => __('Screening Location published', 'cinema-press'),
                'item_published_privately' => __('Screening Location published privately', 'cinema-press'),
                'item_reverted_to_draft' => __('Screening Location reverted to draft', 'cinema-press'),
                'item_trashed' => __('Screening Location trashed', 'cinema-press'),
                'item_scheduled' => __('Screening Location scheduled', 'cinema-press'),
                'item_updated' => __('Screening Location updated', 'cinema-press'),
                'item_link' => __('Screening Location link', 'cinema-press'),
                'item_link_description' => __('Link to a screening location', 'cinema-press'),
            ),
            'description' => __('Screening Locations help with generating markup for the schema data and allow for easier directions to the screening locations', 'cinema-press'),
            'public' => true,
            'hierarchical' => false,
            'exclude_from_search' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'rest_namespace' => 'cinema-press',
            'supports' => array('title', 'thumbnail'),
            'has_archive' => 'locations',
            'rewrite' => array(
                'slug' => 'location',
                'with_front' => false,
                'feeds' => false,
                'pages' => false,
            ),
            'delete_with_user' => false,
            'show_in_menu' => 'options-general.php',
            'menu_icon' => 'dashicons-location',
        );
    }

    /**
     * @inheritDoc
     */
    public static function meta_boxes(array $meta_boxes): array
    {
        $meta_boxes[] = array(
            'title' => __('Screening Location Data', 'cinema-press'),
            'id' => 'cinema_press-location_address',
            'context' => 'after_title',
            'style' => 'seamless',
            'post_types' => array('screening_location'),
            'autosave' => true,
            'revision' => true,
            'fields' => array(
                [
                    'type' => 'heading',
                    'name' => esc_html__('Address', 'cinema-press'),
                ],
                [
                    'id' => "street",
                    'name' => esc_html__('Street', 'cinema-press'),
                    'type' => 'text',
                    'required' => true,
                    'revision' => true,
                ],
                [
                    'id' => 'postal_code',
                    'name' => esc_html__('Postal Code', 'cinema-press'),
                    'type' => 'text',
                    'required' => true,
                    'revision' => true,
                ],
                [
                    'id' => 'city',
                    'name' => esc_html__('City', 'cinema-press'),
                    'type' => 'text',
                    'required' => true,
                    'revision' => true,
                ],
                [
                    'id' => 'country',
                    'name' => esc_html__('Country', 'cinema-press'),
                    'type' => 'select_advanced',
                    'required' => true,
                    'revision' => true,
                    'options' => cp_county_mapping()
                ]
            )
        );

        $meta_boxes[] = array(
            'id' => 'cinema_press-location_coordinates',
            'title' => esc_html__('Coordinates', 'cinema-press'),
            'context' => 'after_title',
            'style' => 'seamless',
            'post_types' => array('screening_location'),
            'autosave' => true,
            'revision' => true,
            'fields' => array(
                [
                    'type' => 'heading',
                    'name' => esc_html__('Coordinates', 'cinema-press'),
                    'desc' => __("The coordinates are used to generate links to that location that are usable by almost any map service", "cinema-press"),
                ]
            )
        );

        $meta_boxes[] = array(
            'id' => 'cinema_press-location_identifiers',
            'title' => esc_html__('Location Identifiers', 'cinema-press'),
            'context' => 'after_title',
            'style' => 'seamless',
            'post_types' => array('screening_location'),
            'autosave' => true,
            'revision' => true,
            'fields' => array(
                [
                    'type' => 'heading',
                    'name' => esc_html__('Place Identifiers', 'cinema-press'),
                    'desc' => __("The identifiers are used to build platform dependent links to map services which will always pinpoint this location if possible", "cinema-press"),
                ],
                [
                    'id' => 'apple_place_id',
                    'name' => esc_html__('Apple Place ID', 'cinema-press'),
                    'desc' => '<a target="_blank" href="https://developer.apple.com/maps/place-id-lookup/">' . esc_html__("Lookup Place ID for Apple Maps", "cinema-press") . '</a>',
                    'type' => 'text',
                    'revision' => true
                ],
                [
                    'id' => 'google_place_id',
                    'name' => esc_html__('Google Place ID', 'cinema-press'),
                    'desc' => '<a target="_blank" href="https://developers.google.com/maps/documentation/places/web-service/place-id#find-id">' . esc_html__("Lookup Place ID for Google Maps", "cinema-press") . '</a>',
                    'type' => 'text',
                    'revision' => true
                ],
                [
                    'id' => 'osm_node_id',
                    'name' => esc_html__('OpenStreetMap Node ID', 'cinema-press'),
                    'desc' => __('The node id for the screening location, if available', 'cinema-press'),
                    'type' => 'text',
                    'revision' => true,
                ],
                [
                    'id' => "w3w",
                    'name' => esc_html__("What3Word Address", 'cinema-press'),
                    'desc' => __('Three words that describe the entrance to the screening location', 'cinema-press'),
                    'type' => 'text',
                    'revision' => true,
                ]
            )
        );

        return $meta_boxes;
    }
}