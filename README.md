# picview

An image gallery.

## Features

* Automatic thumbnail creation
* Thumbnail rotation + regeneration
* Multiple galleries
* Picture lightbox
* Directory navigation
* Customizable theme

## Installation

1. `git clone <this repository> --recursive`
2. `yarn install`

### Upgrading from the old version

1. `git pull <this repository>`
2. `yarn install`
3. `cd style/d120`
4. `git sumbodule update --init`

## Configuration

The entire instance of picview is configured by some global variables in
a `config.php` file in the root directory. To set one of the following options,
simply create a variable with that name in the configuration file.

### Comments path (`$comments_path`)

* *Type*: `string`
* *Required*: `true`
* *Options*: Any existing and writable path on the system
* *Description*: The path where comment data is stored.

### Copyright filename (`$copyright_file`)

* *Type*: `string`
* *Required*: `true`
* *Example*: `copyright.txt`
* *Description*: Naming convention for a file from which the text is rendered on
  every image in that folder when it exists.

### Overlay font file (`$font_file`)

* *Type*: `string`
* *Required*: `true`
* *Example*: `/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf`
* *Description*: A TTF-File that can be used to render unicode code points on
  the image. Used whenever text is written into the images.

### Medium image size (`$medium_size`)

* *Type*: `int`
* *Required*: `true`
* *Description*: The intended length of the shorter edge of the preview images.

### LDAP Host (`$ldapHost`)

* *Type*: `string`
* *Required*: Only if there is a gallery with the `ldap` authentication method.
* *Description*: Any valid URL of an LDAP server host.
  See [ldap_connect](https://secure.php.net/manual/en/function.ldap-connect.php).

### Lightbox Template File (`$lightbox_file`)

* *Type*: `string`
* *Required*: `true`
* *Options*: Any existing file absolute or relative to the `index.php` script.
* *Description*: The template for the picture comment/lightbox action.

### Template File (`$template_file`)

* *Type*: `string`
* *Required*: `true`
* *Options*: Any existing file absolute or relative to the `index.php` script.
* *Description*: The template for the directory view action.

### Thumbnail Path (`$thumbs_path`)

* *Type*: `string`
* *Required*: `true`
* *Options*: Any existing and writable path on the system
* *Description*: The path where preview images are stored / cached.

### Thumbnails per page (`$thumbs_per_page`)

* *Type*: `int`
* *Required*: `true`
* *Description*: The amount of thumbnails to show on a single page for the current folder

### Thumbnail size (`$thumb_size`)

* *Type*: `int`
* *Required*: `true`
* *Description*: The intended length of the shorter edge of the thumbnails.

## Galleries

PicView allows for multiple galleries on a single instance. Each gallery is
assigned a separate `galleryId`, which corresponds to the first element in the
`PATH_INFO` for any PicView URL.

To configure the galleries, add an array named `$galleries` to `config.php`.
Within that array, add a configuration array for each gallery. The key used for
this inner array is the `galleryId`. The following subsections will explain the
different configuration options that are available.

### Title (`title`)

* *Type*: `string`
* *Required*: `false`
* *Default*: The gallery id
* *Description*: Sets a title for this gallery. Used in breadcrumbs.

### Authentication Method (`auth_required`)

* *Type*: `string`
* *Required*: `true`
* *Options*: `'ldap'`, `'password'` or `''`
* *Description*: The method used to restrict access to certain people.
  The LDAP method will allow any user from the configured LDAP,
  the password method will allow any username and password configured for this gallery (`password`).
  The default method is to allow any user agent to access the pictures.

### Passwords (`password`)

* *Type*: `Array(string => string)`
* *Required*: Only if using authentication method `password`
* *Description*: Array where the keys are valid users and the values are valid passwords

### Pictures Path (`pictures_path`)

* *Type*: `string`
* *Required*: `true`
* *Description*: The (absolute) path to the (read-only) images folder of this gallery

## Actions

PicView provides its interface by a set of actions which may be applied to
any given path within the gallery. The available options are:

* `t`: Deliver a thumbnail of the image (`thumb_size`)
* `m`: Deliver a downscaled version of the image (`medium_size`)
* `i`: Deliver the original image
* `c`: Deliver a HTML lightbox with comments
* `s`: Process a comment POST request
* `r`: Rotate the thumbnail and downscaled preview images clockwise
* `d`: Delete and thus regenerate the preview images
* `p`: Show a gallery of the images within the directory
* `j`: Deliver a JSON representation of the current resource
* `h`: Deliver a HAL+JSON representation of the current resource

The URLs for these actions always have the form `/picview/index.php/{gallery}/{action}/{resource}`

## Templating

Template files are plain HTML files which may contain tags that will be replaced
by dynamic content. Tags correspond to entries in the context array passed to
`make_page` or `make_lightbox`.

### Page Template

The general page template is used for the `p` action to display the pictures
and subfolders of a resource path. The available tags are:

* `%base_uri%`: Prefix for absolute path to the root folder (can be blank for root)
* `%breadcrumb%`: A sequence of `<li><a href="...` tags, listing the directories from the root of the gallery to the current resource.
* `%content%`: The galleries for the current folder and a preview of the galleries of the first-level subfolders.
* `%navigation%`: A sequence of `<li><a href="...` tags, listing an `[up]` and all subfolders of the current resource.
* `%pagetitle%`: `picView: {resource}`

### Lightbox Template

The lightbox template is used for the `c` action to display the preview and comments
for any given image, as well as allowing to navigate to the adjacent images. The
available tags are:

* `%actions%`: A sequence of `<li><a href="...` tags, for actions that can be applied to the current resource.
* `%base_uri%`: Prefix for absolute path to the root folder (can be blank for root)
* `%breadcrumb%`: A sequence of `<li><a href="...` tags, listing the directories from the root of the gallery to the current resource.
* `%comments%`: A sequence of blockquotes and a form for displaying and writing comments.
* `%content%`: The carousel of the current image and the links to the adjancent images.
* `%navigation%`: A sequence of `<li><a href="...` tags, listing an `[up]` and all subfolders of the current resource.
* `%pagetitle%`: `picView: {resource}`

## Example configuration

The following is an example configuration of picView:

```php
<?php
$template_file = 'style/picview.tpl';
$lightbox_file = 'style/lightbox.tpl';
$copyright_file = 'copyright.txt';
$font_file = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
$thumbs_path = '/var/www/html/thumbs';
$comments_path = '/var/www/html/comments';
$thumbs_per_page = 30;
$thumb_size = 120;
$medium_size = 600;

$galleries = [
  "picview" => [
    "pictures_path" => "/var/www/html/pictures",
    "auth_required" => false,
    "title" => "pictures"
  ],
  "wp-content" => [
    "pictures_path" => "/var/www/html/wp-content",
    "auth_required" => "password",
    "password" => [
      "user" => "somepassword"
    ]
  ]
];
?>
```
