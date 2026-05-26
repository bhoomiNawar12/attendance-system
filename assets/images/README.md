# Images

Save logos, avatars, and course photos here.

Stitch exports currently use remote Google CDN URLs. For production:

1. Download images from the Stitch HTML `<img src="...">` tags
2. Save under `assets/images/` (e.g. `logo.png`, `hero-campus.jpg`)
3. Update HTML to use relative paths: `../../assets/images/logo.png`
