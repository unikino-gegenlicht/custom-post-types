<div align="center">
    <img src="https://gegenlicht.net/wortmarke/">
    <h1>Custom WordPress Post-Types and Taxonomies</h1>
    <p>🧩 custom post types for our website</p>
<img alt="License" src="https://img.shields.io/github/license/unikino-gegenlicht/custom-post-types?style=for-the-badge">
<img alt="Release" src="https://img.shields.io/github/v/release/unikino-gegenlicht/custom-post-types?sort=semver&display_name=tag&style=for-the-badge">
</div>

> [!WARNING]
> This WordPress plugin does not support automatic updates. Please check for 
> new releases regularly on this site


## Requirements
WordPress Version: 7.0+  
PHP Version: 8.4+  
Webserver: Apache2 (other Webservers are not supported officially)

### Required Plugins
The following plugins need to be installed before you install this plugin. 
Otherwise, the plugin will create a fatal error and cannot be activated
  - MetaBox

### Optional Plugins
The following plugins/extensions to plugins enhance the features and edibility
of the custom post types and help with SEO integration:
  - MetaBox Extensions:
    - MB Yoast SEO (FREE)
    - MB Revision (PAID)
    - MB Term Meta (PAID)
    - MB Conditional Logic (PAID)
    - MB Tabs (PAID)
  - Yoast SEO (Free/Premium)

## Features
### Post Types
This plugin introduces the following post types to your WordPress installation:
  - Movie
  - Event
  - Team Member
  - Cooperation Partner
  - Sponsors
  - Screening Locations

### Taxonomies
In addition, it creates the following taxonomies for the following post types 
which cannot be deactivated as the taxonomies are required for the meta boxes
in the post types.
  - semester
    - Movie
    - Event
  - special-program
    - Movie
    - Event
  - actor
    - Movie
  - director
    - Movie

### Anonymization
As we need to hide the titles and further identifying information on some
screenings, this plugin features an anonymization functionality depending
on the license tiers that have been created.

<details>
<summary>Default License Tiers</summary>

#### `Screening and Advertisement License`
This licensing tier is set to always display all information about the 
screening.

#### `Screening License only`
This licensing tier is set to hide identifying information about the movie,
but allows the display for logged-in users and users accessing the website
from configured networks.
This type of license is often times used with pool licenses issued by
companies like the MPLC.

#### `Sneak License`
This licensing tier is set to always hide information about the screening og a
movie. This includes texts as well as director and actor names, countries of
origin, release years, as well as any identifying images.

</details>




### Schema.org
The plugin offers the generation of data compliant with the schema.org data.
The data can either be used by themes directly or can be integrated into
YoastSEO (if available).