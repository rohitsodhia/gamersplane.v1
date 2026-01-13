#!/bin/bash

SCRIPT_DIR=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" &>/dev/null && pwd)
composeFiles=("-f $SCRIPT_DIR/compose.yml")
remainingArgs=()

while [ $# -gt 0 ]; do
    case $1 in
    -e | --env)
        if [[ -f "$SCRIPT_DIR/compose.$2.yml" ]]; then
            composeFiles+=("-f $SCRIPT_DIR/compose.$2.yml")
        fi
        shift
        ;;
    -o)
        if [[ -f "$SCRIPT_DIR/compose.override.yml" ]]; then
            composeFiles+=("-f $SCRIPT_DIR/compose.override.yml")
        fi
        ;;
    --email)
        composeFiles+=("-f $SCRIPT_DIR/compose.email.yml")
        ;;
    -h | --help)
        # Display script help information# Display script help information
        ;;
    -v | --verbose)
        # Enable verbose mode
        ;;
    *)
        remainingArgs+=" $1"
        ;;
    esac
    shift
done

docker compose ${composeFiles[@]} up $remainingArgs
