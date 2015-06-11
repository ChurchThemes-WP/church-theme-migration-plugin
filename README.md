ChurchThemes.net Migration Plugin
=============================

This plugin helps migrate ChurchThemes.net post types, taxonomies, and metadata to the [Church Theme Content plugin](https://wordpress.org/plugins/church-theme-content/) to allow for switching to another theme like [Uplifted](https://upthemes.com/themes/uplifted/).

So far, the migration tool migrates the following data automagically:

1. Sermons
1. Sermon Metadata (audio, embed, video, document metadata)
1. Sermon Taxonomies: Topics, Speakers, Series
1. People
1. People Group Taxonomy - (currently migrates `person_category` to `ctc_person_group` and drops the `person_tag` taxonomy)
1. Locations

Things that are not migrated:

1. Homepage Slides - Users must currently export and re-create their homepage sliders within themes, as there is no standard for how themes handle slides.
1. Widgets - It is also difficult to migrate widgets from zone-to-zone. Widgets will simply be made inactive and must be assigned to new widget areas once you enable your new theme.

## Usage Instructions

1. Install [this plugin](https://github.com/UpThemes/church-theme-migration-plugin) as well as the [Church Theme Content plugin](https://wordpress.org/plugins/church-theme-content/).
1. Navigate to Tools > ChurchThemes Migration.
1. Click all the blue buttons first. 
1. If needed, click the grey buttons.
1. If the process timed out, try clicking the blue button again. (some shared servers are underpowered and may exceed the maximum execution time, so you may have to click the buttons again to migrate everything, but have no fear, you won't lose any data if you have to do it again.)

## Content Migration Instructions

1. Create a new page called ‘Home’ (and select the ‘Homepage' page template) and one called ‘Blog’ (select the ‘Blog’ page template).
1. Edit the contents of the 'Home' page, as this will appear as your welcome message on your homepage.
1. Go to 'Settings > Reading’ and set the front page as ‘Home' and posts page as ‘Blog’ like this: ![Image](https://www.evernote.com/shard/s3/sh/9b0f40d9-a0db-4095-9d74-3bb6baee03d1/4fd721f5cbd049e4bcd9f271895eaf8a/deep/0/Reading-Settings---Trinity---WordPress.png)
1. Set up all your widget areas from either the ‘Appearance > Widgets’ area or using the Customizer.
1. Go to “Appearance > Menu,” select your main menu and click ‘Select’ then scroll down and check the box to add it to the “Right Top Menu.”
1. Keep in mind you will have to manually migrate content such as slides, events, widgets, and menus.
