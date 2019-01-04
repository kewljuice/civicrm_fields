## Usage

### Add a CiviCRM field to a content type

![Add a CiviCRM field to a content type](images/screenshot1.png?raw=true)

1. Navigate to the Content types page (Administer > Structure > Content types).
2. In the table, locate the row that contains your content type and click the manage fields link.
3. In the Add new field section, select one of the following types. 
    * CiviCRM field_contact
    * CiviCRM field_event
4. Enter a label, machine name for the field.
5. Click Save.

### Select CiviCRM entity on create/edit content type page

![Select CiviCRM entity on content type create](images/screenshot2.png?raw=true)

1. Navigate to the Content type create/edit page.
2. Select the newly created CiviCRM field, start typing the field will automatically show results.

### Manage CiviCRM field display

![Manage CiviCRM field display](images/screenshot3.png?raw=true)

1. Navigate to the Content types page (Administer > Structure > Content types).
2. In the table, locate the row that contains the content type you wish to change and click the manage display link. This takes you to the Default view mode settings page.
3. The format column contains the options to show the CiviCRM field in different formats.

### View content type with CiviCRM field formatter

![View content type with CiviCRM field](images/screenshot4.png?raw=true)

1. Navigate to a Content type page where a CiviCRM field is set.

## Installation
This module is installed as any other Drupal module.

- with drush
```drush pm-enable -y civicrm_fields```

## Development

### Links
- https://ixis.co.uk/blog/drupal-8-creating-field-types-multiple-values
- https://www.qed42.com/blog/autocomplete-drupal-8
- https://symfonycasts.com/screencast/drupal8-under-the-hood/event-arguments-request
- https://symfonycasts.com/screencast/drupal8-under-the-hood/get-service-from-container
