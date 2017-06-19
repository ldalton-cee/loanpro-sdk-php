#!/usr/bin/env bash

read -r -p "Are you contributing to the PHP SDK? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY])
        bash prep_dev_env.sh
        ;;
    *)
        read -r -p "Please enter your tenant ID: " tenant_id
        read -r -p "Please enter your API token: " token
        echo "Writing config file..."
        echo "[api]" > src/config.ini
        echo "tenant=$tenant_id" >> src/config.ini
        echo "token=$token" >> src/config.ini
        echo "" >> src/config.ini
        echo "[communicator]" >> src/config.ini
        echo "type=sync" >> src/config.ini
        echo "Config file written."
        ;;
esac

echo "Running Composer..."
{
  php composer.phar install
} &> /dev/null
echo "Done with composer"
echo "The SDK is setup!"
