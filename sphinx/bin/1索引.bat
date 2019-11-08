@echo off
echo ------------------------------------------------------
echo Date£º%date%
echo Time£º%time%
echo ------------------------------------------------------
color 7
title Sphinx Ë÷Òý indexer --all --rotate
@echo on
indexer.exe -c sphinx.conf --all --rotate
@pause>nul
exit