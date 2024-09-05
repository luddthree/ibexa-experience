
include "ez_user_hash.vcl"

sub vcl_recv {

    if (req.method == "FASTLYPURGE") {
      // Deny unauthenticated single purges
      set req.http.Fastly-Purge-Requires-Auth = "1";
    }

    // Prevent external requests to send this header.
    if(fastly.ff.visits_this_service == 0 && req.http.x-get-fresh-content){
      unset req.http.x-get-fresh-content;
    }

    /* Preventing race condition where edge might receive purge requests before shield
       https://developer.fastly.com/learning/concepts/purging/#race-conditions
       See rest of implementation in vcl_miss
    */
    // If the request is coming from another Fastly node and it's a `Get Fresh Content` type,
    // force a MISS so we get brand new content.
    if(fastly.ff.visits_this_service > 0 && req.http.x-get-fresh-content){
      unset req.http.x-get-fresh-content;
      set req.hash_always_miss = true;
    }

    // Advertise Symfony for ESI support
    set req.http.Surrogate-Capability = "abc=ESI/1.0";

    // Varnish, in its default configuration, sends the X-Forwarded-For header but does not filter out Forwarded header
    unset req.http.Forwarded;

    // Preserve X-Forwarded-For in all requests.
    if (fastly.ff.visits_this_service == 0) {
      if (req.http.X-Forwarded-For) {
        set req.http.Fastly-Temp-XFF = req.http.X-Forwarded-For ", " client.ip;
      } else {
        set req.http.Fastly-Temp-XFF = client.ip;
      }
    }
    else {
      set req.http.Fastly-Temp-XFF = req.http.X-Forwarded-For;

      /* if shielding is enabled, the code below is required */
      /* This prevents shield from delivering stale content (if it is expired or soft-purged) to edge
       https://developer.fastly.com/learning/concepts/stale#shielding-considerations */
      set req.max_stale_while_revalidate = 0s;
    }

    # Disable ESI at the shield or for ESI subrequests from certain level
    if (fastly.ff.visits_this_service > 0 || req.esi_level > 3) {
      set req.esi = false;
    }

    # Still accept compression for requests that cannot contain ESI tags or be loaded as ESI fragments
    if (req.url.ext !~ "(?i)^(?:css|js|wof|woff|woff2|ico|svg)$") {
      unset req.http.Accept-Encoding;
    }

    unset req.http.X-Snippet-Loaded;

#FASTLY recv

    // Check that vcl snippet has been added to config
    if (req.http.X-Snippet-Loaded == "v1") {
        unset req.http.X-Snippet-Loaded;
    } else {
        error 555;
    }

    if (req.http.Authenticate || req.http.Authorization) {
      if (req.request != "HEAD" && req.request != "GET" && req.request != "FASTLYPURGE") {
        return(pass);
      }
      return(lookup);
    }

    // Remove all cookies besides Session ID, as JS tracker cookies and so will make the responses effectively un-cached
    if (req.http.cookie) {
        set req.http.cookie = ";" + req.http.cookie;
        set req.http.cookie = regsuball(req.http.cookie, "; +", ";");
        set req.http.cookie = regsuball(req.http.cookie, ";(eZSESSID[^=]*)=", "; \1=");
        set req.http.cookie = regsuball(req.http.cookie, ";(ibexa-[^=]*)=", "; \1=");
        set req.http.cookie = regsuball(req.http.cookie, ";[^ ][^;]*", "");
        set req.http.cookie = regsuball(req.http.cookie, "^[; ]+|[; ]+$", "");

        if (req.http.cookie == "") {
            // If there are no more cookies, remove the header to get page cached.
            unset req.http.cookie;
        }
    }

    if(req.url.ext ~ "^(?:css|js|gif|jpe?g|bmp|png|tiff?|ico|img|tga|wmf|svg|swf|ico|mp3|mp4|m4a|ogg|mov|avi|wmv|zip|gz|pdf|ttf|eot|wof)$") {
      if (req.request != "HEAD" && req.request != "GET" && req.request != "FASTLYPURGE") {
        return(pass);
      }
      return(lookup);
    }

    // Sort the query string for cache normalization.
    set req.url = boltsort.sort(req.url);

    // Retrieve client user context hash and add it to the forwarded request.
    call ez_user_context_hash;

  if (req.request != "HEAD" && req.request != "GET" && req.request != "FASTLYPURGE") {
    return(pass);
  }

  return(lookup);
}

