<?php

namespace FOS\MessageBundle\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Transforms collection of UserInterface into strings separated with coma.
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class RecipientsDataTransformer implements DataTransformerInterface
{
    /**
     * @var DataTransformerInterface
     */
    private $userToUsernameTransformer;

    public function __construct(DataTransformerInterface $userToUsernameTransformer)
    {
        $this->userToUsernameTransformer = $userToUsernameTransformer;
    }

    /**
     * Transforms a collection of recipients into a string.
     *
     * @param Collection $recipients
     *
     * @return string
     */
    public function transform($recipients)
    {
        if (null === $recipients || 0 === $recipients->count()) {
            return '';
        }

        $usernames = array();

        foreach ($recipients as $recipient) {
            $usernames[] = $this->userToUsernameTransformer->transform($recipient);
        }

        return implode(', ', $usernames);
    }

    /**
     * Transforms a string (usernames) to a Collection of UserInterface.
     *
     * @param string $usernames
     *
     * @throws UnexpectedTypeException
     * @throws TransformationFailedException
     *
     * @return Collection $recipients
     */
    public function reverseTransform($usernames)
    {
        if (null === $usernames || '' === $usernames) {
            return null;
        }

        if (!is_string($usernames)) {
            throw new UnexpectedTypeException($usernames, 'string');
        }

        $recipients = new ArrayCollection();
        $recipientsNames = array_filter(explode(',', $usernames));

        foreach ($recipientsNames as $username) {
            $user = $this->userToUsernameTransformer->reverseTransform(trim($username));

            if (!$user instanceof UserInterface) {
                throw new TransformationFailedException(sprintf('User "%s" does not exists', $username));
            }

            $recipients->add($user);
        }

        return $recipients;
    }
}
