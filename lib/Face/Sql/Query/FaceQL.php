<?php

namespace Face\Sql\Query;


use Face\Config;
use Face\Sql\Query\SelectBuilder;
use Face\Exception;
use Face\Exception\FQLParseException;
use Face\Parser\RegexpLexer;
use Face\Parser\TokenNavigation;

class FaceQL
{

    protected $lexer;
    protected $faceConfig;

    const T_START_QUERY     = "T_START_QUERY";
    const T_IDENTIFIER      = "T_IDENTIFIER";
    const T_WHITESPACE      = "T_WHITESPACE";
    const T_JOIN            = "T_JOIN";
    const T_LIMIT           = "T_LIMIT";
    const T_INTEGER         = "T_INTEGER";

    public function __construct(Config $config = null){

        if(null == $config){
            $this->faceConfig = Config::getDefault();
        }else{
            $this->faceConfig = $config;
        }

        $this->lexer = new RegexpLexer();
        $this->lexer->setTokens([

            "SELECT FROM"               => static::T_START_QUERY,
            "JOIN"                      => static::T_JOIN,
            "LIMIT"                     => static::T_LIMIT,
            "[0-9]+"                    => static::T_INTEGER,
            "[a-zA-Z][a-zA-Z0-9_.]*"    => static::T_IDENTIFIER,
            "\\s+"                      => static::T_WHITESPACE

        ]);

        $this->lexer->setCaseSensitive(false);
        $this->lexer->addIgnoredToken("T_WHITESPACE");
    }

    public function tokenize($source){
        return $this->lexer->tokenize($source);
    }

    public function parse($source){

        $tokens = new TokenNavigation($this->tokenize($source));
        $tokens->expectToBe(static::T_START_QUERY);

        switch ($tokens->current()->getTokenValue()){

            case "SELECT FROM":
                $tokens->next();
                return $this->parseSelect($tokens);
                break;

            default:

                throw new FQLParseException("Unknown operation: " . $tokens->current()->getTokenValue());

        }

    }

    private function parseSelect(TokenNavigation $tokens){
        $tokens->expectToBe(static::T_IDENTIFIER);
        $name = $tokens->getTokenValue();

        try{
            $face = $this->faceConfig->getFaceLoader()->getFaceForName($name);
        }catch(Exception $e){
            throw new FQLParseException("Unable to find entity $name in FROM clause", 0, $e);
        }
        $select = new SelectBuilder($face);

        $this->parseJoin($select, $tokens);

        $this->parseLimit($select, $tokens);

        if($tokens->hasNext()){
            throw new FQLParseException("Unexpected token " . $tokens->look()->getTokenName());
        }

        return $select;

    }

    private function parseJoin(SelectBuilder $selectBuilder, TokenNavigation $tokens){
        while ($tokens->hasNext() && $tokens->look(1)->is(static::T_JOIN)) {
            $tokens->next();
            $tokens->next();
            $tokens->expectToBe(static::T_IDENTIFIER);
            $name = $tokens->getTokenValue();

            try {
                $selectBuilder->join($name);
            } catch (Exception $e) {
                throw new FQLParseException("Unable to find parse $name in JOIN clause", 0, $e);
            }

        }
    }

    private function parseLimit(SelectBuilder $selectBuilder, TokenNavigation $tokens){
        if ($tokens->hasNext() && $tokens->look(1)->is(static::T_LIMIT)) {
            $tokens->next();
            $tokens->next();
            $tokens->expectToBe(static::T_INTEGER);
            // TODO: additional limit syntaxes "LIMIT 0,1" or  "LIMIT 0 OFFSET 1"
            $selectBuilder->limit($tokens->getTokenValue());
        }
    }

}
