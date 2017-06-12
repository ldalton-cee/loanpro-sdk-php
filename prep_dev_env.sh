#!/usr/bin/env bash
ln -s ../../pre-commit.sh .git/hooks/pre-commit
ln -s ../../pre-push.sh .git/hooks/pre-push
echo "Git hooks made. Please create 'src/config.ini' based on 'https://phabricator.simnang.com/w/projects/fandora/php_sdk/'"