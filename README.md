# ExCL Wordpress Content Management System #
============================================

## Setup Steps ##
=================

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