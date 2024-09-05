# IMPORTANT: Fastly Image Optimizer requires shielding to be enabled as well 
# See: https://developer.fastly.com/reference/io/#enabling-image-optimization

# Restrict optimizer by file path and extension
if (req.url.ext ~ "(?i)^(gif|png|jpe?g|webp)$") {
    if (req.url.path ~ "^/var/([a-zA-Z0-9_-]+)/storage/images") {
        set req.http.x-fastly-imageopto-api = "fastly";
    }
}
