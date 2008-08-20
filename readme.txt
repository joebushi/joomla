Hello and welcome to my testing branch.

You are maybe here because you are interested in what I'd like to see in Joomla 1.6. In this branch I'm trying to get the basic stuff of the features that I've been working on functional and then show it to the rest of the development working group in the hope that we will agree on bringing this into 1.6 and working out the kinks together. :-)

Whats in this branch?
- ACL
- new JParameter/JForm
- new Template Parameter system
- refactored voting plugin
- tinymce 3.1.1
- category parameters
- subcategory feature for com_categories
- article link plugin
- category layout selector for the category view after standard section layout
- userprofile

ACL
ACL is one of the most requested features for Joomla. The changes for this are mainly done in /libraries/joomla/user/authorization/authorization.php and the authorization subfolder in terms of classes, the admin interface is done in com_users. Additional changes are done to the JHTMLList::accesslist() function and to the frontend components. We have some small changes in the SQL queries to implement this.
This feature is already working, the UI is a WIP.

new JParameter/JForm
This class allows you to create forms, but also to output tables of data controlled through an XML. The most important feature however is the ability to dynamically extend parameter sets. With this feature most of the core components have been changed to allow for dynamic parameter sets. The interesting stuff happens in /libraries/joomla/html/form.php, an example can be found in the folder of the com_users component in the backend.

Voting plugin
This plugin now works with the article parameters and does not need another table in the system. This also allows to enable the plugin for single articles only inside a blog category layout.

TinyMCE 3.1.1
Its an update. Should work better with IE and Safari.

Category Parameters
This uses the new features of the JParameter class and allows to set several options for categories in all components using this feature.

Subcategory feature
This one is a proof of concept and most likely not really usefull. Its a quick patch to allow to use this in com_weblinks.

Article Link Plugin
Allows to link to another article from inside an article, similar to the image button.

Category layout selector
You can now select which layout to use when coming from a standard section list layout.

Userprofile
Allows to create simple, extendable user profiles, controlled through XML files.

Feel free to give me feedback. :-)