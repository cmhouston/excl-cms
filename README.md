# Introduction to ExCL #
ExCL is a platform that enables museums to engage visitors at museum activities through the use of 
a mobile application. Content is managed through a WordPress content management system by museum 
staff, and visitors will download the customized ExCL app, written using Appcelerator’s Titanium, 
to their mobile device. ExCL is also intended to be used by museums on kiosk devices and provides 
a kiosk mode for this purpose.

This documentation is intended for ExCL developers and details the steps to setup and enhance both 
the content management system and the mobile application. We will describe both the WordPress and 
the Titanium technical details, followed by tips on using a continuous integration build server and 
deploying to the app stores.

For the WordPress developer documentation, please go [here]()

For the Titanium developer documentation, please go [here]()

## ExCL Wordpress Content Management System ##

### Getting started ###

1. Download wordpress from http://wordpress.org/download
2. Unzip it and copy the contents of the wordpress folder to c:\wamp\www\wp
3. Clone the cmh-excl-cms repository (go to https://bitbucket.org/parivedasolutions/cmh-excl-cms to see your clone link) from bitbucket to anywhere on your harddrive (like My Documents)
4. Set up DSynchronize with the source folder being the wp-content\plugins directory in the cloned repo and the destination folder being c:\wamp\www\wp\wp-content\plugins directory. Make sure the box by sources is checked and then check "Copy only newer files" and "Create folder if not exists". Click the synchronize button once to make sure that it works, then check the "Realtime sync" box to have it watch the changes and copy them automatically to your wp folder.
5. Go to http://localhost/wp in your browser and run the install. Make sure to use the following information for the database credentials
	database name: excl_db_wp
	database user: excldb
	database pass: Pariveda1
	database prefix: dev_
	database host: mysql.excl.dreamhosters.com
6. It should see that there is already a wordpress instance set up and let you log in as 'admin' with password 'Pariveda1'
7. Develop!

Note: To upload files to dreamhost, use FileZilla.
	Host: tabit.dreamhost.com
	username: gkuncheria
	pass: dkx2Y34C
	Save file path: >excl.dreamhosters.com > dev > wp-content > plugins