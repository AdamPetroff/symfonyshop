<?php

namespace AppBundle\DataTransformers;

use Symfony\Component\Form\DataTransformerInterface;

class EntityToChiceTransformer implements DataTransformerInterface
{
	public function transform($value){
		BaseRepository::getKeyValue($value, 'id')
	}

	public function reverseTransform($value){

	}
}
