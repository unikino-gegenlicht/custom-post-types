<?php
function ggl_post_type_location(): void
{
    register_post_type(
        'location',
        [
            'label' => __('Locations', 'ggl-post-types'),
            'labels' => [
                'menu_name' => __('Locations', 'ggl-post-types'),
                'name_admin_bar' => __('Location', 'ggl-post-types'),
                'singular_name' => __('Location', 'ggl-post-types'),
                'add_new_item' => __('Add Location', 'ggl-post-types'),
                'add_new' => __('Add Location', 'ggl-post-types'),
                'edit_item' => __('Edit Location', 'ggl-post-types'),
                'view_item' => __('Show Location', 'ggl-post-types'),
                'search_items' => __('Search Locations', 'ggl-post-types'),
                'not_found' => __('No Locations found', 'ggl-post-types'),
                'not_found_in_trash' => __('No Locations found in Trash', 'ggl-post-types'),
                'all_items' => __('All Locations', 'ggl-post-types'),
                'archives' => __('Old Locations', 'ggl-post-types'),
            ],
            'public' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'can_export' => true,
            'show_ui' => true, // implies show_in_menu, show_in_nav_menus, show_in_admin_bar
            'show_in_rest' => true,
            'delete_with_user' => false,
            'menu_position' => 6,
            'menu_icon' => 'dashicons-location',
            'supports' => ['title'],
            'rewrite' => false
        ]
    );
}

function location_metaboxes($meta_boxes): array
{
    $prefix = 'location_';

    $meta_boxes[] = [
        'title' => __('Address', 'ggl-post-types'),
        'id' => 'address',
        'context' => 'before_permalink',
        'style' => 'seamless',
        'post_types' => ['location'],
        'autosave' => true,
        'fields' => [
            [
                'type' => 'heading',
                'name' => esc_html__('Address', 'ggl-post-types'),
            ],
            [
                'id' => $prefix . 'street',
                'type' => 'text',
                'name' => esc_html__('Street', 'ggl-post-types'),
                'desc' => esc_html__('Street Name including House Number', 'ggl-post-types'),
                'required' => true,
            ],
            [
                'id' => $prefix . 'city',
                'type' => 'text',
                'name' => esc_html__('City', 'ggl-post-types'),
                'required' => true,
            ],
            [
                'id' => $prefix . 'postal_code',
                'type' => 'text',
                'name' => esc_html__('Postal Code', 'ggl-post-types'),
                'required' => true,
            ],
            [
                'id' => $prefix . 'country',
                'type' => 'select_advanced',
                'name' => __('Country', 'ggl-post-types'),
                'options' => generate_country_mapping(),
                'multiple' => false,
                'required' => true,
            ],
            [
                'id' => $prefix . 'longitude',
                'type' => 'number',
                'name' => esc_html__('Longitude', 'ggl-post-types'),
                'required' => true,
            ],
            [
                'id' => $prefix . 'latitude',
                'type' => 'number',
                'name' => esc_html__('Latitude', 'ggl-post-types'),
                'required' => true,
            ],
        ]
    ];

    return $meta_boxes;
}