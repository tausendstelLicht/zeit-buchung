#!/bin/sh

# ruby-cli-app
# A wrapper script for invoking ruby-cli-app with docker
# Put this script in $PATH as `ruby-cli-app`

PROGNAME="$(basename $0)"
VERSION="latest"
IMAGE="zeitbuchung"

# Helper functions for guards
error(){
  error_code=$1
  echo "ERROR: $2" >&2
  echo "($PROGNAME wrapper version: $VERSION, error code: $error_code )" &>2
  exit $1
}
check_cmd_in_path(){
  cmd=$1
  which $cmd > /dev/null 2>&1 || error 1 "$cmd not found!"
}

# Guards (checks for dependencies)
check_cmd_in_path docker
mkdir -p $HOME/.zeitbuchung/records

# Set up mounted volumes, environment, and run our containerized command
exec docker run \
  --interactive --tty --rm \
  --volume "$HOME/.zeitbuchung/records":/app/recordFiles \
  --user "$(id -u):$(id -g)" \
  "$IMAGE:$VERSION" php /app/bin/zeit-buchung.php "$@"