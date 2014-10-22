**Change Log**

**Version 2.0.1**
- Fix for total likes not appearing
- Fix for method accessing issue via action hook
- Fix for like button text not changing
- Added admin function to remove duplicate like entries from DB
- Fixes for upgrade cleaning out the mod settings


**Version 2.0**
- PHP files converted to OOP, easy code management and readability
- JS file converted to singleton and compressed, in layman words, its more efficient and fast
- All inline css removed from JS and templates
- Likes text based on number of likes, [reference url](http://www.simplemachines.org/community/index.php?topic=506743.msg3746658#msg3746658)
- Handling for likes data when post is deleted/merged/move, [reference url](http://www.simplemachines.org/community/index.php?topic=506743.msg3748949#msg3748949)
- Like posts permission based on post count group, [reference url](http://www.simplemachines.org/community/index.php?topic=506743.msg3749117#msg3749117)
- New permission in admin panel to disbale like stats completely
- Permission to disbale notification for eveyone or based on user group
- In admin panel, new feature to optimize like tabel and remove likes from delete topic
- Some hidden/fun JS features added in admin panel
- Permissions fixes through out the codebase
- Tons of fixes


*Version 1.6.1*
- Fixes for UTF8 encoding on notifications
- Fixes for notification panel for smart devices
- Select all board functionality
- Persist settings on mod update
- Acknowledging contributors


*Version 1.6*
- Added new permission to enable disable mod for specific boards
- Fix for notification UTF8 chars
- Fixes for mouse/hover events
- Like/unlike css classes separated out
- Missing text strings added. Thanks to emanuele45


*Version 1.5.2*
- Fixes for jquery version compare function
- Fix for avatar not loading:#18


*Version 1.5.1*
- Much better handling for jQuery loading
- Like posts info section scrolling and height fixes
- Fixes for strpos error


*Version 1.5*
- Added a completed new module to showcase the like stats
- Added button to close the like posts notification popup
- Disabled multiple like/unlike ajax requests simultaneously
- Removed extra data & variables accessed/used in DB queries
- Various bug fixes


*Version 1.4*
- Permissions for guests added
- Guests can view likes across various sections, depending on the permissions provided by the admin


*Version 1.3.1*
- Fresh mod installation issue fixed
- Fix for notification panel z-index


*Version 1.3*
- Added a complete notification system for the mod
- Fix and enhancements across the mod


*Version 1.2.1*
- Fixes for jQuery loding issue
- Fix for incorrect like count in the boards (Message Index)

*Version 1.2*
- Support for PHP < 5.2
- Show total likes a user has receieved in posts
- Admins can recount total likes user has received
- Various bug fixing and improvements
- Fix to support other language strings


*Version 1.1.1*
- Majorly address bug fixing
- jQuery noConflict mode implemented
- Check whether jQuery is loaded or not
- Fix to support other language strings


*Version 1.1*
- To show posts user has liked in profile
- To show posts liked by others in a users profile
- visual feedback on like/dislike click
- fixes in admin panel permission system
- various bug fixes