# IQS 
The IQS (Insurance Quoting Service) is a PHP based web application which acts as an API  
to obtain home owner's insurance quotes from the ICG 360 EzQuote system. When used in conjunction
with a front end application the IQS manages session state, records relevant session data, 
provides access to EzQuote, and allows for various other configuration and functionality features.

# Deploying IQS with a Front End project (FE)

When deploying a complete package of IQS and an FE, the various files, folders, structure and configuration
must be executed as described below.

## Requirements
The requirements below are what is used in development and development testing by OHS. Some variation
may be acceptable however OHS recommends thorough testing if there is deviation.

Database Server: Microsoft SQL Server (Dev uses 2012 Express)
Env: Docker - debian:jessie 
Web Server: Apache2
PHP:  5.4 or higher

Software packages required:

Software: Unix ODBC 2.3.2 (compiled and included in dev_materials/docker_scripts/ohs_docker/files)
Purpose: Facilitates communications with MSSQL Database

Software: Microsoft ODBC Driver 11 ((compiled and included in dev_materials/docker_scripts/ohs_docker/files))
Purpose: Facilitates communications with MSSQL Database

Software: php5-json
Purpose: PHP library for parsing JSON

Software: php5-odbc
Purpose: PHP library for database communication 

Software: php5-curl
Purpose: PHP library for making HTTP requests (to the EzQuote API)

Software: php5-mcrypt
Purpose: PHP library for encryption (encrypting of SSN and other sensitive data as required by law)

Software: php5-gd
Purpose: PHP library required by dompdf for rendering PDF files

Software: Slim Framework
Purpose: PHP framework for building REST interfaces

Software: dompdf
Purpose: PHP library for generating PDF files


## Deployment Location 
It is assumed, for the purposes of this document, that the IQS and FE files will be deployed to the root
of an Apache web server. It is possible to deploy the IQS and it's associated FE in various different
ways, however this document only describes deploying to the root of the web server.

## Deployment Structure IQS
When deploying IQS to the web server, simply copy the Iqs and iqs-admin folders from the iqs GitHub repo 
to the root of the web server. Once complete your web server root should look as such:

Iqs/
iqs-admin/

### IQS Configuration
The IQS has a number of configuration files which may require modification prior to deployment. You can find all
base configuration files in the dev_materials/templates folder in the iqs GitHub repo. Some of these templates
are only used in development so as a result not all template files are required for deployment. Deployment required 
template files and their destination for deployment are:

filePaths.ini           ->      Iqs/conf/filePaths.ini
init.php                ->      iqs-admin/app/init.php
iqsConf.ini             ->      **Based on filePaths.ini configuration**

### IQS Configuration File Details

__filePaths.ini__
In order to safeguard the sensitive nature of the various configuration properties held in the iqsConf.ini file 
such as database location, user ID and password, the filePaths.ini files simply points to the location of the 
iqsConf.ini file. There is one token that must be replaced in the filePaths.ini file, it is:

@iqsconfpath@

For example, if the path to your iqsConf.ini file is /etc/iqs/iqsConf.ini then your filePaths.ini file should have 
the following entry:

[iqsconf]
filepath = "/etc/iqs/iqsConf.ini";

Replace the @iqsconfpath@ token with the system path (not the relative web path) to your iqsConf.ini file.

__init.php__
init.php provides information necessary for the iqs-admin module to function properly. There are 3 tokens that must 
be replaced in the init.php template. They are:

@iqs_admin_stormpathurl@
Replace this token with the URL necessary to access StormPath. For example, in development we use a development
URL of "https://api.stormpath.com/v1/applications/3oZIRpbLITQV2BMceGEPaT".

@iqs_admin_baseurl@
This provides the relative web path to the root of the IQS-Admin. For example, in development since we access
the admin on port 8080, the path is ":8080/iqs-admin/public/". This is essentially everything after the base URL.

@iqs_admin_iqspath@
The relative path to the IQS library. If configured as directed in this document this value will be "/Iqs/Iqs.php".

__iqsConf.ini__
iqsConf.ini contains the configuration information needed by the IQS to access the database, define the install type,
and the encryption key used to encrypt certain database information.

iqsConf.ini does not have a specific location. Instead it resides wherever the filePaths.ini file directs. 
For safety reasons OHS recommends not storing the iqsCon.ini in a web accessable directory.

There are 8 tokens which require replacement in the iqsConf.ini template. They are:

@dbhost@
The IP address or hostname of the database.

@dbuser@
The User ID required to access the db

@dbpass@
The password required to access the db

@dbname@
The name of the database, typically "IQS"

@dbport@
The port used to access the db, typically "1433"

@dbdriver@
The driver used to access the db. As of writing this value should always be "ODBC Driver 11 for SQL Server"

@installtype@
The database and platform enum. As of writing this valus is always "2" for Linux Apache host using Windows SQL Server (MSSQL ODBC 11 Drivers)

@enckey@
The encryption key used to decrypt encrypted database fields. This value must be at 16 characters long.