sub vcl_fetch {
    set beresp.http.X-Now = now;
    declare local var.x-lang-pattern STRING;

    /* Preventing race condition where edge might receive purge requests before shield
       https://developer.fastly.com/learning/concepts/purging/#race-conditions
    */
    if (req.backend.is_origin) {
        # We're on the shield
        set beresp.http.X-Now-Shield = now;
        set beresp.http.X-Shield-Generation = req.vcl.generation;
    } else {
        # We're on the edge
        declare local var.local INTEGER;
        set var.local = std.atoi(req.vcl.generation);
        if (std.atoi(beresp.http.X-Shield-Generation) < var.local) {
            set beresp.ttl = 1s;
        }
        set beresp.http.X-Edge-Generation = req.vcl.generation;
    }

    /* handle 5XX (or any other unwanted status code) */
    if (beresp.status >= 500 && beresp.status < 600) {
        /* deliver stale if the object is available */
        if (stale.exists) {
            return(deliver_stale);
        }

        if (req.restarts < 1 && (req.request == "GET" || req.request == "HEAD")) {
            restart;
        }

        /* else go to vcl_error to deliver a synthetic */
        error 503;
    }

    if (bereq.http.accept ~ "application/vnd.fos.user-context-hash"  && beresp.status >= 500 ) {
        error beresp.status "Internal Server Error";
    }

    // Check for ESI acknowledgement
    if (beresp.http.Surrogate-Control ~ "ESI/1.0") {
        if (beresp.http.x-esi-received-from) {
            set beresp.http.x-esi-received-from = beresp.http.x-esi-received-from "," req.backend;
        }
        else {
            set beresp.http.x-esi-received-from = req.backend;
        }

        // ESI will stop working once a revalidation based on these headers happens, so let's remove them
        unset beresp.http.ETag;
        unset beresp.http.Last-Modified;

        // Note: ESI will only be processed if req.esi = true
        // That is controlled in vcl_recv() to ensure ESI processing is only done on edge
        esi;
    }

    // Modify Surrogate-Key header to add translation suffix
    if (beresp.http.Surrogate-Key && beresp.http.x-lang) {
        set var.x-lang-pattern = "\1" + beresp.http.x-lang;
        set beresp.http.Surrogate-Key = beresp.http.Surrogate-Key + " " +  regsuball(beresp.http.Surrogate-Key, "(\S+)", var.x-lang-pattern);
    }

#FASTLY fetch

  if ((beresp.status == 500 || beresp.status == 503) && req.restarts < 1 && (req.request == "GET" || req.request == "HEAD")) {
    restart;
  }

  if (req.restarts > 0) {
    set beresp.http.Fastly-Restarts = req.restarts;
  }

  if (beresp.http.Set-Cookie) {
    return(pass);
  }

  if (beresp.http.Cache-Control ~ "no-cache") {
    return(pass);
  }

  if (beresp.http.Cache-Control ~ "private") {
    return(pass);
  }

  set beresp.stale_if_error = 86400s;
  set beresp.stale_while_revalidate = 300s;
  if (beresp.http.Expires || beresp.http.Surrogate-Control ~ "max-age" || beresp.http.Cache-Control ~ "(s-maxage|max-age)") {
    # keep the ttl here
  }
  else {
    # apply the default ttl
    set beresp.ttl = 3600s;
  }

  return(deliver);
}

sub vcl_hit {
#FASTLY hit

  if (!obj.cacheable) {
    return(pass);
  }
  return(deliver);
}

sub vcl_miss {
  // If we're doing a SWR background fetch towards the shield, for the shield
  // to do a MISS
  if(req.is_background_fetch && req.backend.is_shield){
    set bereq.http.x-get-fresh-content = "1";
  }

#FASTLY miss
  return(fetch);
}

