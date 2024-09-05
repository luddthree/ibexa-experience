// This code should be added a snippet in your config:
//  Name : Re-Enable shielding on restart
//  Priority: 100
//  Type: recv
//
// Fastly CLI :
// - fastly vcl snippet create --name="Re-Enable shielding on restart" --version=active --autoclone --priority 100 --type recv --content=vendor/ibexa/fastly/fastly/snippet_re_enable_shielding.vcl
// - fastly service-version activate --version=latest


set var.fastly_req_do_shield = (req.restarts <= 2);

# set var.fastly_req_do_shield = (req.restarts > 0 && req.http.accept == "application/vnd.fos.user-context-hash");
set req.http.X-Snippet-Loaded = "v1";

