#!/usr/bin/env bash

/usr/bin/rsync --human-readable \
--compress \
--rsh=ssh \
--times \
--owner \
--group \
--info=progress2 \
--ignore-times \
--links  \
--perms \
--recursive \
--size-only \
--delete \
--force \
--numeric-ids \
--stats \
-e "ssh -p 57773" \
root@j4u.pl:/home/j4uogl/domains/ogloszenia.jaslo4u.pl/public_html/images/  /home/u/PhpstormProjects/classifieds/static/listing/0000_legacy