## Deployment Structure FE
Each FE contains different modules. In the repo for each FE, there is a folder named "webroot". For the purposes of 
this document we'll assume that the geico-iqs-fe project is being used. To deploy the geico-iqs-fe project simply copy
the required files and folders from the webroot folder to the root of the web server alongside of the IQS folders. The end result should
look as such (assuming that the IQS has already been deployed as described above):

404.html
Iqs/        *(previously copied from iqs repo)
ajax/
assets/
css/
iqs-admin/  *(previously copied from iqs repo)
js/
products/
propertyquote.html
recallquote.html
unsupported.php
.htaccess


### FE Configuration 
Each FE state has a single configuration file known as config.js. There is no template for the config.js
file because OHS handles configuring each state individually. The default configuration from OHS should match
the required configuration for the client staging and deployment environment already. If there does happen to be 
a need to configure the FE states for special circumstances you can find the config file in each state under:

/webroot/products/<state acronym>/js/config.js

Within this file are the following configuration settings (NJ used as a sample):

    var Product_State = "NJ";
    var Feid = "GIQ"+Product_State;
    var Insurance_Product = "HO3MP";
    var QuoteIDPrefix = "CRU4Q-";
    var sessionTimeOutLength = 12 * 60;
    var serviceUrl = "../../Iqs/api.php/";
    var PixelTrackingTestURL = "https://www2-test.geico.com/vs/track.js";
    var PixelTrackingProdURL = "https://www.geico.com/vs/track.js";
    
Product_State: The 2 letter acronym for the state

Feid: The "Front End Identifier" used by the DB to determine which front end the quote data is from

Insurance_Product: Typically defaults to an HO3 of some sort. Is modified by the FE depending on user input.

QuoteIDPrefix: Appended to the Quote ID for EzQuote. The user only sees the digit values, not the prefix.

SessionTimeOutLength: As requested, all sessions last 12 hours, but can be configured here.

ServiceUrl: The location of the IQS API relative to the state folder.

PixelTrackingTestURL: The URL used to send pixel tracking data to GEICO when in debug mode with a bogus SANE ID.

PixelTrackingProdURL: The URL used to send pixel tracking data to GEICO when in production.


##Database Deployment or Update##
With the breakup of the IQS/GIQ project and the creation of the IQS-Admin, the database takes a more
active role in IQS configuration. Previously, all configuration data was stored in the above mentioned
iqsConf.ini file. Today, that file has reduced functionality and all remaining configuration details are stored
in a new table in the database. In addition to storing the configuration data, the database now stores 
zip code white list data.

Not every release of IQS requires a database upgrade. As a matter of fact, DB upgrades are fairly rare. 
Please refer to the release notes at the bottom of this document to determine if a DB upgrade is necessary
for the release.

###Database SQL Templates###
There are 2 SQL template files, listed below. 

__iqs_db.sql__
This sql script is used to build a new databasee for a new installation of IQS. Only use if this is a new 
installation of IQS and no IQS database exists.

__iqs_db_update.sql__
This sql script updates existing IQS databases to work with the latest version of IQS. 

###Database SQL Template Configuration Tokens###
When development builds a release, the sql script template is populated with development configuration values based
on the FE in use. SageSure has a unique installation of IQS for each FE. This means that each unique installation 
of IQS requires its own database.

As part of the build process, each FE gets a specialized SQL script. These scripts can be found in 
/dev_materials/sql_scripts/<front end name>/

There are 2 scripts for each front end:

<FE Name>_iqs_db_new.sql
<FE Name>_iqs_db_update.sql

**<FE Name>_iqs_db_new.sql details:**
If the installation of the IQS and the associated FE are brand new with an empty database then the <FE Name>_iqs_db_new.sql 
script must be run to build the database. **NOTE: at the top of this script are lines of code which determine the location
and various other database settings. Please configure these values to meet your environment before running** The lines of 
code in question are:

      ( NAME = N'IQS', FILENAME = N'C:\GIQ\db\IQS.mdf' , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
    LOG ON
      ( NAME = N'IQS_log', FILENAME = N'C:\GIQ\db\IQS_log.ldf' , MAXSIZE = 2048GB , FILEGROWTH = 10%)
      

**<FE Name>_iqs_db_update.sql**
If the IQS and associated FE is an upgrade, **AND** if the upgrade requires a database update, then there will be an available
sql script for the update. This script is capable of taking a v1.0.0 or later IQS database and updating to the latest 
release.


#DEPLOYING DEV-INT AND STAGING BUILDS WITH JENKINS#
Jenkins will build from GitHub based on the branch name. dev-int builds are triggered from the "develop" branch while 
staging builds are triggered from the "release/<version number>" branch.

There are 2 main parts to releasing with Jenkins:

Part 1: Building IQS
Part 2: Building an FE

If an IQS build is already released and is the correct build for the FE desired then it is simply a matter of referencing 
the docker tag in the FE dockerfile. Otherwise, if a new version of IQS is required for the FE then both IQS and the FE must be built. 

## Building IQS For Dev-Int ##
1.) Merge all features desired into the develop branch

2.) Update the /README.MD file with the latest information if there is any

