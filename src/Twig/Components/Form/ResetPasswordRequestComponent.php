<?php

namespace App\Twig\Components\Form;

use App\Entity\ResetPasswordRequest;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('reset_password_request', template: 'components/form/reset_password_request.html.twig')]
final class ResetPasswordRequestComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public ?ResetPasswordRequest $resetPasswordRequest = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ResetPasswordRequestFormType::class, $this->resetPasswordRequest);
    }
}
