<?php
/*
 * Copyright (c) 2024. Philip Kaufmann, Jan Eike Suchard, Benjamin Witte
 * This file is copyright under the latest version of the EUPL.
 * Please see the LICENSE file for your rights.
 */

function ggl_taxonomy_program_type(): void
{
    register_taxonomy(
        'special-program',
        null,
        [
            'label' => __('Special Programs', 'ggl-post-types'),
            'description' => 'This is the special programs category, if you have a movie which is falls into a special program, create it here first and then assign it to the movie',
            'labels' => [
                'name' => __('Special Programs', 'ggl-post-types'),
                'singular_name' => __('Special Program', 'ggl-post-types'),
                'search_items' => __('Search Special Programs', 'ggl-post-types'),
                'all_items' => __('All Special Programs', 'ggl-post-types'),
                'edit_item' => __('Edit Special Program', 'ggl-post-types'),
                'update_item' => __('Update Special Program', 'ggl-post-types'),
                'add_new_item' => __('Add New Special Program', 'ggl-post-types'),
                'new_item_name' => __('New Special Program Name', 'ggl-post-types'),
                'menu_name' => __('Special Programs', 'ggl-post-types'),
            ],
            'show_ui' => true,
            'public' => true,
            'show_tagcloud' => false,
            'hierarchical' => false,
            'meta_box_cb' => false,
            'query_var' => true,
            'rewrite' => [
                'slug' => 'special-program',
                'hierarchical' => false,
                'with_front' => false,
            ],
        ]
    );
}

function ggl_taxonomy_program_type_meta_boxes($meta_boxes): mixed
{
    $prefix = 'special-program_';

    $meta_boxes[] = [
        'title' => esc_html__('Extended Configuration', 'ggl-post-types'),
        'id' => 'extended-configuration',
        'taxonomies' => 'special-program',
        'context' => 'normal',
        'fields' => [
            [
                'type' => 'color',
                'name' => esc_html__('Banner Color', 'ggl-post-types'),
                'id' => $prefix . 'banner_color',
                'desc' => esc_html__('The color used as the background for the special programme banner', 'ggl-post-types'),
            ],
            [
                'type' => 'color',
                'name' => esc_html__('Text Color', 'ggl-post-types'),
                'id' => $prefix . 'text_color',
                'desc' => esc_html__('The color used for the text on the special programme banner', 'ggl-post-types'),
            ],
            [
                'type' => 'image_advanced',
                'name' => __('Program Icon', 'ggl-post-types'),
                'id' => $prefix . 'image',
                'force_delete' => false,
                'max_file_uploads' => 1,
            ],
        ],
    ];

    return $meta_boxes;
}