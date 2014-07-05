<?php

use Symfony\Component\HttpFoundation\Response;
use TobiasOlry\Talkly\Security\UserProvider\DebugUserProvider;
use TobiasOlry\Talkly\Security\UserProvider\NtlmUserProvider;
use TobiasOlry\Talkly\Security\Security;
use TobiasOlry\Talkly\Security\UserManager;
use TobiasOlry\Talkly\Twig\SecurityExtension;

use TobiasOlry\Talkly\Event\Subscriber\NotificationSubscriber;
use TobiasOlry\Talkly\Event\notificationtransport\EmailTransport;

$app['controllers']->before(
    function () use ($app) {
        $request      = $app['request'];
        $userProvider = $app['security.user_provider'];

        if (! $username = $userProvider->getUsername()) {
            return new Response('not allowed', 403);
        }

        $user = $app['security.usermanager']->findOrCreate($username);

        $request->server->set('PHP_AUTH_USER', $username);
        $app['security.token']->setUser($user);

        $app['dispatcher']->addSubscriber($app['service.eventsubscriber']);
    }
);


switch($app['config']['user_provider']) {
    case 'debug':
        $app['security.user_provider'] = $app->share(
            function () use ($app) {
                $user = isset($app['config']['user_provider_debug_user'])
                    ? $app['config']['user_provider_debug_user'] : 'mmustermann';

                return new DebugUserProvider($user);
            }
        );
        break;
    case 'ntlm':
        $app['security.user_provider'] = $app->share(
            function () use ($app) {
                return new NtlmUserProvider(
                    $app['request_stack'],
                    $app['config']['user_provider_ntlm_domain']
                );
            }
        );
        break;
}

$app['security.usermanager'] = $app->share(
    function () use ($app) {
        return new UserManager($app['orm.em']);
    }
);

$app['security.token'] = $app->share(
    function () use ($app) {
        return new Security();
    }
);

$app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
    $twig->addExtension(new SecurityExtension($app['security.token']));

    return $twig;
}));
