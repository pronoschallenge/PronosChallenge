/**
 * \file preloadimages.js
 * PreloadImages class definition. Preloads one or several images at once.
 *
 * Copyright (c) 2004-2005 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A.
 * All rights reserved.
 *
 * $Id: preloadimages.js 1193 2005-12-13 22:46:41Z alex $
 */

/**
 * \internal Constructor.
 *
 * \param objArgs [object] object properies.
 */
Zapatec.ImagePreloader = function(objArgs) {
  // Zapatec.PreloadImages object
  this.job = null;
  // Image object
  this.image = null;
  // Initialize
  if (arguments.length > 0) this.init(objArgs);
};

/**
 * \internal Initializes object.
 *
 * \param objArgs [object] initialization object:
 * {
 *   job: [object] Zapatec.PreloadImages object,
 *   url: [string] image URL,
 *   timeout: [number, optional] number of milliseconds to wait for onload
 *    event before forcing it
 * }
 */
Zapatec.ImagePreloader.prototype.init = function(objArgs) {
  // Check arguments
  if (!objArgs || !objArgs.job) {
    return;
  }
  // Attach to PreloadImages
  this.job = objArgs.job;
  // Create new Image
  this.image = new Image();
  this.job.images.push(this.image);
  var self = this;
  this.image.onload = function() {
    self.job.loadedUrls.push(objArgs.url);
    self.onLoad();
  };
  this.image.onerror = function() {
    self.job.invalidUrls.push(objArgs.url);
    self.onLoad();
  };
  this.image.onabort = function() {
    self.job.abortedUrls.push(objArgs.url);
    self.onLoad();
  };
  this.image.src = objArgs.url;
  if (typeof objArgs.timeout == 'number') {
    setTimeout(function() {
      if (self.job) {
        // Onload didn't fire yet
        self.job.abortedUrls.push(objArgs.url);
        self.onLoad();
      }
    }, objArgs.timeout);
  }
};

/**
 * \internal Image onload event handler.
 */
Zapatec.ImagePreloader.prototype.onLoad = function() {
  // Remove handlers to prevent further calls
  this.image.onload = null;
  this.image.onerror = null;
  this.image.onabort = null;
  // Reduce counter
  this.job.leftToLoad--;
  // If this was last image
  if (this.job.leftToLoad == 0 && typeof this.job.onLoad == 'function') {
    // We don't need onload handler any more after last image was loaded
    var funcOnLoad = this.job.onLoad;
    this.job.onLoad = null;
    // onLoad callback
    funcOnLoad(this.job);
  }
  // Detach from PreloadImages
  var objJob = this.job;
  this.job = null;
};

/**
 * Constructor.
 *
 * \param objArgs [object] object properies.
 */
Zapatec.PreloadImages = function(objArgs) {
  // Array of Image objects
  this.images = [];
  // Counter to know when all images are loaded
  this.leftToLoad = 0;
  // Array of successfully loaded URLs
  this.loadedUrls = [];
  // Array of invalid URLs
  this.invalidUrls = [];
  // Array of aborted URLs
  this.abortedUrls = [];
  // Onload event handler
  this.onLoad = null;
  // Initialize
  if (arguments.length > 0) this.init(objArgs);
};

/**
 * \internal Initializes object.
 *
 * onLoad callback function will be called in any case, even if errors occured
 * during loading or loading process was aborted. onLoad callback function
 * receives Zapatec.Transport.PreloadImages object. Its various properies can
 * be used:
 * {
 *   images: [object] array of Image objects,
 *   loadedUrls: [object] array of successfully loaded URLs,
 *   invalidUrls: [object] array of URLs that were not loaded due to errors,
 *   abortedUrls: [object] array of URLs that were not loaded due to abort,
 *   leftToLoad: [number] how many images left to load if event was forced
 * }
 *
 * If onLoad event doesn't fire during long period of time, it can be forced
 * using "timeout" argument.
 *
 * \param objArgs [object] initialization object:
 * {
 *   urls: [object] array of absolute or relative image URLs to preload,
 *   onLoad: [function, optional] onload event handler,
 *   timeout: [number, optional] number of milliseconds to wait for onload
 *    event before forcing it
 * }
 */
Zapatec.PreloadImages.prototype.init = function(objArgs) {
  // Check arguments
  if (!objArgs) {
    return;
  }
  if (!objArgs.urls || !objArgs.urls.length) {
    if (typeof objArgs.onLoad == 'function') {
      // onLoad callback
      objArgs.onLoad(this);
    }
    return;
  }
  // Run job
  this.images = [];
  this.leftToLoad = objArgs.urls.length;
  this.loadedUrls = [];
  this.invalidUrls = [];
  this.abortedUrls = [];
  this.onLoad = objArgs.onLoad;
  // Go through URLs array
  for (var iUrl = 0; iUrl < objArgs.urls.length; iUrl++) {
    // Preload URL
    new Zapatec.ImagePreloader({
      job: this,
      url: objArgs.urls[iUrl],
      timeout: objArgs.timeout
    });
  }
};
