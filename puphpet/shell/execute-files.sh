#!/bin/bash

VAGRANT_CORE_FOLDER=$(cat "/.puphpet-stuff/vagrant-core-folder.txt")

shopt -s nullglob

echo 'Running files in files/exec-always'
find "${VAGRANT_CORE_FOLDER}/files/exec-always" -maxdepth 1 -not -path '*/\.*' -type f \( ! -iname "empty" \) -exec chmod +x '{}' \; -exec {} \;
echo 'Finished running files in files/exec-always'
