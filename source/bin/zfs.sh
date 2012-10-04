#!/bin/sh
# Get current foldername
currentFolder=`basename \`pwd\``

# Check if we are executing the script inside scripts folder or in the root folder
if [ $currentFolder == "bin" ] ; then
    echo "You must run this script from the root folder"
    exit 1
else
    prefix='./'
fi

# add the directory to include_path
ZF_CONFIG_FILE='bin/zf.ini'
export ZF_CONFIG_FILE

# zf.sh call
./bin/zf.sh $@
