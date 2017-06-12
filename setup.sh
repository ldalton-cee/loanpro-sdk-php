#!/usr/bin/env bash

read -r -p "Are you contributing to the PHP SDK? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY])
        bash prep_dev_env.sh
        ;;
    *)
        read -r -p "Please enter your tenant ID: " tenant_id
        read -r -p "Please enter your API token: " token
        echo "Writting config file..."
        echo "[api]" > src/config.ini
        echo "tenant=$tenant_id" >> src/config.ini
        echo "token=$token" >> src/config.ini
        echo "" >> src/config.ini
        echo "[communicator]" >> src/config.ini
        echo "type=sync" >> src/config.ini
        ;;
esac
