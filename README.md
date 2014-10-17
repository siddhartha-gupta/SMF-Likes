[Like Posts](http://custom.simplemachines.org/mods/index.php?mod=3708)

This mod provides and interface to like/unlike posts.

***Features***
- Users can like posts within a topic.
- Whereas on message index only the first post of topic is taken into consideration. One can like first post of topic from message index also.
- A notification system, where users can see who has liked what post in real time.
- Total no of likes user has received so far is shown in post displays.
- In profile section one can see the likes given and received by a user so far.
- A complete dedicated admin interface, to control various features of mods
- Interface to see various stats related with the mod. The section can be accessed using the 'Like stats' button provided in the website navgation

***Admin interface***

*General Settings*
- Enable disable the mod with a single click
- Enable disable like stats completely
- Enable disable like posts notification for everyone
- Number of likes to show at once in user profiles
- Number of likes to show at once in notification panel
- Whether to show like button on message index or not

*Permission Settings*
- Enable the permission "Can like posts" for those groups who can like the posts.
- Enable the permission "Can view like" for those groups who can view who liked which post.
- Enable the permission "Can view likes of other users in their profiles" for those groups who can view like summary of other users in there profiles.
- Enable the permission "Can view stats of liked posts" for those groups who can view like posts stats
- Enable the permission "Can view notifications of liked posts" for those groups who can view like posts notifications
- Enable the permissions for guests about where they can see likes i.e in posts/topics, boards, profiles & whether or not they can see the liked posts stats

*Board Settings*
- Select on which boards you want to enable the mod
- If you want to select all boards in a category, just click on the category name

*Recount Like Stats*
- To remove the deleted messages from likes count and stats
- To recount the total likes user has received so far. One should run this once a month at least.


**Note** - If you are upgrading from version < 1.2, please recount the total like of the users from:
My Community » Administration Center » Like Posts  » Recount Like stats


**Change Log**

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


All suggestions related to core features and UI are most welcomed.

[GitHub Link](https://github.com/Joker-SMF/SMF-Likes)

*All images/css used in the mod falls under the license used below.*


**License**
 * This SMF Modification is subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this SMF modification except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
