if [ "$APPLICATION_ENV" = 'production' ]; then
    app_version=$(git describe --tags)
    app_commit=$(git rev-parse --short HEAD)

    export NEW_RELIC_METADATA_SERVICE_VERSION=$app_version
    export NEW_RELIC_METADATA_RELEASE_TAG=$app_version
    export NEW_RELIC_METADATA_COMMIT=$app_commit
fi