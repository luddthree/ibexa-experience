#!/bin/bash

function usage {
    echo "usage test_racecondition.sh hostname"
    echo -e "\n\nVariables FASTLY_KEY and FASTLY_SERVICE_ID needs to be set"
    exit 1
}

if [ "$1" == "" ]; then
    usage
    exit 1
fi

if [ "$FASTLY_KEY" == "" ]; then
   usage
   exit 1
fi

if [ "$FASTLY_SERVICE_ID" == "" ]; then
   usage
   exit 1
fi

TEST_HOSTNAME=$1

# In order to make test work
# - enable purge per url in .vcl
# - add datacenter to vcl_hash():
#    sub vcl_hash {
#        set req.hash += req.http.host;
#        set req.hash += req.url;
#        set req.hash += server.datacenter;
#        return(hash);
#    #FASTLY hash
#    }
# - Add in sub vcl_fetch()
#+    set beresp.http.X-Now = now;
#    (...)
#    if (req.backend.is_origin) {
#        # We're on the shield
#+        set beresp.http.X-Now-Shield = now;
#


#    151.101.112.204 for HHN (Frankfurt)
#    151.101.12.204 for FRA (Frankfurt)
#    151.101.236.204 for OSL (Oslo)
#    151.101.84.204 for BMA (Stockholm)

# Edge should be Oslo
# Shield should be Stockholm

# purge commands:
# curl --resolve $TEST_HOSTNAME:80:151.101.84.204 -XPURGE http://$TEST_HOSTNAME/ ; # stockholm
# curl --resolve $TEST_HOSTNAME:80:151.101.236.204 -XPURGE  http://$TEST_HOSTNAME/ ; # oslo

# purge oslo hard, fetch oslo
# curl --resolve $TEST_HOSTNAME:80:151.101.236.204 -XPURGE  http://$TEST_HOSTNAME/ ; curl -IXGET --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/
# Same for stockholm:
# curl --resolve $TEST_HOSTNAME:80:151.101.84.204 -XPURGE http://$TEST_HOSTNAME/ ; curl -IXGET  --resolve $TEST_HOSTNAME:80:151.101.84.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ;curl -IXGET  --resolve $TEST_HOSTNAME:80:151.101.84.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/

# soft purge commands:
# curl --resolve $TEST_HOSTNAME:80:151.101.84.204 -XPURGE --header "fastly-soft-purge: 1" http://$TEST_HOSTNAME/ ; # stockholm
# curl --resolve $TEST_HOSTNAME:80:151.101.236.204 -XPURGE --header "fastly-soft-purge: 1" http://$TEST_HOSTNAME/ ; # oslo

# curl fetch command
# curl -IXGET  --resolve $TEST_HOSTNAME:80:151.101.84.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # stockholm
# curl -IXGET --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # oslo


echo clearing all cache
#./bin/console fos:httpcache:invalidate:tag ez-all
curl -XPOST --header "Fastly-Key: $FASTLY_KEY" --header "Accept: application/json" https://api.fastly.com/service/$FASTLY_SERVICE_ID/purge/ez-all
echo -e "\nSleeping 5 seconds to wait for purge to propagate"
sleep 5

echo warming cache, twice to be sure
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.84.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # stockholm
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.84.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # stockholm
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.236.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # oslo
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.236.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # oslo

echo "#### Starting test in 2 sec..."
sleep 2

echo Purging edge
curl --resolve $TEST_HOSTNAME:80:151.101.236.204 -XPURGE --header "fastly-soft-purge: 1" --header "Fastly-Key: $FASTLY_KEY" http://$TEST_HOSTNAME/ ; # oslo

echo "Fetching from edge #1"
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.236.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # oslo
sleep 1;

echo "Fetching from edge #2"
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.236.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # oslo
sleep 1;

echo "Fetching from edge #3"
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.236.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # oslo
sleep 1;

echo "Fetching from edge #4"
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.236.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # oslo
sleep 1;

echo "Fetching from edge #5"
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.236.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # oslo
sleep 1;

echo "Fetching from edge #6"
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.236.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # oslo
sleep 1;

echo "Fetching from edge #7"
curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.236.204 --header "Fastly-Debug: 1" http://$TEST_HOSTNAME/ ; # oslo
sleep 1;

echo "Now, run the following command approximately every second"
echo -e "You\'ll notice that X-Now header changes approximately every 5 seconds (don't mix it with X-Now-Shield which doesn't change)\n"
echo -e "You can already see this behaviour above as X-Now for #4 should be different from #7\n\nCommand:"
echo curl -IXGET --resolve $TEST_HOSTNAME:80:151.101.236.204 --header '"'"Fastly-Debug: 1"'"' http://$TEST_HOSTNAME/ ; # oslo
