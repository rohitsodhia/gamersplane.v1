#/bin/bash

# Run dockerize and template file main.cf.tmpl into main.cf
# then start postfix as child process
dockerize -template /etc/postfix/main.cf.tmpl:/etc/postfix/main.cf postfix start-fg
