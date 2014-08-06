# ExCL Developer Documentation #

### Contents ###

1. [Introduction to ExCL](#exclIntro)
- [WordPress](#wordpress)
- [Titanium](https://github.com/cmhouston/excl-mobile#titanium)


# <a name="exclIntro"></a> Introduction to ExCL #
ExCL is a platform that enables museums to engage visitors at museum activities through the use of 
a mobile application. Content is managed through a WordPress content management system by museum 
staff, and visitors will download the customized ExCL app, written using [Appcelerator Titanium](http://www.appcelerator.com/titanium/), 
to their mobile device. ExCL is also intended to be used by museums on kiosk devices and provides 
a kiosk mode for this purpose.

ExCL is divided into two parts: the content management system and the Appcelerator Titanium mobile application. This repository is for the WordPress content management system. [Click here to go to the Titanium project](https://github.com/cmhouston/excl-mobile).

This documentation is intended for ExCL developers and details the steps to setup and enhance  
the content management system, which uses WordPress.

If you are a developer, see the [developer documentation](docs/developerDocs.md) for the ExCL Wordpress technical documentation.

## <a name="wordpress"></a>WordPress ##

### Getting started ###

1. Download wordpress from http://wordpress.org/download
2. Unzip it and copy the contents of the wordpress folder to your web directory
3. Clone the this repository to anywhere on your harddrive (like My Documents)
4. _(Optional)_ Set up DSynchronize with the source folder being the wp-content\plugins directory in the cloned repo and the destination folder being {WORDPRESS\_INSTALLATION\_DIRECTORY}\wp-content\plugins directory. Make sure the box by sources is checked and then check "Copy only newer files" and "Create folder if not exists". Click the synchronize button once to make sure that it works, then check the "Realtime sync" box to have it watch the changes and copy them automatically to your wp folder.
5. Go to http://localhost/{WORDPRESS\_INSTALLATION\_DIRECTORY} in your browser and run the install.

### Development ###

If you are a developer, see the [WordPress developer documentation](docs/developerDocs.md)
