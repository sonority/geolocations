What does it do?
^^^^^^^^^^^^^^^^

This extension shows location-records on a google-map. It provides a search-plugin for radialsearch (Umkreissuche) and fulltextsearch.
There is also a simple backend-module to autogenerate coordinates from addresses of all your location-records (import-module is planned too).

The FE-plugins and their features are:

**The map-plugin**

- You can use your own styles (create awesome styles with http://googlemaps.github.io/js-samples/styledmaps/wizard/index.html)
- Enable/disable scrollwheel and dragging (also switchable with a button on the map)
- Set initial zoomlevel
- Set initial coordinates

**The search-plugin**

- You can use the address-autocompleter and/or automatic geolocation/positioning
- The search provides a radialsearch and/or a fulltextsearch (the fields are configurable in typoscript)
- The radius for the radialsearch can be configured directly in the FE-plugin
- Show a perimeter on the map (radialsearch)
- Show a category-selector

**The list-plugin (mandatory)**

- The list plugin contains all the records which were found with radialsearch/fulltextsearch
- Initially it can display a set of specific locations or locations from specific categories
- If a location item is clicked you can choose the actions "show in map" or "unveil"

The search is done with AJAX and puts the results in the list
You can also use simple categories and set a marker-icon for it.
The google-map is also integrated into the backend-forms, where you can automatically set or correct the geoposition.