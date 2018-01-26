#!/bin/bash
cd imgs
./create.sh
cd ..
latex dokumentaatio.tex
pdflatex dokumentaatio.tex
