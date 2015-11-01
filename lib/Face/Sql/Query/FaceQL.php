<?php

namespace Face\Sql\Query;


use Face\Config;
use Face\Exception;
use Face\Exception\FQLParseException;
use Face\Parser\ParsingException;
use Face\Parser\RegexpLexer;
use Face\Parser\TokenNavigation;

class FaceQL
{

    protected $lexer;
    protected $faceConfig;

    const T_START_QUERY     = "T_START_QUERY";
    const T_IDENTIFIER      = "T_IDENTIFIER";
    const T_WHITESPACE      = "T_WHITESPACE";

    public function __construct(Config $config = null){

        if(null == $config){
            $this->faceConfig = Config::getDefault();
        }else{
            $this->faceConfig = $config;
        }

        $this->lexer = new RegexpLexer();
        $this->lexer->setTokens([

            "SELECT FROM"               => static::T_START_QUERY,
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

                throw new ParsingException("Unknown operation: " . $tokens->current()->getTokenValue());

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
        return $select;

    }

}
