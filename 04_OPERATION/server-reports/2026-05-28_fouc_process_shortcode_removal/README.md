# Drycured FOUC / Process Page Shortcode Removal — 2026-05-28

Status: PASS

Purpose:
Investigate and reduce frontend flash / double-render behavior on drycured.com, especially on the main process page.

Finding:
The main process page /proces-izrade/ contained the shortcode:
[drycured_home_process_rail]

At the same time, the MU-plugin drycured-process-overview.php automatically appended the modern process overview block:
[drycured_process_overview] / dcpo-wrap

This caused two process display layers to appear in the initial page source:
- dc-process-rail
- dcpo-wrap

Action:
Removed only the manual [drycured_home_process_rail] shortcode from page ID 2864, "Proces izrade".

No plugin code was changed in this step.

Validation after removal:
- dc-process-rail count: 0
- dcpo-wrap count: 7
- Home returned 200 OK
- /proces-izrade/ returned 200 OK
- sitemap.xml returned 200 OK
- Recepti returned 200 OK
- Alati returned 200 OK
- wp-json returned 200 OK

Conclusion:
The main double-render source on /proces-izrade/ was removed. The process page now keeps its overview block as the canonical process page display.

Rollback:
Restore previous page content from:
process_2864_post_content_before_shortcode_removal.html

or re-add:
[drycured_home_process_rail]

to page ID 2864 if needed.
