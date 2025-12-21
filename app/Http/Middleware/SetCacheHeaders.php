<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $type  Cache type: 'static', 'api', 'none'
     */
    public function handle(Request $request, Closure $next, ?string $type = null): Response
    {
        $response = $next($request);

        // Don't cache if already has cache headers set
        if ($response->headers->has('Cache-Control')) {
            return $response;
        }

        // Don't cache authenticated requests, POST/PUT/DELETE, or HTML responses (views)
        // HTML responses need to have $errors variable available
        $isHtml = str_contains($response->headers->get('Content-Type', ''), 'text/html');
        
        if ($request->user() || !in_array($request->method(), ['GET', 'HEAD']) || $isHtml) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            return $response;
        }

        // Only set cache headers for API responses (JSON)
        if ($type === 'api' && str_contains($response->headers->get('Content-Type', ''), 'application/json')) {
            $response->headers->set('Cache-Control', 'public, max-age=60, must-revalidate');
            // Add ETag for cache validation (only for JSON responses)
            $this->setETag($response);
        } elseif ($type === 'static') {
            // Static assets (images, CSS, JS) - cache for 1 year
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } else {
            // Default: no cache
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
        }

        return $response;
    }

    /**
     * Set ETag header for cache validation
     */
    protected function setETag(Response $response): void
    {
        $content = $response->getContent();
        if ($content) {
            $etag = md5($content);
            $response->setEtag($etag);
        }
    }
}

