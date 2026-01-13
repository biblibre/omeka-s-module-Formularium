<?php
namespace Formularium\FormComponentType;

use Formularium\FormComponent\FormComponent;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Stdlib\ErrorStore;
use Omeka\Stdlib\HtmlPurifier;

class Html extends AbstractFormComponentType
{
    public function __construct(protected HtmlPurifier $htmlPurifier)
    {
    }

    public function getLabel(): string
    {
        return 'HTML';
    }

    public function prepareForm(PhpRenderer $view): void
    {
        $view->headScript()->appendFile($view->assetUrl('vendor/ckeditor/ckeditor.js', 'Omeka'));
        $view->headScript()->appendFile($view->assetUrl('js/formularium-form-component-type-html.js', 'Formularium'));
        $view->headLink()->appendStylesheet($view->assetUrl('css/formularium-form-component-type-html.css', 'Formularium'));
    }

    public function settingsFieldsetAddElements(Fieldset $fieldset): void
    {
        parent::settingsFieldsetAddElements($fieldset);

        $fieldset->add([
            'name' => 'html',
            'type' => 'Laminas\Form\Element\Textarea',
            'options' => [
                'label' => 'HTML',
            ],
            'attributes' => [
                'class' => 'formularium-form-component-type-html',
            ],
        ]);
    }

    public function hydrateFormComponent(FormComponent $formComponent, ErrorStore $errorStore)
    {
        $html = $formComponent->getSetting('html');
        $formComponent->setSetting('html', $this->htmlPurifier->purify($html));
    }

    public function render(PhpRenderer $renderer, Form $form, FormComponent $formComponent, $data = null): string
    {
        return $formComponent->getSetting('html', '');
    }
}
