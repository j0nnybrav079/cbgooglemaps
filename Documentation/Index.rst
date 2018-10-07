=========
Quick Map
=========


What does it do?
================
This “Quick Maps” extension provides the possibility to include maps by GoogleMaps, MapBox or OpenStreetMap to your frontend in a quick and easy way.
|

GoogleMaps licence change?
==========================
Due to the license change for the use of GoogleMaps, especially the obligation to specify a payment form for the developer account, I decided to extend this extension in such a way that maps from other services (OpenStreetMap and MapBox) can now also be used.
|

API-Key / AccessToken?
======================
API-Key or AccessToken only required for GoogleMaps and MapBox!
|

1) GoogleMaps needs an API-Key:
	See: https://cloud.google.com/maps-platform/  for further information and requesting an api-key
2) MapBox needs an access token:
	See: https://www.mapbox.com/install/  for further information and requesting an access token


Quick update from GoogleMaps to OpenStreetMap or MapBox:
========================================================
*ATTENTION: If you use custom templates for this extension, you have to update them if you want to use another map provider than GoogleMaps*
|

1. install the new version 4.x via extension manager
2. switch to the desired map provider in the constant editor (GoogleMaps, MapBox or OpenStreetMap)
3. In the constant editor, enter the previously requested GoogleMaps API key or MapBox AccessToken.
4. Clear cache (frontend cache & general cache required or clear whole cache in the install tool)
5. Finished

Please excuse that the documentation is not yet available in the "new" restructuredText format. As soon as I find some time I'll take care of it!
The previous documentation is still available within the extension as an ODT and PDF file.

