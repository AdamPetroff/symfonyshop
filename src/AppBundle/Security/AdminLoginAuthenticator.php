<?php
/**
 * Created by Adam The Great.
 * Date: 6. 1. 2017
 * Time: 22:20
 */

namespace AppBundle\Security;


use AppBundle\Form\AdminLoginType;
use AppBundle\Service\AdminManager;
use AppBundle\Utils\SecurityUtils;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
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

    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $this->router->generate('admin_login', [], RouterInterface::ABSOLUTE_URL) == $request->getUriForPath($request->getPathInfo()) && $request->getMethod() == 'POST';
        if(!$isLoginSubmit){
            return null;
        }
        $form = $this->formFactory->create(AdminLoginType::class);
        $form->handleRequest($request);

        $request->getSession()->set(SecurityUtils::LAST_ADMIN_USERNAME, $form->getData()['_username']);

        return $form->getData();
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['_username']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['_password'];

        return $this->adminManager->checkPassword($user, $password);
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('admin_login');
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('admin_index');
    }

}