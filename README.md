ChurchThemes.net Migration Plugin
=============================

This plugin helps migrate ChurchThemes.net content to the Church Theme Content plugin. There are lots of considerations to be made when migrating because it is not an apples-to-apples data comparison. We are mapping some taxonomies over but not others.

So far, the parts of the migration tool that work are:

1. Sermons
1. Sermon Taxonomies: Topics, Speakers, Series

Still needed:

1. Sermon Metadata (audio, embed, video, document metadata) * implemented but currently broken
1. People
1. People Group Taxonomy - (need to migrate `person_tags` to `ctc_person_group`)
1. Locations
1. Slides -> Slide Widget for Homepage (not sure how to best accomplish this or if it is worth spending time on)

## Usage Instructions

1. Install the plugin.
1. Navigate to Tools > ChurchThemes Migration.
1. Click the "Migrate Sermons" button.
1. If successful, click the "Migrate Sermon Taxonomies" button.
