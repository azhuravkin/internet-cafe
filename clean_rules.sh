#!/bin/bash
#
# Delete rules in this format:
# -A CLIENTS -m mac --mac-source 00:11:22:33:44:55 -m comment --comment "1296842113" -j ACCEPT
# if timestamp, saved as comment, is expired.

PATH=/bin:/sbin:/usr/bin:/usr/sbin

EXPIRED=$(($(date +%s) - 86400))

clean_table() {
    iptables-save -t $1 | grep '\-A CLIENTS' | while read LINE
    do
	TIMESTAMP=$(echo $LINE | awk '{print $10}' | sed 's/\"//g')

	if [ $EXPIRED -gt $TIMESTAMP ]; then
	    CMD=$(echo $LINE | sed 's/^\-A/-D/')
	    eval iptables -t $1 $CMD
	fi
    done
}

clean_table "nat"
clean_table "filter"
