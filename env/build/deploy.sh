#!/usr/bin/env bash

set -e

# Import our environment variables
source ".env" #import constants

THEMES_FOLDER="./"
# Includes helper functions
. "env/build/functions.sh"

echo -e "${INFO_C}Deploying ${WP_HOME} - ${WP_ENV} ${NC}\n"

# Check composer is installed
check_composer_install

# Check root composer.json
VENDOR_FOLDER="vendor"
if is_modified_git $COMPOSER_CONFIG; then
    do_composer_update $COMPOSER_CONFIG
elif [ ! -d "${VENDOR_FOLDER}" ]; then
     echo -e "${INFO_C}  Missing Vendor folder!.${NC}"
     do_composer_update $COMPOSER_CONFIG
fi

# Check the composer.json file in the root of our themes folders
find $THEMES_FOLDER -maxdepth 2 -name $COMPOSER_CONFIG |while read fname; do
  FOLDER_NAME=$(dirname "${fname}")
  VENDOR_FOLDER="${FOLDER_NAME}/vendor"
  if is_modified_git "$fname"; then
      do_composer_update "$fname"
  elif [ ! -d "${VENDOR_FOLDER}" ]; then
     echo -e "${INFO_C}  Missing Vendor folder!.${NC}"
     do_composer_update "$fname"
  fi
done

echo -e ""

check_yarn_install

# Check the package.json file in the root of our themes folders
find $THEMES_FOLDER -maxdepth 2 -name $YARN_CONFIG |while read fname; do
#  Always run yarn update, in case the machine we're using requires different binaries
  do_yarn_update "$fname"
  do_yarn_build "$fname"
done

echo -e ""

echo -e "${SUCCESS_C}Application deployment completed${NC}\n"
exit 0 #exit, all is ok
