 # Administration language tweaks
 
 ## Overview
 
 The administration language should be applied on:
 
 - backend
    - all paths having the option **_admin_route**
    - node/*/edit pages
    - TODO: list other elements which should use administration language
    
 - frontend
    - contextual links
    - admin toolbar
    - TODO: list other elements which should use administration language
 
 ## Setup
 
 1. You may need to apply to following patches.
 
     ```
     "extra": {
        "patches": {
          "drupal/core": {
            "Language negotiaton fix": "https://www.drupal.org/files/issues/2189267-53.patch",
            "Account administration pages' language negotiation causes 'access denied' in toolbar subtree caching": "https://www.drupal.org/files/issues/2868193-10.patch",
            "Admin toolbar should always be rendered in the admin language": "https://www.drupal.org/files/issues/2313309-40.patch"
          }
        }
     }
     ```
 
 2. Configurations:
 
    2.1. Configure the language detection /admin/config/regional/language/detection
      
      Interface text language detection:
        
        1. Administration language
        2. URL
      
      Content language detection:
      
        1. URL
    
    2.2. Configure the administration language /admin/config/regional/language/detection/administration_language

    PATHS

    - add path /contextual/render for contextual links, the path is loaded via ajax by calling "/contextual/render"
    
      Hint: The contextual links are cached via the browser Session Storage, clear all Drupal.contextual.*

    ADMIN ROUTES

    - Enable to automatically apply on routes with option _admin_route
 