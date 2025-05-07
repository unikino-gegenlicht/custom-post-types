<?php
function emit_movie_schema(): void {
    $post = get_post();
    if ($post === null) {
        return;
    }

    if ($post->post_type !== "movie") {
        return;
    }


    $schemaData = [
        "@context" => "https://schema.org",
        "@type" => "Event",
        "name" => $post->post_title,
        "startDate" => "", // todo: implement reading
        "endDate" => "", // todo: implement reading
        "eventAttendanceMode" => "http://schema.org/OfflineEventAttendanceMode",
        "eventStatus" => "http://schema.org/EventScheduled",
        "image" => get_the_post_thumbnail_url($post, 'full'),
        "description" => "", // todo: implement generation
        "location" => [], // todo: implement reading from associated location
        "organizer" => [], // todo: always gegenlicht
        "performer" => [], // todo: selected team member/cooperation partner, else gegenlicht
        "offers" => [], // todo: ticket including price
        "workFeatured" => [
            "@type" => "Movie",
            "name" => $post->post_title,
            "image" => get_the_post_thumbnail_url($post, 'full'),
            "titleEIDR" => "", // only if movie licensed otherwise leave out
            "director" => "", // todo: read from input
            "dateCreated" => "", // todo: read from meta

        ]
    ];
}
?>