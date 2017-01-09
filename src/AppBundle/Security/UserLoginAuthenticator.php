<?php
/**
 * Created by Adam The Great.
 * Date: 6. 1. 2017
 * Time: 22:20
 */

namespace AppBundle\Security;


use AppBundle\Entity\User;
use AppBundle\Form\UserLoginType;
use AppBundle\Service\AdminManager;
use AppBundle\Service\UserManager;
use AppBundle\Utils\SecurityUtils;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class UserLoginAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var FormBuilder
     */
    private $formFactory;
    /**
     * @var AdminManager
     */
    private $userManager;
    /**
     * @var RouterInterface
     */
    private $router;


    public function __construct(FormFactory $formFactory, UserManager $userManager, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function getCredentials(Request $request): ?array
    {
        $isLoginSubmit = $this->router->generate(
                'front_login', 
                [],
                RouterInterface::ABSOLUTE_URL) == $request->getUriForPath($request->getPathInfo()) && $request->getMethod() == 'POST';
        if (!$isLoginSubmit) {
            return null;
        }
        $form = $this->formFactory->create(UserLoginType::class);
        $form->handleRequest($request);

        $request->getSession()->set(SecurityUtils::LAST_USER_USERNAME, $form->getData()['_username']);

        return $form->getData();
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        return $this->userManager->findByUsername($credentials['_username']);
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $password = $credentials['_password'];

        return $this->userManager->checkPassword($user, $password);
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->router->generate('front_login');
    }

    /**
     * @return string
     */
    protected function getDefaultSuccessRedirectUrl(): string
    {
        return $this->router->generate('front_homepage');
    }

    public function supportsRememberMe()
    {
        return true;
    }


}