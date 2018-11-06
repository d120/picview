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
2. `cd style/d120 && npm install`

### Upgrading from the old version

1. `git pull <this repository>`
2. `cd style/d120`
3. `git submodule update --init`
4. `npm install`

## Configuration

The entire instance of picview is configured by some global variables in
a `config.php` file in the root directory. To set one of the following options,
simply create a variable with that name in the configuration file.

### Comments path (`$comments_path`)

* *Type*: `String`
* *Required*: `true`
* *Options*: Any existing and writable path on the system
* *Description*: The path where comment data is stored.

### Copyright filename (`$copyright_file`)

* *Type*: `String`
* *Required*: `true`
* *Example*: `copyright.txt`
* *Description*: Naming convention for a file from which the text is rendered on
  every image in that folder when it exists.

### Overlay font file (`$font_file`)

* *Type*: `String`
* *Required*: `true`
* *Example*: `/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf`
* *Description*: A TTF-File that can be used to render unicode code points on
  the image. Used whenever text is written into the images.

### LDAP Host (`$ldapHost`)

* *Type*: `String`
* *Required*: Only if there is a gallery with the `ldap` authentication method.
* *Description*: Any valid URL of an LDAP server host.
  See [ldap_connect](https://secure.php.net/manual/en/function.ldap-connect.php).

### Lightbox Template File (`$lightbox_file`)

* *Type*: `String`
* *Required*: `true`
* *Options*: Any existing file absolute or relative to the `index.php` script.
* *Description*: The template for the picture comment/lightbox action.

### Template File (`$template_file`)

* *Type*: `String`
* *Required*: `true`
* *Options*: Any existing file absolute or relative to the `index.php` script.
* *Description*: The template for the directory view action.

### Thumbnail Path (`$thumbs_path`)

* *Type*: `String`
* *Required*: `true`
* *Options*: Any existing and writable path on the system
* *Description*: The path where preview images are stored / cached.

## Galleries

PicView allows for multiple galleries on a single instance. Each gallery is
assigned a separate `galleryId`, which corresponds to the first element in the
`PATH_INFO` for any PicView URL.

To configure the galleries, add an array named `$galleries` to `config.php`.
Within that array, add a configuration array for each gallery. The key used for
this inner array is the `galleryId`. The following subsections will explain the
different configuration options that are available.

### Title (`title`)

* *Type*: `String`
* *Required*: `false`
* *Default*: The gallery id
* *Description*: Sets a title for this gallery. Used in breadcrumbs.

### Authentication Method (`auth_required`)

* *Type*: `String`
* *Required*: `true`
* *Options*: `'ldap'`, `'password'` or `''`
* *Description*: The method used to restrict access to certain people.
  The LDAP method will allow any user from the configured LDAP,
  the password method will allow any username and password configured for this gallery (`password`).
  The default method is to allow any user agent to access the pictures.

### Passwords (`password`)

* *Type*: `Array(String => String)`
* *Required*: Only if using authentication method `password`
* *Description*: Array where the keys are valid users and the values are valid passwords

### Pictures Path (`pictures_path`)

* *Type*: `String`
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

The URLs for these actions always have the form `/picview/index.php/{gallery}/{action}/{resource}`

## Templating

Template files are plain HTML files which may contain tags that will be replaced
by dynamic content. Currently, the following tags are supported for the main template:

`%pagetitle%`, `%navigation%`, `%content%`, `%breadcrumb%`
