<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTrialStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si la ruta actual es 'logout', no hacemos nada y dejamos que continúe.
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && $user->hasRole('admin') && $user->empresa) {
            $empresa = $user->empresa;

            // Comprobamos si la prueba ha expirado
            $trialExpired = $empresa->subscription_status === 'trialing' && $empresa->trial_ends_at && $empresa->trial_ends_at->isPast();

            // Comprobamos si la suscripción ha sido cancelada o está vencida
            $subscriptionInactive = in_array($empresa->subscription_status, ['past_due', 'canceled']);

            if (($trialExpired || $subscriptionInactive) && !$request->routeIs('trial.expired')) {
                return redirect()->route('trial.expired');
            }
        }
        return $next($request);
    }
}