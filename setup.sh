#!/bin/sh

if [ "$1" = 'empty' ]; then
    cp stubs/ContainerEmpty.php app/Container/Container.php
elif [ "$1" = 'medium' ]; then
    cp stubs/ContainerMedium.php app/Container/Container.php
elif [ "$1" = 'full' ]; then
    cp stubs/ContainerFull.php app/Container/Container.php
fi
