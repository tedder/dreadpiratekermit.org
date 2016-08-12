#!/bin/sh
AWS_PROFILE=pjnet aws s3 sync . s3://dreadpiratekermit.org/ --exclude ".git" --exclude "*.sh" --exclude ".DS_Store"
