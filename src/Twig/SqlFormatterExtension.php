<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2017 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

if (!class_exists(AbstractExtension::class)) {
    class_alias('Twig_Environment', BaseExtension::class);
    class_alias('Twig_SimpleFilter', Filter::class);
} else {
    class_alias(AbstractExtension::class, BaseExtension::class);
    class_alias(TwigFilter::class, Filter::class);
}

/**
 * @private
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class SqlFormatterExtension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new Filter('wouterj_format_sql', [$this, 'formatSql'], ['is_safe' => ['html']]),
        ];
    }

    public function formatSql($sql)
    {
        \SqlFormatter::$use_pre = false;
        \SqlFormatter::$quote_attributes = 'class="symbol"';
        \SqlFormatter::$backtick_quote_attributes = 'class="backtick"';
        \SqlFormatter::$reserved_attributes = 'class="keyword"';
        \SqlFormatter::$boundary_attributes = 'class="symbol"';
        \SqlFormatter::$number_attributes = 'class="number"';
        \SqlFormatter::$word_attributes = 'class="word"';
        \SqlFormatter::$error_attributes = 'class="error"';
        \SqlFormatter::$comment_attributes = 'class="comment"';
        \SqlFormatter::$variable_attributes = 'class="variable"';

        return \SqlFormatter::highlight($sql);
    }
}
