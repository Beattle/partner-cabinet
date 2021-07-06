<?php

namespace App\Service\Serializer;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ExceptionNormalizer extends ObjectNormalizer
{
    const METADATA_TYPE_KEY = '~type';

    public function normalize($object, $format = null, array $context = [])
    {
        $result[self::METADATA_TYPE_KEY] = substr(strrchr(get_class($object), '\\'), 1);
        $result = array_merge($result, parent::normalize($object, $format, $context));

        return $result;
    }

    public function supportsNormalization($data, $format = null)
    {
        return \is_object($data) && $data instanceof \Throwable;
    }

    public function supportsDenormalization($data, $type, $format = null) {
        return false;
    }
}