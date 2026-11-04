<?php

namespace ContatoSeguro\Tests\Factories;

class CategoryTranslationFactory extends Factory
{
    protected const TABLE = 'category_translation';

    protected static function defaults(): array
    {
        return ['lang_code' => 'pt', 'label' => 'categoria de teste'];
    }
}
