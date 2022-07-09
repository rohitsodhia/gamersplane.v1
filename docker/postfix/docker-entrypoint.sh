#/bin/bash

# Run dockerize and template file main.cf.tmpl into main.cf
# then start postfix as child process
dockerize postfix start-fg
