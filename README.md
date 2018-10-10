# File uploader

This project is a file uploader system written in PHP.

Upload limitations: (authorized file extension)
(you can edit those limit in `app/config.php`)

- *.png
- *.jpg
- *.jpeg

Maximum upload file size: 4M

# Directories structure

## app

The app folder app contains all the logical application build in PHP.

## docker

The docker folder contains all configuration related to docker containers.

# App environment variables

There is default value for environment variables but these value can be set in `app/.env`.

| KEY | EXPECTED VALUE |
| --- | --- |
| D_MAX_FILE_SIZE | The maximum upload file size in M (ex: 4) |
| S_UPLOAD_PATH | the directory where file will be stored  |
| S_SECRET | Some random well secured string (ex: a5rGe=nfb+9D%zPV) |

# Generate SSL certificate and key with mkcert

* mkcert documentation: https://github.com/FiloSottile/mkcert

```
mkcert localhost 127.0.0.1 ::1
```
When you certificate and key are fuilly created you need drop them in `docker/conf/nginx/cert` folder and rename them like the following:

| current file name | new file name |
| --- | --- |
| localhost+5.pem | localhost.pem |
| localhost+5-key.pem | localhost-key.pem |

# Build server 
```
# Build images
docker-compose -f docker/docker-compose.yml build
```

# Launch server

```
# Launch server
docker-compose -f docker/docker-compose.yml up

# Lanch server in background
docker-compose -f docker/docker-compose.yml up -d
```

# Install application dependencies

```
docker exec myproject-app composer install
```

# Public app access

```
https://127.0.0.1:8080
```
