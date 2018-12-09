v1.8.11
-giq-84: updated the address objects to return the squarefootunderroof and constructionyear on address validation

v1.8.10
-update iqs_db.sql scripts for all known FE variants to include ezaddresses creds

v1.8.9
-add api method credentials override feature
-add api method credentials override manager to iqs-admin
-update iqs_db_update.sql scripts for all known FE variants to include ezaddresses creds

v1.8.8
-update to IQS validation code to accommodate FL
-update FEDNAT SQL scripts for new user
-update ezquoteapiaccessor with ezaddresses unique token (temp fix)

v1.8.7
-sql scripts now include default block codes for new db
-stormpath api keys updated; Curl for iqs-admin removed and replaced with IQS library calls
-Update prod environment to jessie from wheezy
-Fix for "/quotes/" folder in the URL (core update, effective for all IQS-FE projects)
-breakout of front end modules from IQS
-Support for EzQuote 2.1.1-100 (auth updates)
-IQS-Admin (config, zip code whitelist, block codes)
-Update QuotePdfGenerator.php to support 2% windhaildeductible