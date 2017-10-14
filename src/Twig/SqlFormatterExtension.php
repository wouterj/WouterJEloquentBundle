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

/**
 * @private
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class SqlFormatterExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('wouterj_format_sql', [$this, 'formatSql'], ['is_safe' => ['html']]),
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
