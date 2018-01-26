#!/bin/bash

source config/environment.sh

echo "Poistetaan tietokantataulut..."

ssh $USERNAME@users2017.cs.helsinki.fi "
cd htdocs/$PROJECT_FOLDER/sql
psql < drop_tables.sql
exit"

echo "Valmis!"
