#!/bin/bash
# $Id: deploy.sh 2414 2012-10-24 19:11:54Z ecgero $
#
#
USAGE="usage: deploy.sh <SRC_DIR> <SERVER_NAME>"
#
#
if [[ -z ${1} ]]; then
    echo ${USAGE}
    exit -1
fi
if [[ -z ${2} ]]; then
    echo ${USAGE}
    exit -2
fi
#
#
BASEDIR=/opt/swift/web
#
#SRC_DIR=${1}
#SERVER_NAME=${2}
#TARGET_DIR="${BASEDIR}/${SERVER_NAME}/docs/."
SRC_DIR="/home/wnm/megatransfer/ecgero/swiftweb"
SERVER_NAME=${2}
TARGET_DIR="/var/www/swiftweb-dev"
#
#
echo "  removing old application files"
#rm -rf ${BASEDIR}/${SERVER_NAME}/docs/*
#cat /dev/null > ${BASEDIR}/${SERVER_NAME}/logs/access_log
#cat /dev/null > ${BASEDIR}/${SERVER_NAME}/logs/error_log
#
#
# copy php files, etc to the DOCUMENT_ROOT
echo "  deploying SwiftShift application"
cp -r ${SRC_DIR}/src/web/pages       ${TARGET_DIR}
cp -r ${SRC_DIR}/src/web/db          ${TARGET_DIR}/pages
cp -r ${SRC_DIR}/src/web/preferences ${TARGET_DIR}/pages
cp -r ${SRC_DIR}/src/web/properties  ${TARGET_DIR}/pages
cp -r ${SRC_DIR}/src/web/schedule    ${TARGET_DIR}/pages
cp -r ${SRC_DIR}/src/web/section     ${TARGET_DIR}/pages
cp -r ${SRC_DIR}/src/web/util        ${TARGET_DIR}/pages
cp ${SRC_DIR}/src/web/pages/favicon.ico       ${TARGET_DIR}/pages
cp ${SRC_DIR}/src/web/pages/favicon.ico       ${TARGET_DIR}
echo "  making index.php"
cp ${TARGET_DIR}/pages/WorkerLoginPage.php ${TARGET_DIR}/pages/index.php
#
echo "  deploying SwiftShift public site"
cp -r ${SRC_DIR}/src/public     ${TARGET_DIR}
#
echo "  deploying 3rd party libs"
cp -r ${SRC_DIR}/src/web/jquery     ${TARGET_DIR}/pages
cp -r ${SRC_DIR}/src/web/swwat      ${TARGET_DIR}/pages
#
echo "  deploying sql files"
cp -r ${SRC_DIR}/src/db/sql      ${TARGET_DIR}/pages
#cp ${SRC_DIR}/src/db/scripts/db/scripts/dbschema.php.hosted ${TARGET_DIR}/pages/dbschema.php
echo "  deploying SwiftShift admin"
cp -r ${SRC_DIR}/src/web/admin       ${TARGET_DIR}/pages/admin
cp -r ${SRC_DIR}/src/web/db          ${TARGET_DIR}/pages/admin
cp -r ${SRC_DIR}/src/web/properties  ${TARGET_DIR}/pages/admin
cp -r ${SRC_DIR}/src/web/util        ${TARGET_DIR}/pages/admin
cp -r ${SRC_DIR}/src/web/swwat       ${TARGET_DIR}/pages/admin
# presumably do not need other stuff
#
# remove all .svn directories
rm -rf `find ${TARGET_DIR} -name '.svn'`
cd ${TARGET_DIR}
tar -cf pages.tar pages
#cp ${SRC_DIR}/src/web/scripts/deploy/deploy.php ${TARGET_DIR}
cp ${SRC_DIR}/src/web/admin/DeployAction.php ${TARGET_DIR}
cp ${SRC_DIR}/src/web/admin/DeployPage.php ${TARGET_DIR}
#cp ${TARGET_DIR}/../properties/constants.php ${TARGET_DIR}/pages/properties/constants.php
#cp ${TARGET_DIR}/../properties/constants.php ${TARGET_DIR}/pages/admin/properties/constants.php
