# Gamers' Plane v1

## Setup

To run the dev version of GP locally, you need to install `docker` and `docker-compose` locally.

Once both are setup, open your console to the GP root directory. From there, you can use the following commands to do put up and take down the environment, as well as some basic functionality.

- `./docker/up`: This brings up the environment. You can add a `-d` flag to run in detacted mode and `-b` to add the build flag (in case you need to rebuild the environment). You can add a service as the final argument to the end to specifically bring up/rebuild one service.
- `./docker/down`: Bring down the environment. You can add `-v` to remove any values.
- `./docker/logs`: Show the docker flogs for all running services. You can add a `-f` flag to run in follow mode. You can add a service name as the final argument to see the logs for just that environment.
- `./docker/shell`: Add a service as the final argument to open a shell in that service.

You'll also need to add two entires to your hosts file: point `gamersplane.local` and `api.gamersplane.local` to your docker IP.

Once up, there is a default user created for use:

```
GPTest
Test1234
```