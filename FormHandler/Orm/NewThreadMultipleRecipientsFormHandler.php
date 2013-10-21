<?php

namespace FOS\MessageBundle\FormHandler\Orm;

use FOS\MessageBundle\FormModel\NewThreadMultipleMessage;
use Symfony\Component\Form\Form;

/**
 * Description of NewThreadMultipleRecipientsFormHandler
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class NewThreadMultipleRecipientsFormHandler extends NewThreadFormHandler
{
    protected function createNewThreadMultipleRecipientsFromFormData(NewThreadMultipleMessage $message)
    {
        $this->newThreadBuilder->setCreatedAt(new \DateTime('now'));
        $this->newThreadBuilder->setSubject($message->getSubject());
        $this->newThreadBuilder->setBody($message->getBody());
        $this->newThreadBuilder->setRecipients(array($message->getRecipients()));
        $this->newThreadBuilder->setIsSpam(false);
        $this->newThreadBuilder->setSender($this->getAuthenticatedParticipant());

        return $this->newThreadBuilder->build();
    }

    public function createThreadObjectFromFormData(Form $form)
    {
        $this->createNewThreadMultipleRecipientsFromFormData($form->getData());
    }

}
