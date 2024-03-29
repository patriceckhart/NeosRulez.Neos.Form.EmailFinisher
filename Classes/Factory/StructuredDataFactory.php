<?php
namespace NeosRulez\Neos\Form\EmailFinisher\Factory;

use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\Image;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Form\Core\Model\FormDefinition;

class StructuredDataFactory
{

    /**
     * @param array $formValues
     * @param FormDefinition $formDefinition
     * @return string
     */
    public function getCsvString(array $formValues, FormDefinition $formDefinition): string
    {
        $csvFile = fopen('php://memory', 'w');
        foreach ($formValues as $formValueKey => $formValue) {
            if($formValue instanceof Image) {
                $formValue = $formValue->getResource()->getFilename();
            } else if($formValue instanceof PersistentResource) {
                $formValue = $formValue->getFilename();
            } else if(is_array($formValue)) {
                $formValue = implode(',', $formValue);
            } else if($formValue instanceof \DateTime) {
                $formValue = $formValue->format('Y-m-d H:i:s');
            }
            fputcsv($csvFile, [$formDefinition->getElementByIdentifier($formValueKey)->getProperty('label'), $formValue]);
        }
        rewind($csvFile);
        $csvContent = stream_get_contents($csvFile);
        fclose($csvFile);
        return $csvContent;
    }

}
