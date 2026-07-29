#!/bin/bash -e

## EXIT IF NO ARGUMENTS ARE GIVEN
if [[ $# -eq 0 ]] ; then
    echo "Usage: $0 ModuleName RegistrarUrl RegistrarNS"
    echo "example:"
    echo "$0 DomainRobot www.domain-robot.org ns14.net"
    echo "will create ./dist/DomainRobot and rename all files and sourcecode"
    exit 1
fi

if [[ "$OSTYPE" == "darwin"* ]]; then
  # Require gnu-sed.
  if ! [ -x "$(command -v gsed)" ]; then
    echo "Error: 'gsed' is not istalled." >&2
    echo "If you are using Homebrew, install with 'brew install gnu-sed'." >&2
    exit 1
  fi
  SED_CMD=gsed
else
  SED_CMD=sed
fi

# THE NAME OF THE TARGET REGISTRAR TO BUILD
REGISTRARNAME=$1
REGISTRARURL=$2
REGISTRARNS=$3

# SRC is the original InterNetX Module. So that what we will replace
PLACEHOLDERNAME=InterNetXJson

# Script Vars and Derived values
SRCFOLDER=src
DISTFOLDER=dist
PLACEHOLDERNAME_LOWERCASE=$(echo "$PLACEHOLDERNAME" | tr '[:upper:]' '[:lower:]')
REGISTRARNAME_LOWERCASE=$(echo "$REGISTRARNAME" | tr '[:upper:]' '[:lower:]')
OUTPUTFOLDER=${DISTFOLDER}/$REGISTRARNAME

VERSION=$(cat ./VERSION)

echo "About to create a module Version for $REGISTRARNAME in '$OUTPUTFOLDER'"

# REMOVE OUTPUTFOLDER
echo "Deleting and Creating Outputfolder (${OUTPUTFOLDER})"
rm -rf $OUTPUTFOLDER
mkdir -p $DISTFOLDER

# Copy all Files to the new Folder
echo "Copying files from src to (${OUTPUTFOLDER})"
cp -a $SRCFOLDER $OUTPUTFOLDER

# Copy Logo for Registarmodule
echo "Copying Logo (cp assets/$REGISTRARNAME_LOWERCASE/logo.gif $OUTPUTFOLDER/install/modules/registrars/$PLACEHOLDERNAME/)"
cp assets/$REGISTRARNAME_LOWERCASE/logo.gif $OUTPUTFOLDER/install/modules/registrars/$PLACEHOLDERNAME/

# Rename Across all Files in OUTPUTFOLDER
cd $OUTPUTFOLDER

## Replace Link to Account creation in Admin Area
echo "Replacing Link to Registrar Account Creation"
${SED_CMD} -i "s/www.$PLACEHOLDERNAME_LOWERCASE.com/$REGISTRARURL/" install/modules/registrars/InterNetXJson/app/RegistrarModule.php


## Replace InterNetX with RegistrarName (Case sensitive) Across all Files
echo "Replacing $PLACEHOLDERNAME with $REGISTRARNAME across all files"
grep -rl $PLACEHOLDERNAME .           | xargs ${SED_CMD} -i "s/$PLACEHOLDERNAME/$REGISTRARNAME/g"
grep -rl $PLACEHOLDERNAME_LOWERCASE . | xargs ${SED_CMD} -i "s/$PLACEHOLDERNAME_LOWERCASE/$REGISTRARNAME_LOWERCASE/g"

## Replace Nameserver domain in class.InterNetX_API.php
echo "Replacing demo.autodns2.de with $REGISTRARNS in install/modules/registrars/InterNetXJson/app/Helpers/Config.php"
${SED_CMD} -i "s/demo.autodns2.de/$REGISTRARNS/" install/modules/registrars/InterNetXJson/app/Helpers/Config.php


echo "Replacing version with $VERSION"
${SED_CMD} -i "s/DEVELOPMENT-VERSION/$VERSION/" install/modules/registrars/InterNetXJson/app/Helpers/Config.php

cp ../../VERSION VERSION

## Change into module install files folder
cd install

## Removing Tests
rm modules/registrars/InterNetXJson/test.sh
rm -rf modules/registrars/InterNetXJson/tests
## Removing Monkey-Debug Logs
rm -rf modules/registrars/InterNetXJson/app/Helpers/log

rm -rf modules/registrars/InterNetXJson/vendor/phpunit
rm -rf modules/registrars/InterNetXJson/vendor/nikic
rm -rf modules/registrars/InterNetXJson/vendor/bin
rm -rf modules/registrars/InterNetXJson/vendor/sebastian
rm -rf modules/registrars/InterNetXJson/vendor/phar-io
rm -rf modules/registrars/InterNetXJson/vendor/doctrine
rm -rf modules/registrars/InterNetXJson/vendor/myclabs
rm -rf modules/registrars/InterNetXJson/vendor/theseer
rm modules/registrars/InterNetXJson/composer.json
rm modules/registrars/InterNetXJson/composer.lock
rm modules/registrars/InterNetXJson/vendor/internetx/php-domainrobot-sdk/swagger-codegen-cli.jar


## Rename Folders
echo "Renaming Folders"
mv modules/addons/$PLACEHOLDERNAME modules/addons/$REGISTRARNAME
mv modules/registrars/$PLACEHOLDERNAME modules/registrars/$REGISTRARNAME

## Rename Files
echo "Renaming Files"
## Premium Domain Addon
mv modules/addons/${REGISTRARNAME}/app/Models/Migrations/Create${PLACEHOLDERNAME}PremiumDomiansPricingTable.php modules/addons/${REGISTRARNAME}/app/Models/Migrations/Create${REGISTRARNAME}PremiumDomiansPricingTable.php
mv modules/addons/${REGISTRARNAME}/app/Models/Migrations/Create${PLACEHOLDERNAME}PremiumDomiansClassesTable.php modules/addons/${REGISTRARNAME}/app/Models/Migrations/Create${REGISTRARNAME}PremiumDomiansClassesTable.php
mv modules/addons/${REGISTRARNAME}/${PLACEHOLDERNAME}.php modules/addons/${REGISTRARNAME}/${REGISTRARNAME}.php
## Registrar Module Entry File
mv modules/registrars/${REGISTRARNAME}/${PLACEHOLDERNAME}.php modules/registrars/${REGISTRARNAME}/${REGISTRARNAME}.php

cd ../..
zip -r ${REGISTRARNAME}_whmcs.zip ${REGISTRARNAME}

echo "All Done. The new Module files can be found in '$OUTPUTFOLDER'"