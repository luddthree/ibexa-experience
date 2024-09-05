sub ez_user_context_hash {
    // We'll only deal with fetching the user context hash on the edge.
    if (fastly.ff.visits_this_service == 0) {

        // Prevent tampering attacks on the hash mechanism
        if (req.restarts == 0
            && (req.http.accept ~ "application/vnd.fos.user-context-hash"
                || req.http.x-user-context-hash
            )
        ) {
            error 400 "Bad request";
        }

        // Workaround for Fastly bug, req.restarts is not reset when handling the first ESI for a given page
        if ((req.restarts == 0 && (req.request == "GET" || req.request == "HEAD")) || (req.is_esi_subreq && !req.http.x-user-context-hash && req.restarts < 2)) {
            // Backup accept header, if set
            if (req.http.accept) {
                set req.http.x-fos-original-accept = req.http.accept;
            }
            set req.http.accept = "application/vnd.fos.user-context-hash";

            // Backup original URL
            set req.http.x-fos-original-url = req.url;
            set req.url = "/_fos_user_context_hash";

            // Force the lookup, the backend must tell not to cache or vary on all
            // headers that are used to build the hash.
            // varnish : return(hash);
            return (lookup);
        }

        // Rebuild the original request which now has the hash.
        if (req.restarts > 0 && req.http.accept == "application/vnd.fos.user-context-hash") {
            // By default, Fastly treats a restart as a failure and wont distribute hits in the PoP cluster
            // Setting Fastly-Force-Shield fixes that behaviour
            set req.http.Fastly-Force-Shield = "1";
            set req.url = req.http.x-fos-original-url;
            unset req.http.x-fos-original-url;
            if (req.http.x-fos-original-accept) {
                set req.http.accept = req.http.x-fos-original-accept;
                unset req.http.x-fos-original-accept;
            } else {
                // If accept header was not set in original request, remove the header here.
                unset req.http.accept;
            }

            // Force the lookup, the backend must tell not to cache or vary on the
            // user context hash to properly separate cached data.
            // varnish : return(hash);
            //return (lookup);
            //return (pass);
        }
    }
}