sub vcl_deliver {
    // https://developer.fastly.com/learning/concepts/purging/#race-conditions
    if (fastly.ff.visits_this_service == 0 && !req.http.Fastly-Debug) {
        unset resp.http.X-Edge-Generation;
        unset resp.http.X-Shield-Generation;
    }

    if (resp.status >= 500 && resp.status < 600) {
        /* restart if the stale object is available */
        if (stale.exists) {
            restart;
        }
    }

    // We'll only fetch the user-context-hash and do a restart if we are on the edge.
    if (fastly.ff.visits_this_service == 0) {
        // On receiving the hash response, copy the hash header to the original
        // request and restart.
        // Workaround for Fastly bug , req.restarts is not reset when handling the first ESI for a given page:
        if (((req.restarts == 0 || (req.restarts == 1 && req.is_esi_subreq))) && resp.http.content-type ~ "application/vnd.fos.user-context-hash") {
            set req.http.x-user-context-hash = resp.http.x-user-context-hash;

            restart;
        }

        // If we get here, we are still trying to fetch x-user-context-hash
        // but the origin replied with a redirect.
        // Let's redirect them to their original req.url
        if (req.restarts == 0 && resp.status == 301 && req.http.x-fos-original-url) {
            set resp.http.location = regsub(resp.http.location, "/_fos_user_context_hash", req.http.x-fos-original-url);
        }

        // If we get here, this is the real response that is sent to the client.

        // Remove the vary on user context hash, this is not public. Keep all
        // other vary headers.
        if (resp.http.Vary ~ "X-User-Context-Hash") {
            set resp.http.Vary = regsub(resp.http.Vary, "(?i),? *X-User-Context-Hash *", "");
            set resp.http.Vary = regsub(resp.http.Vary, "^, *", "");
            if (resp.http.Vary == "") {
                unset resp.http.Vary;
            }

            // If we vary by user hash, we'll also adjust the cache control headers going out by default to avoid sending
            // large ttl meant for Varnish to shared proxies and such. We assume only session cookie is left after vcl_recv.
            if (req.http.cookie) {
                // When in session where we vary by user hash we by default avoid caching this in shared proxies & browsers
                // For browser cache with it revalidating against Varnish, use for instance "private, no-cache" instead
                set resp.http.cache-control = "private, no-cache, no-store, must-revalidate";
            } else if (resp.http.cache-control ~ "public") {
                // For non logged in users we allow caching on shared proxies (mobile network accelerators, planes, ...)
                // But only for a short while, as there is no way to purge them
                set resp.http.cache-control = "public, s-maxage=600, stale-while-revalidate=300, stale-if-error=300";
            }
        }

        // Sanity check to prevent ever exposing the hash to a non debug client.
        unset resp.http.x-user-context-hash;

        if (req.http.Fastly-Debug) {
            set resp.http.X-State = fastly_info.state;
        }
    }

    if (fastly.ff.visits_this_service == 0) {
        unset resp.http.Surrogate-Control;

        if (!req.http.Fastly-Debug) {
            unset resp.http.X-Now-Shield;
            unset resp.http.X-Now;
        }
    }

#FASTLY deliver
  return(deliver);
}

sub vcl_error {
#FASTLY error
    if (obj.status == 555) {
        synthetic {"<!DOCTYPE html><html><h1>Oops! An Error Occurred</h1><h2>Fastly misconfigured. Snippet missing from config, or wrong snippet version loaded</h2></html>"};
        return(deliver);
    }

    /* handle 503s */
    if (obj.status >= 500 && obj.status < 600) {
        /* deliver stale object if it is available */
        if (stale.exists) {
            return(deliver_stale);
        }

        /* otherwise, return a synthetic */

        /* include your HTML response here */
        synthetic {"<!DOCTYPE html><html><h1>Oops! An Error Occurred</h1><h2>Back-end server is faulty or not available</h2></html>"};
        return(deliver);
    }
}

sub vcl_pass {
#FASTLY pass
}

sub vcl_log {
#FASTLY log
}
