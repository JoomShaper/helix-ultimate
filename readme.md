# Helix Ultimate V2.0.0

This is a major version of **Helix Ultimate** introduces a lot of improvements.

## Installation process:

1. Install a fresh **Joomla!**
2. Open terminal in the root directory of the **Joomla!** project.
3. Run `git init`
4. Run `git remote add origin git@github.com:JoomShaper/helix-ultimate.git` Or If you are not using **SSH** then run `git remote add origin https://github.com/JoomShaper/helix-ultimate.git`
6. Run the command `git fetch`
7. Run `git checkout 2.0.0-rc.2`
8. Run `cd plugins/system/helixultimate`
9. Run `composer install`
10. Run `composer dump-autoload -o`
11. Login to administrator panel.
12. Go to `Extensions > Manage > Discover`
13. Install `shaper_helixultimate` template and
   `System - Helix Ultimate Framework` plugin.
14. Go to `Extensions > Plugins` and search for the plugin
   `System - Helix Ultimate Framework` and enable it.
