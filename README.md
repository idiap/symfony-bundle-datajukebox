# Data Jukebox Bundle

## Synopsis

The Data Jukebox Bundle is a PHP/Symfony bundle which aims to provide - for
common CRUD (Create-Read-Update-Delete) operations - the same level of abstraction
that Symfony does for forms.

By defining and associating so-called "Properties" to standard ORM (Object
Relation Mapper) "Entities", one can generate fully-featured list and detail
views for those entities, as easily as one generates forms.

The same "Properties" are used to generate Symfony forms even easier, by automating
the form building process.

Developpers can thus concentrate entirely on the data model and have all views
build automatically, with just a few lines in the corresponding controllers.
 

## Dependencies

- [MUST] Symfony 2.7 or later
- [MUST] PHP 5.3.9 or later


## Features

- new "DataJukebox" service
- new "Properties" class and corresponding entity annotation
- automated list/detail/form view generation
- fully-featured list/detail views, including:
    - browsing controls ("First", "Previous", "Next", "Last" buttons)
    - fields display (show/hide) controls
    - fields sorting controls (allowing multiple fields criteria)
    - fields filtering controls (using a rich expression language)
    - global search controls (using a rich expression language)
    - CRUD operations controls (view detail, update, delete links and buttons)
- versatile "Properties" control based on the action ('list', 'detail', 'insert', 'update', etc.) and authorization level (user-defined)
- fine-grained "Properties", including:
- localized fields labels and tooltips
- displayable fields (in the list/detail/form views)
- default-displayed fields (for ergonomic presentation of data)
- hidden fields (that must be fetched from the database but not displayed)
- required fields (that may not be hidden or must be data-filled in forms)
- read-only fields (that may not be modified in forms)
- fields default values (when creating new data in forms)
- fields links (for powerful data-driven hyperlinks)
- orderable fields (fields that may be used for data sorting)
- filterable fields (fields that may be used for field-based data filtering)
- searchable fields (fields that are used for global data filtering)
- additional form options (for further customizing forms)
- additional links (for powerful data-driven hyperlinks)


## Installation

### Routes configuration

[config/routes.yaml] Add "datajukebox" bloc at root:
datajukebox:
    resource: '@DataJukeboxBundle/Resources/config/routing.yml'
    prefix: '/Symfony'
