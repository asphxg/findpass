@echo off
echo ------------------------------------------------------
echo Date£º%date%
echo Time£º%time%
echo ------------------------------------------------------
color 7
title Sphinx
@echo on
searchd.exe -c sphinx.conf