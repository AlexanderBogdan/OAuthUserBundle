<?php
namespace Obogdan\OAuthUserBundle\Auth;


use Doctrine\Common\Persistence\ManagerRegistry;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use Obogdan\OAuthUserBundle\Entity\User;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuthProvider extends OAuthUserProvider
{
    protected $session;
    protected $doctrine;
    protected $container;

    /**
     * OAuthProvider constructor.
     *
     * @param SessionInterface $session
     * @param ManagerRegistry $doctrine
     * @param Container $service_container
     */
    public function __construct(SessionInterface $session, ManagerRegistry $doctrine, Container $service_container)
    {
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->container = $service_container;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->doctrine->getRepository('OAuthUserBundle:User')->loadUserByUsername($username);

        if (null === $user) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = $this->doctrine->getRepository('OAuthUserBundle:User')->findOneBy(['email' => $response->getEmail()]);

        if (null === $user) {
            $user = new User($response->getEmail());

            /**
             * Assign a role on e-mail basis.
             */
            switch ($response->getEmail()) {
                case 'admin@test.com' :
                    $role = 'ROLE_ADMIN';
                    break;
                case 'manager@test.com' :
                    $role = 'ROLE_MANAGER';
                    break;
                default:
                    $role = 'ROLE_WORKER';
            }
            $user->setRole($role);

            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword(md5(uniqid()), $user->getSalt());
            $user->setPassword($password);
            $user->setFirstName($response->getFirstName());
            $user->setLastName($response->getLastName());
        }

        $user->setLastLoginDate(new \DateTime());

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        $this->session->set('id', $user->getId());
        $this->session->set('email', $user->getEmail());

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s"', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Obogdan\\OAuthUserBundle\\Entity\\User';
    }
}
