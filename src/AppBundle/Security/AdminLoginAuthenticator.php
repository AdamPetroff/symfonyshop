<?php
/**
 * Created by Adam The Great.
 * Date: 6. 1. 2017
 * Time: 22:20
 */

namespace AppBundle\Security;


use AppBundle\Entity\Admin;
use AppBundle\Form\AdminLoginType;
use AppBundle\Service\AdminManager;
use AppBundle\Utils\SecurityUtils;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class AdminLoginAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var FormBuilder
     */
    private $formFactory;
    /**
     * @var AdminManager
     */
    private $adminManager;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(FormFactory $formFactory, AdminManager $adminManager, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->adminManager = $adminManager;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function getCredentials(Request $request): ?array
    {
        $isLoginSubmit = $this->router->generate('admin_login', [],
                RouterInterface::ABSOLUTE_URL) == $request->getUriForPath($request->getPathInfo()) && $request->getMethod() == 'POST';
        if (!$isLoginSubmit) {
            return null;
        }
        $form = $this->formFactory->create(AdminLoginType::class);
        $form->handleRequest($request);

        $request->getSession()->set(SecurityUtils::LAST_ADMIN_USERNAME, $form->getData()['_username']);

        return $form->getData();
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?Admin
    {
        return $userProvider->loadUserByUsername($credentials['_username']);
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $password = $credentials['_password'];

        return $this->adminManager->checkPassword($user, $password);
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->router->generate('admin_login');
    }

    /**
     * @return string
     */
    protected function getDefaultSuccessRedirectUrl(): string
    {
        return $this->router->generate('admin_index');
    }

    public function supportsRememberMe()
    {
        return true;
    }

}