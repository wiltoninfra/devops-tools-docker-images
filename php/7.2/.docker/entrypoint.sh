#!/bin/bash

set -e 

  #sleep 2
  exec supervisord -n -c /etc/supervisord.conf