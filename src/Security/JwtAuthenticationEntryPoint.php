<?php
// src/Security/JwtAuthenticationEntryPoint.php
namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use App\Entity\User;

class JwtAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $hasHeader = $request->headers->has('Authorization');
        $headerValue = $request->headers->get('Authorization');

        error_log(sprintf(
            "JWT EntryPoint called | Path: %s | Has Authorization: %s | Header: %s | IP: %s",
            $request->getPathInfo(),
            $hasHeader ? 'YES' : 'NO',
            $headerValue ? substr($headerValue, 0, 50).'...' : 'NONE',
            $request->getClientIp()
        ));

        return new JsonResponse([
            'error' => 'Unauthorized',
            'message' => 'Token manquant ou invalide',
            'path' => $request->getPathInfo(),
            'has_header' => $hasHeader
        ], Response::HTTP_UNAUTHORIZED);
    }
}
