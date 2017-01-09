<?php
/**
 * Created by Adam The Great.
 * Date: 7. 1. 2017
 * Time: 11:05
 */

namespace AppBundle\Utils;


use Symfony\Component\HttpFoundation\RequestStack;

class SecurityUtils
{
    const LAST_ADMIN_USERNAME = '_security.admin.last_username';
    const LAST_USER_USERNAME = '_security.user.last_username';
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return string
     */
    public function getLastAdminUsername(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request->attributes->has(self::LAST_ADMIN_USERNAME)) {
            return $request->attributes->get(self::LAST_ADMIN_USERNAME);
        } else {
            $session = $request->getSession();
            return null === $session ? '' : $session->get(self::LAST_ADMIN_USERNAME);
        }
    }

    /**
     * @return string
     */
    public function getLastUserUsername(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request->attributes->has(self::LAST_USER_USERNAME)) {
            return $request->attributes->get(self::LAST_USER_USERNAME);
        } else {
            $session = $request->getSession();
            return null === $session ? '' : $session->get(self::LAST_USER_USERNAME);
        }
    }
}