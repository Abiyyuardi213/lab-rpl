/**
 * Lab RPL - Robust Service Worker
 * 
 * Strategy:
 * 1. Bypass all navigation requests (HTML pages) to let the browser handle redirects (fixes Login/Auth issues).
 * 2. Cache only static assets (images, manifest) for basic PWA functionality.
 * 3. Automatic cache cleanup on version updates.
 */

const CACHE_NAME = "lab-rpl-v1.3"; // Increment version to force update
const STATIC_ASSETS = [
    "/image/rplmini.png",
    "/manifest.json",
];

// 1. Install Event: Cache essential assets
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// 2. Activate Event: Clean up old caches
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// 3. Fetch Event: Handle requests
self.addEventListener("fetch", (event) => {
    // Skip non-GET requests (like Login POSTs)
    if (event.request.method !== "GET") return;

    // CRITICAL FIX: Bypass Service Worker for navigation requests (HTML pages).
    // This allows the browser to handle Laravel redirects (like Login) normally,
    // avoiding the "redirected response" network error.
    if (event.request.mode === "navigate") {
        return; // Let the browser handle this defaultly
    }

    // For other assets (CSS, JS, Images), try Cache first, then Network
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(event.request).then((networkResponse) => {
                // Return network response as is
                return networkResponse;
            }).catch(() => {
                // If network fails (offline), and it's not in cache, let it fail
                // We're not catching pages here, so no need for an offline fallback yet
            });
        })
    );
});