3.) Update the /RELEASE_NOTES.md file with the latest release notes for the version

4.) Update the /dev_materials/phing_scripts/build_iqs.xml file to reflect the proper version on line 12. Example:
<property name="vernum" value="1.8.2" />

5.) Update the /dev_materials/phing_scripts/build_iqs.xml file to reflect the proper release level on line 13. Example:
<property name="vertype" value="dev"/>
The value for dev-int should always be "dev".

6.) Run the Phing build script

7.) Commit to the "develop" branch

8.) Push

Notes:
Jenkins will see the new "develop" branch commit and automatically begin building the dev-int container. If you wish to 
use this IQS container with an FE build then you will need to log into Jenkins and search for the "iqs-art-docker" project.
The project page will show the docker tag. Take note of this tag, you'll need it to build the FE container which will
use this build of the IQS. A docker tag for a dev-int build will look similar to this: "1.8.2-60-develop-76ed42a-51"

## Building IQS For Staging ##

1.) From the develop branch, create a new release branch using the naming convention "release/<version number>". For
example, at the time of writing the version is 1.8.2, so a release branch would be "release/1.8.2"

2.) The /README.md should already have the latest update from when the dev-int build was created

3.) The /RELEASE_NOTES.md should already have the latest update from when the dev-int build was created

4.) Update the /dev_materials/phing_scripts/build_iqs.xml file to reflect the proper version on line 12. This
should have already been set when creating the dev-int build. Example:
<property name="vernum" value="1.8.2" />

5.) Update the /dev_materials/phing_scripts/build_iqs.xml file to reflect the proper release level on line 13. Example:
<property name="vertype" value="release"/>
The value for a staging release should always be "release".

6.) Run the Phing build script

7.) Commit to the "release/<version_number>" branch

8.) Push

Notes:
Jenkins will see the new "release" branch commit and automatically begin building the staging container. If you wish to 
use this IQS container with an FE build then you will need to log into Jenkins and search for the "iqs-art-docker" project.
The project page will show the docker tag. Take note of this tag, you'll need it to build the FE container which will
use this build of the IQS. A docker tag for a staging build will look similar to this: "1.8.2-60-release-76ed42a-51"

## Building an FE for dev-int ##

1.) Merge all features desired into the develop branch

2.) Switch to the develop branch

3.) Update the /README.MD file with the latest information if there is any

4.) Update the /RELEASE_NOTES.md file with the latest release notes for the version

5.) If necessary, update the /docker/Dockerfile on line 1 to reflect the correct docker tag for the IQS you wish to use. 
Obtain this tag from Jenkins as described in the "Notes" sections above for building dev-int and staging. For example,
to use the latest release build (as of writing) of IQS with your FE, line one of the Dockerfile should be:
"FROM registry.icg360.net/iqs:1.8.2-60-release-76ed42a-51"

6.) Update the /dev_materials/phing_scripts/build_<feid>.xml file to reflect the proper version on line 12. Example:
<property name="vernum" value="1.8.2" />

7.) Update the /dev_materials/phing_scripts/build_<feid>.xml file to reflect the proper release level on line 13. Example:
<property name="vertype" value="dev"/>
The value for dev-int should always be "dev".

8.) Run the Phing build script
8.) Run the Phing build script

9.) Commit to the "develop" branch

10.) Push

Jenkins will see the new "develop" branch commit and automatically begin building the dev-int container. If you don't know
the URL for the dev-int container you will need to contact dev-ops. The dev-int URL for the FNIC container is:
"https://fnic-iqs-fe.dev-int.icg360.net/"

## Building an FE For Staging ##

1.) From the develop branch, create a new release branch using the naming convention "release/<version number>". For
example, at the time of writing the version is 1.8.2, so a release branch would be "release/1.8.2"

2.) Switch to the release branch

3.) The /README.md should already have the latest update from when the dev-int build was created

4.) The /RELEASE_NOTES.md should already have the latest update from when the dev-int build was created

5.) If necessary, update the /docker/Dockerfile on line 1 to reflect the correct docker tag for the IQS you wish to use. 
Obtain this tag from Jenkins as described in the "Notes" sections above for building dev-int and staging. For example,
to use the latest release build (as of writing) of IQS with your FE, line one of the Dockerfile should be:
"FROM registry.icg360.net/iqs:1.8.2-60-release-76ed42a-51"

6.) Update the /dev_materials/phing_scripts/build_iqs.xml file to reflect the proper version on line 12. This
should have already been set when creating the dev-int build. Example:
<property name="vernum" value="1.8.2" />

7.) Update the /dev_materials/phing_scripts/build_iqs.xml file to reflect the proper release level on line 13. Example:
<property name="vertype" value="release"/>
The value for a staging release should always be "release".

8.) Run the Phing build script

9.) Commit to the "release/<version_number>" branch

10.) Push

Notes:
Jenkins will see the new "release/<version_number>" branch commit and automatically begin building the staging container. If you don't know
the URL for the staging container you will need to contact dev-ops. The staging URL for the FNIC container is:
"https://stage.federatedhomeowners.com/quotes/"

#FURTHER DISCUSSION#