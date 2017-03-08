<?php

namespace WouterJ\EloquentBundle\Twig;

class SqlFormatterExtension extends \Twig_Extension
{
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
