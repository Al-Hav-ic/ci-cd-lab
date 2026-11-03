<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Фільтр доступу для POST (лаб. 5): партнерський клієнт / касовий термінал.
 * Логіка відповідає методичному прикладу CheckRequestIntegrity (X-App-Client, 403, X-Security-Policy).
 */
class CheckAppClient
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && ! $request->hasHeader('X-App-Client')) {
            return response()->json([
                'status' => 'access_denied',
                'message' => 'Відхилено: відсутній обов’язковий заголовок X-App-Client.',
            ], 403);
        }

        $response = $next($request);

        $response->headers->set('X-Security-Policy', 'Enforced');

        return $response;
    }
}
