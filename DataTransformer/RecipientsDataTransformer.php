<?php
namespace Ornicar\MessageBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Form\DataTransformer\UserToUsernameTransformer;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
/**
 * Transforms collection of UserInterface into strings separated with coma
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class RecipientsDataTransformer implements DataTransformerInterface
{
    /**
     * @var \FOS\UserBundle\Form\DataTransformer\UserToUsernameTransformer
     */
    private $userToUsernameTransformer;

    /**
     * @param UserToUsernameTransformer $userToUsernameTransformer
     */
    public function __construct(UserToUsernameTransformer $userToUsernameTransformer)
    {
        $this->userToUsernameTransformer = $userToUsernameTransformer;
    }

    /**
     * Transforms a collection of recipients into a string
     *
     * @param Collection|null $recipients
     *
     * @return string
     */
    public function transform($recipients)
    {
        if (null === $recipients || $recipients->count() == 0) {
            return "";
        }

        $usernames = array();

        foreach ($recipients as $recipient) {
            $usernames[] = $this->userToUsernameTransformer->transform($recipient);
        }

        return implode(', ', $usernames);
    }

    /**
     * Transforms a string (usernames) to a Collection of UserInterface
     *
     * @param string $usernames
     *
     * @return array|null
     */
    public function reverseTransform($usernames)
    {
        if (null === $usernames || '' === $usernames) {
            return null;
        }

        if (!is_string($usernames)) {
            throw new UnexpectedTypeException($usernames, 'string');
        }

        $recipients = array();
        $transformer = $this->userToUsernameTransformer;
        $recipientsNames = array_filter(explode(', ', $usernames));

        foreach ($recipientsNames as $username) {
            $user = $this->userToUsernameTransformer->reverseTransform($username);

            if (!$user instanceof \Symfony\Component\Security\Core\User\UserInterface) {
                throw new TransformationFailedException(sprintf('User "%s" does not exists', $username));
            }

            $recipients[] = $user;
        }

        return $recipients;
    }
}
