#!/bin/bash

VENDORDIRECTORY=vendor/sbb/availabilityplus
VUFINDDIRECTORY=availabilityplus

if [ -d "$VENDORDIRECTORY" ]; then
    ln -rs $VENDORDIRECTORY/module/AvailabilityPlus module/
    ln -rs $VENDORDIRECTORY/themes/availabilityplus themes/
    ln -rs $VENDORDIRECTORY/local/config/vufind/* local/config/vufind/
    ln -rs $VENDORDIRECTORY/local/languages/AvailabilityPlus languages/

    echo "Symlinked all files."
elif [ -d "$VUFINDDIRECTORY" ]; then
    ln -rs $VUFINDDIRECTORY/module/AvailabilityPlus module/
    ln -rs $VUFINDDIRECTORY/themes/availabilityplus themes/
    ln -rs $VUFINDDIRECTORY/local/config/vufind/* local/config/vufind/
    ln -rs $VUFINDDIRECTORY/local/languages/AvailabilityPlus languages/

    echo "Symlinked all files."
fi
